<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use App\Models\PMB\PeriodePendaftaran;
use Illuminate\Support\Facades\Validator;

class PeriodePendaftaranController extends Controller
{
    public function renderPeriode()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Periode Pendaftaran';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['periodes'] = PeriodePendaftaran::orderBy('created_at', 'desc')->get();
        
        return view('master.pmb.periode-index', $data);
    }
    
    public function handlePeriode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PeriodePendaftaran::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Periode pendaftaran berhasil ditambahkan');
    }
    
    public function updatePeriode(Request $request, $code)
    {
        $periode = PeriodePendaftaran::where('code', $code)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $periode->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Periode pendaftaran berhasil diperbarui');
    }
    
    public function deletePeriode($code)
    {
        $periode = PeriodePendaftaran::where('code', $code)->firstOrFail();
        $periode->delete();
        
        return redirect()->back()->with('success', 'Periode pendaftaran berhasil dihapus');
    }
}
