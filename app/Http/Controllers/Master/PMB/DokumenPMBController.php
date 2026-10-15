<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;

class DokumenPMBController extends Controller
{
    public function index()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'master.';
        $data['menus'] = 'PMB';
        $data['pages'] = 'DokumenPMB';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        return view('master.pmb.dokumenpmb-index', $data);
    }
    
    public function store(Request $request)
    {
        // Implementation needed
        return redirect()->back()->with('success', 'Data berhasil disimpan');
    }
    
    public function update(Request $request, $id)
    {
        // Implementation needed
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        // Implementation needed
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
