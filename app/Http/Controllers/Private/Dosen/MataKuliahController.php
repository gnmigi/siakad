<?php

namespace App\Http\Controllers\Private\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Auth;
// USE MODELS
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\MataKuliah;

class MataKuliahController extends Controller
{
    public function renderMataKuliah()
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Mata Kuliah";
        $data['pages'] = "Daftar Mata Kuliah";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get mata kuliah where current dosen is assigned as dosen1, dosen2, or dosen3
        $mataKuliah = MataKuliah::with(['programStudi', 'kurikulum', 'dosen1', 'dosen2', 'dosen3'])
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $data['mataKuliah'] = $mataKuliah;

        return view('central.backpage.dosen-mata-kuliah', $data, compact('user'));
    }
}
