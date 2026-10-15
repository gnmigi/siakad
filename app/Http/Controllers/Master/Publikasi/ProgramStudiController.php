<?php

namespace App\Http\Controllers\Master\Publikasi;

use App\Http\Controllers\Controller;
use App\Models\Publikasi\ProgramStudi;
use App\Models\Publikasi\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::with('kategori')
            ->latest()
            ->paginate(10);

        $webs = \App\Models\Pengaturan\WebSetting::first();
        $user = Auth::user();

        $data = [
            'programStudis' => $programStudis,
            'pages' => 'Program Studi',
            'menus' => 'Master Publikasi',
            'academy' => config('app.name', 'SIAKAD'),
            'webs' => $webs,
            'user' => $user,
            'spref' => $user ? $user->prefix : ''
        ];

        return view('master.publikasi.program-studi-index', $data);
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $webs = \App\Models\Pengaturan\WebSetting::first();
        $user = Auth::user();
        
        $data = [
            'kategoris' => $kategoris,
            'pages' => 'Tambah Program Studi',
            'menus' => 'Master Publikasi',
            'academy' => config('app.name', 'SIAKAD'),
            'webs' => $webs,
            'user' => $user,
            'spref' => $user ? $user->prefix : ''
        ];
        return view('master.publikasi.program-studi-create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();
        $data['code'] = 'PS-' . strtoupper(Str::random(8));

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists('images/program-studi')) {
                Storage::disk('public')->makeDirectory('images/program-studi');
            }
            
            $photo->storeAs('images/program-studi', $filename, 'public');
            $data['photo'] = $filename;
        }

        ProgramStudi::create($data);

        Alert::success('Berhasil', 'Program Studi berhasil ditambahkan');
        return redirect()->route('master.publikasi.program-studi.index');
    }

    public function show(ProgramStudi $programStudi)
    {
        $programStudi->load('kategori');
        $webs = \App\Models\Pengaturan\WebSetting::first();
        $user = Auth::user();
        
        $data = [
            'programStudi' => $programStudi,
            'pages' => 'Detail Program Studi',
            'menus' => 'Master Publikasi',
            'academy' => config('app.name', 'SIAKAD'),
            'webs' => $webs,
            'user' => $user,
            'spref' => $user ? $user->prefix : ''
        ];
        
        return view('master.publikasi.program-studi-view', $data);
    }

    public function edit(ProgramStudi $programStudi)
    {
        $kategoris = Kategori::all();
        $webs = \App\Models\Pengaturan\WebSetting::first();
        $user = Auth::user();
        
        $data = [
            'programStudi' => $programStudi,
            'kategoris' => $kategoris,
            'pages' => 'Edit Program Studi',
            'menus' => 'Master Publikasi',
            'academy' => config('app.name', 'SIAKAD'),
            'webs' => $webs,
            'user' => $user,
            'spref' => $user ? $user->prefix : ''
        ];
        
        return view('master.publikasi.program-studi-edit', $data);
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($programStudi->photo && Storage::disk('public')->exists('images/program-studi/' . $programStudi->photo)) {
                Storage::disk('public')->delete('images/program-studi/' . $programStudi->photo);
            }

            $photo = $request->file('photo');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists('images/program-studi')) {
                Storage::disk('public')->makeDirectory('images/program-studi');
            }
            
            $photo->storeAs('images/program-studi', $filename, 'public');
            $data['photo'] = $filename;
        }

        $programStudi->update($data);

        Alert::success('Berhasil', 'Program Studi berhasil diperbarui');
        return redirect()->route('master.publikasi.program-studi.index');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        // Delete photo if exists
        if ($programStudi->photo && Storage::disk('public')->exists('images/program-studi/' . $programStudi->photo)) {
            Storage::disk('public')->delete('images/program-studi/' . $programStudi->photo);
        }

        $programStudi->delete();

        Alert::success('Berhasil', 'Program Studi berhasil dihapus');
        return redirect()->route('master.publikasi.program-studi.index');
    }

    public function toggleStatus(ProgramStudi $programStudi)
    {
        $programStudi->update([
            'status' => $programStudi->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $programStudi->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        Alert::success('Berhasil', "Program Studi berhasil {$status}");
        
        return redirect()->route('master.publikasi.program-studi.index');
    }
}
