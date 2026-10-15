<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use RealRashid\SweetAlert\Facades\Alert;

class GraduationSettingsController extends Controller
{
    public function index()
    {
        $data['webs'] = WebSetting::first();
        
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $data['spref'] = $prefix . '.';
        
        $data['menus'] = "Master Akademik";
        $data['pages'] = "Pengaturan Kelulusan";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get current graduation settings or set defaults
        $data['settings'] = $data['webs'] ?? new WebSetting();
        
        return view('master.akademik.graduation-settings-index', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'min_gpa_graduation' => 'required|numeric|min:0|max:4',
            'min_credits_graduation' => 'required|integer|min:1|max:300',
        ], [
            'min_gpa_graduation.required' => 'IPK minimal kelulusan wajib diisi',
            'min_gpa_graduation.numeric' => 'IPK minimal kelulusan harus berupa angka',
            'min_gpa_graduation.min' => 'IPK minimal kelulusan tidak boleh kurang dari 0',
            'min_gpa_graduation.max' => 'IPK minimal kelulusan tidak boleh lebih dari 4',
            'min_credits_graduation.required' => 'SKS minimal kelulusan wajib diisi',
            'min_credits_graduation.integer' => 'SKS minimal kelulusan harus berupa angka bulat',
            'min_credits_graduation.min' => 'SKS minimal kelulusan minimal 1',
            'min_credits_graduation.max' => 'SKS minimal kelulusan maksimal 300',
        ]);

        try {
            $webSetting = WebSetting::first();
            
            if (!$webSetting) {
                $webSetting = new WebSetting();
            }

            $webSetting->min_gpa_graduation = $request->min_gpa_graduation;
            $webSetting->min_credits_graduation = $request->min_credits_graduation;
            $webSetting->save();

            Alert::success('Berhasil', 'Pengaturan kelulusan berhasil diperbarui');
            
            // Determine the correct redirect route based on current route
            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            return redirect()->route($prefix . '.akademik.graduation-settings-render');

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
