<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use App\Models\PMB\JalurPendaftaran;
use Illuminate\Support\Facades\Validator;

class JalurPendaftaranController extends Controller
{
    public function renderJalur()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Jalur Pendaftaran';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['jalurs'] = JalurPendaftaran::orderBy('created_at', 'desc')->get();
        
        return view('master.pmb.jalur-index', $data);
    }
    
    public function handleJalur(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_id' => 'required|exists:periode_pendaftarans,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        JalurPendaftaran::create([
            'periode_id' => $request->periode_id,
            'name' => $request->name,
            'desc' => $request->description,
            'code' => 'JALUR_' . uniqid(),
            'status' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Jalur pendaftaran berhasil ditambahkan');
    }
    
    public function updateJalur(Request $request, $code)
    {
        $jalur = JalurPendaftaran::where('code', $code)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'periode_id' => 'required|exists:periode_pendaftarans,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $jalur->update([
            'periode_id' => $request->periode_id,
            'name' => $request->name,
            'desc' => $request->description,
            'status' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Jalur pendaftaran berhasil diperbarui');
    }
    
    public function deleteJalur($code)
    {
        $jalur = JalurPendaftaran::where('code', $code)->firstOrFail();
        $jalur->delete();
        
        return redirect()->back()->with('success', 'Jalur pendaftaran berhasil dihapus');
    }
}
