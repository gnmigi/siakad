<?php

namespace App\Http\Controllers\Private\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function renderAbsensi()
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Absensi";
        $data['pages'] = "Absensi Dosen";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Cek apakah tabelnya sudah ada di database biar tidak crash
        $absensi = collect(); 
        
        if (Schema::hasTable('absensis')) {
            $absensi = DB::table('absensis')
                        ->where('user_id', $user->id)
                        ->whereDate('date', today())
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $data['absensi'] = $absensi;

        return view('central.backpage.absen-index', $data, compact('user'));
    }

    public function handleAbsensi(Request $request)
    {
        try {
            $user = Auth::guard('dosen')->user();

            // Validasi input
            $request->validate([
                'type' => 'required',
                'date' => 'required',
                'time_in' => 'required',
                'photo_in' => 'required|image|max:8192', // Max 8MB
            ]);

            // Proses Upload Foto Masuk (Tanpa Intervention untuk Dosen, simpan normal)
            $filename = null;
            if ($request->hasFile('photo_in')) {
                $file = $request->file('photo_in');
                $filename = 'absensi_in_' . $user->id . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/images/absensi', $filename);
            }

            // Konversi Format Tanggal agar diterima MySQL
            $tanggalDB = Carbon::parse($request->date)->format('Y-m-d');

            // Simpan ke Database
            DB::table('absensis')->insert([
                'type' => $request->type,
                'date' => $tanggalDB,
                'time_in' => $request->time_in,
                'photo_in' => $filename,
                'desc' => $request->desc,
                'code' => 'ABS-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => $user->id,
                'status' => 0, // 0 = Auto Approve (sesuaikan jika dosen butuh approval)
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Berhasil melakukan Absen Masuk!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal absen: ' . $e->getMessage());
        }
    }

    public function updateAbsensi(Request $request, $code)
    {
        try {
            $user = Auth::guard('dosen')->user();

            $request->validate([
                'time_out' => 'required',
                'photo_out' => 'required|image|max:8192',
            ]);

            // Proses Upload Foto Pulang
            $filename = null;
            if ($request->hasFile('photo_out')) {
                $file = $request->file('photo_out');
                $filename = 'absensi_out_' . $user->id . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/images/absensi', $filename);
            }

            // Update database untuk Absen Pulang
            DB::table('absensis')
                ->where('code', $code)
                ->where('user_id', $user->id)
                ->update([
                    'time_out' => $request->time_out,
                    'photo_out' => $filename,
                    'updated_at' => now(),
                ]);

            return redirect()->back()->with('success', 'Berhasil melakukan Absen Pulang!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal absen pulang: ' . $e->getMessage());
        }
    }
}