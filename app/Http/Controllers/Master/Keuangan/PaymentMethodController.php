<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['menus'] = "Master";
        $data['pages'] = "Metode Pembayaran";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['spref'] = $user ? $user->prefix : '';
        
        $data['paymentMethods'] = PaymentMethod::orderBy('sort_order')->get();
        
        return view('master.keuangan.payment-method-index', $data, compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank_transfer,e_wallet,virtual_account,credit_card,cash',
            'provider' => 'required|string|max:255',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fee_fixed' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            PaymentMethod::create([
                'code' => strtoupper(Str::slug($request->name, '_')),
                'name' => $request->name,
                'type' => $request->type,
                'provider' => $request->provider,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'auto_verify' => $request->has('auto_verify'),
                'fee_percentage' => $request->fee_percentage ?? 0,
                'fee_fixed' => $request->fee_fixed ?? 0,
                'sort_order' => $request->sort_order ?? 0,
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Metode pembayaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan metode pembayaran: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank_transfer,e_wallet,virtual_account,credit_card,cash',
            'provider' => 'required|string|max:255',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fee_fixed' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();
            
            $paymentMethod->update([
                'name' => $request->name,
                'type' => $request->type,
                'provider' => $request->provider,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'auto_verify' => $request->has('auto_verify'),
                'fee_percentage' => $request->fee_percentage ?? 0,
                'fee_fixed' => $request->fee_fixed ?? 0,
                'sort_order' => $request->sort_order ?? 0,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Metode pembayaran berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui metode pembayaran: ' . $e->getMessage());
        }
    }

    public function destroy($code)
    {
        try {
            $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();
            
            // Check if payment method is being used
            if ($paymentMethod->paymentTransactions()->exists()) {
                return redirect()->back()->with('error', 'Metode pembayaran tidak dapat dihapus karena sedang digunakan');
            }
            
            $paymentMethod->update(['deleted_by' => Auth::id()]);
            $paymentMethod->delete();

            return redirect()->back()->with('success', 'Metode pembayaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus metode pembayaran: ' . $e->getMessage());
        }
    }

    public function toggleStatus($code)
    {
        try {
            $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();
            
            $paymentMethod->update([
                'is_active' => !$paymentMethod->is_active,
                'updated_by' => Auth::id(),
            ]);

            $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Metode pembayaran berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
