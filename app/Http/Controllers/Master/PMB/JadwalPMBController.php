<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;

class JadwalPMBController extends Controller
{
    public function renderJadwal()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Jadwal PMB';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        return view('master.pmb.jadwal-index', $data);
    }
    
    public function handleJadwal(Request $request)
    {
        return redirect()->back()->with('success', 'Jadwal PMB berhasil ditambahkan');
    }
    
    public function updateJadwal(Request $request, $code)
    {
        return redirect()->back()->with('success', 'Jadwal PMB berhasil diperbarui');
    }
    
    public function deleteJadwal($code)
    {
        return redirect()->back()->with('success', 'Jadwal PMB berhasil dihapus');
    }
}
