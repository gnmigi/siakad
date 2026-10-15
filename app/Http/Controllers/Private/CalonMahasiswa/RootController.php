<?php

namespace App\Http\Controllers\Private\CalonMahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PMB\Pendaftar;
use App\Models\PMB\PMBRequirement;
use App\Models\PMB\PMBRequirementUpload;
use App\Models\Pengaturan\WebSetting;

class RootController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.pendaftar');
    }

    public function renderDashboard()
    {
        $user = Auth::guard('pendaftar')->user();
        
        if (!$user) {
            return redirect()->route('auth.render-signin');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'camaba.';
        $data['menus'] = "Dashboard";
        $data['pages'] = "Dashboard Calon Mahasiswa";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Load relationships
        $user->load(['periode', 'jalur', 'prodi1', 'prodi2', 'prodi3']);
        
        // Check if user has been converted to mahasiswa
        $mahasiswaData = null;
        if ($user->mahasiswa_id && $user->mahasiswa_id > 0) {
            $mahasiswaData = \App\Models\Mahasantri::with(['prodi', 'kelas'])
                ->find($user->mahasiswa_id);
        }
        
        // Get requirements and uploads for dashboard
        $requirements = collect();
        $uploads = collect();
        
        try {
            // Get all active requirements
            $requirements = PMBRequirement::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->take(5) // Limit to 5 for dashboard display
                ->get();
            
            // Get user's uploads
            $uploads = PMBRequirementUpload::where('pendaftar_id', $user->id)
                ->get()
                ->keyBy('requirement_id');
        } catch (\Exception $e) {
            // Log error but continue with empty collections
            \Log::error('Error loading PMB requirements for dashboard: ' . $e->getMessage());
        }
        
        return view('central.backpage.dashboard-calon-mahasiswa', $data, compact('user', 'mahasiswaData', 'requirements', 'uploads'));
    }

    public function renderProfile()
    {
        $user = Auth::guard('pendaftar')->user();
        
        if (!$user) {
            return redirect()->route('auth.render-signin');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'camaba.';
        $data['menus'] = "Profile";
        $data['pages'] = "Profile Calon Mahasiswa";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        return view('central.backpage.profile-calon-mahasiswa', $data, compact('user'));
    }

    public function handleProfile(Request $request)
    {
        $user = Auth::guard('pendaftar')->user();
        
        if (!$user) {
            return redirect()->route('auth.render-signin');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pendaftars,email,' . $user->id,
            'phone' => 'required|string|unique:pendaftars,phone,' . $user->id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'alamat' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'asal_sekolah' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer',
            'nik' => 'required|string|size:16|unique:pendaftars,nik,' . $user->id,
            'nisn' => 'nullable|string',
            'pekerjaan_ayah' => 'nullable|string',
            'pekerjaan_ibu' => 'nullable|string',
            'no_hp_ortu' => 'nullable|string',
        ]);

        try {
            $data = $request->except(['current_password', 'password', 'password_confirmation']);
            
            // Handle password change
            if ($request->filled('current_password') && $request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()
                        ->with('error', 'Password lama tidak sesuai')
                        ->withInput();
                }
                $data['password'] = Hash::make($request->password);
            }
            
            $user->update($data);

            return redirect()->back()->with('success', 'Profile berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profile: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function requirementsRender()
    {
        try {
            $user = Auth::guard('pendaftar')->user();
            
            if (!$user) {
                return redirect()->route('auth.render-signin');
            }
            
            $webs = WebSetting::first();
            
            // Get requirements for this user's periode with error handling
            $requirements = collect();
            $uploads = collect();
            
            try {
                // Get all active requirements (tidak perlu filter periode_id karena tabel tidak memiliki kolom tersebut)
                $requirements = PMBRequirement::where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('name', 'asc')
                    ->get();
                
                // Get user's uploads
                $uploads = PMBRequirementUpload::where('pendaftar_id', $user->id)
                    ->get()
                    ->keyBy('requirement_id');
            } catch (\Exception $e) {
                // Log error but continue with empty collections
                \Log::error('Error loading PMB requirements: ' . $e->getMessage());
            }
            
            $data['webs'] = $webs;
            $data['spref'] = 'camaba.';
            $data['menus'] = "Syarat Pendaftaran";
            $data['pages'] = "Syarat Pendaftaran";
            $data['academy'] = $webs->school_apps . ' oleh ' . $webs->school_name;
            
            return view('central.backpage.requirements-calon-mahasiswa', $data, compact('user', 'requirements', 'uploads'));
        } catch (\Exception $e) {
            \Log::error('Error in requirementsRender: ' . $e->getMessage());
            return redirect()->route('camaba.dashboard-render')->with('error', 'Terjadi kesalahan saat memuat halaman syarat pendaftaran.');
        }
    }

    public function requirementsUpload(Request $request)
    {
        $user = Auth::guard('pendaftar')->user();
        
        if (!$user) {
            return redirect()->route('auth.render-signin');
        }
        
        $request->validate([
            'requirement_id' => 'required|exists:pmb_requirements,id',
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        try {
            $requirement = PMBRequirement::findOrFail($request->requirement_id);
            
            // Check if user already uploaded for this requirement
            $existingUpload = PMBRequirementUpload::where('pendaftar_id', $user->id)
                ->where('requirement_id', $requirement->id)
                ->first();
            
            // Delete old file if exists
            if ($existingUpload && $existingUpload->file_path && Storage::exists('public/' . $existingUpload->file_path)) {
                Storage::delete('public/' . $existingUpload->file_path);
            }
            
            // Store new file
            $filePath = $request->file('file')->store('pmb/requirements/' . $user->id, 'public');
            
            // Update or create upload record
            PMBRequirementUpload::updateOrCreate(
                [
                    'pendaftar_id' => $user->id,
                    'requirement_id' => $requirement->id,
                ],
                [
                    'file_path' => $filePath,
                    'original_name' => $request->file('file')->getClientOriginalName(),
                    'file_size' => $request->file('file')->getSize(),
                    'status' => 'pending',
                    'uploaded_at' => now(),
                ]
            );
            
            return redirect()->back()->with('success', 'File berhasil diupload!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
