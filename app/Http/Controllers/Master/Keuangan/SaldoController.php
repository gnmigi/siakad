<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// Use Models
use App\Models\Keuangan\Saldo;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\RiwayatPembayaran;
use App\Models\Pengaturan\WebSetting;
// Use Plugins

class SaldoController extends Controller
{
    public function renderSaldo()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Saldo";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Get all saldo records with enhanced statistics
        $data['saldos'] = Saldo::with([
                'tagihan' => function($q) { $q->select('id', 'mahasiswa_id', 'code', 'desc'); },
                'tagihan.mahasiswa' => function($q) { $q->select('id', 'name', 'numb_nim'); },
                'createdBy' => function($q) { $q->select('id', 'name'); }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate comprehensive statistics
        $globalStats = Saldo::where('status', 'Sukses')
            ->selectRaw('SUM(CASE WHEN type = "Pemasukan" THEN amount ELSE 0 END) as total_pemasukan')
            ->selectRaw('SUM(CASE WHEN type = "Pengeluaran" THEN amount ELSE 0 END) as total_pengeluaran')
            ->first();

        $todayStats = Saldo::where('status', 'Sukses')
            ->whereDate('created_at', Carbon::today('Asia/Jakarta'))
            ->selectRaw('COUNT(*) as total_transaksi')
            ->selectRaw('SUM(CASE WHEN type = "Pemasukan" THEN amount ELSE 0 END) as pemasukan_hari_ini')
            ->selectRaw('SUM(CASE WHEN type = "Pengeluaran" THEN amount ELSE 0 END) as pengeluaran_hari_ini')
            ->first();

        $data['statistics'] = [
            'total_pemasukan' => $globalStats->total_pemasukan ?? 0,
            'total_pengeluaran' => $globalStats->total_pengeluaran ?? 0,
            'saldo_bersih' => ($globalStats->total_pemasukan ?? 0) - ($globalStats->total_pengeluaran ?? 0),
            'total_transaksi_hari_ini' => $todayStats->total_transaksi ?? 0,
            'pemasukan_hari_ini' => $todayStats->pemasukan_hari_ini ?? 0,
            'pengeluaran_hari_ini' => $todayStats->pengeluaran_hari_ini ?? 0,
            'total_pembayaran_mahasiswa' => PaymentTransaction::where('is_manual', true)
                ->where('status', 'verified')
                ->sum('paid_amount'),
            'pembayaran_mahasiswa_bulan_ini' => PaymentTransaction::where('is_manual', true)
                ->where('status', 'verified')
                ->whereMonth('created_at', Carbon::now('Asia/Jakarta')->month)
                ->whereYear('created_at', Carbon::now('Asia/Jakarta')->year)
                ->sum('paid_amount'),
            'jumlah_mahasiswa_bayar_hari_ini' => PaymentTransaction::where('is_manual', true)
                ->where('status', 'verified')
                ->whereDate('created_at', Carbon::today('Asia/Jakarta'))
                ->distinct('mahasiswa_id')
                ->count('mahasiswa_id')
        ];
        
        // Recent transactions for dashboard
        $data['recent_transactions'] = Saldo::with([
                'tagihan' => function($q) { $q->select('id', 'mahasiswa_id', 'code', 'desc'); },
                'tagihan.mahasiswa' => function($q) { $q->select('id', 'name', 'numb_nim'); },
                'createdBy' => function($q) { $q->select('id', 'name'); }
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('master.keuangan.saldo-index', $data, compact('user'));
    }

    public function handleSaldo(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'type' => 'required|in:Pemasukan,Pengeluaran',
                'amount' => 'required|numeric|min:0',
                'source' => 'required|string|max:255',
                'desc' => 'nullable|string',
                'tagihan_id' => 'nullable|exists:tagihan_kuliahs,id',
                'transaction_code' => 'required|string|max:255',
            ]);

            // Generate unique codes
            $code = 'SAL-' . Str::random(8);
            $transactionCode = $request->transaction_code ?? 'TRX-' . Str::random(12);
            
            // Create new Saldo
            Saldo::create([
                'code' => $code,
                'type' => $request->type,
                'status' => 'Pending',
                'amount' => $request->amount,
                'transaction_code' => $transactionCode,
                'source' => $request->source,
                'desc' => $request->desc,
                'tagihan_id' => $request->tagihan_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Saldo berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Saldo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateSaldo(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'type' => 'required|in:Pemasukan,Pengeluaran',
                'amount' => 'required|numeric|min:0',
                'source' => 'required|string|max:255',
                'desc' => 'nullable|string',
                'status' => 'required|in:Pending,Sukses,Gagal',
                'tagihan_id' => 'nullable|exists:tagihan_kuliahs,id',
                'transaction_code' => 'required|string|max:255',
            ]);

            $saldo = Saldo::where('code', $code)->firstOrFail();

            $saldo->update([
                'type' => $request->type,
                'amount' => $request->amount,
                'source' => $request->source,
                'desc' => $request->desc,
                'status' => $request->status,
                'tagihan_id' => $request->tagihan_id,
                'transaction_code' => $request->transaction_code,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Saldo berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Saldo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteSaldo($code)
    {
        try {
            DB::beginTransaction();

            $saldo = Saldo::where('code', $code)->firstOrFail();

            // Check if saldo is already processed
            if ($saldo->status !== 'Pending') {
                return redirect()->back()->with('error', 'Tidak dapat menghapus Saldo yang sudah diproses');
            }

            $saldo->update([
                'deleted_by' => Auth::id()
            ]);
            $saldo->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Saldo berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Saldo: ' . $e->getMessage());
        }
    }
}
