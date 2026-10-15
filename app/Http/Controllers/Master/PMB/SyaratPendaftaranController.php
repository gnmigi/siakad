<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use App\Models\PMB\PMBRequirement;
use App\Models\PMB\PMBRequirementUpload;
use App\Models\PMB\PeriodePendaftaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SyaratPendaftaranController extends Controller
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
        $data['menus'] = 'PMB';
        $data['pages'] = 'Syarat Pendaftaran';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Get all requirements with upload counts
        $data['requirements'] = PMBRequirement::withCount(['uploads', 'uploads as pending_uploads_count' => function($query) {
            $query->where('status', 'pending');
        }, 'uploads as approved_uploads_count' => function($query) {
            $query->where('status', 'approved');
        }])->orderBy('sort_order')->orderBy('name')->get();
        
        return view('master.pmb.syarat-index', $data);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,image,document',
            'allowed_extensions' => 'required|string',
            'max_size_mb' => 'required|integer|min:1|max:50',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PMBRequirement::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'allowed_extensions' => $request->allowed_extensions,
            'max_size_mb' => $request->max_size_mb,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active') ?: true, // Default active if not checked
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->back()->with('success', 'Syarat pendaftaran berhasil ditambahkan');
    }
    
    public function update(Request $request, $id)
    {
        $requirement = PMBRequirement::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,image,document',
            'allowed_extensions' => 'required|string',
            'max_size_mb' => 'required|integer|min:1|max:50',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $requirement->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'allowed_extensions' => $request->allowed_extensions,
            'max_size_mb' => $request->max_size_mb,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->back()->with('success', 'Syarat pendaftaran berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $requirement = PMBRequirement::findOrFail($id);
        
        // Delete associated uploads and files
        $uploads = $requirement->uploads;
        foreach ($uploads as $upload) {
            if (Storage::exists($upload->file_path)) {
                Storage::delete($upload->file_path);
            }
        }
        
        $requirement->delete();
        
        return redirect()->back()->with('success', 'Syarat pendaftaran berhasil dihapus');
    }
    
    public function uploads()
    {
        $data['webs'] = WebSetting::first();
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $data['spref'] = $prefix . '.';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Review Upload Dokumen';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Get all uploads with relationships
        $data['uploads'] = PMBRequirementUpload::with(['requirement', 'pendaftar'])
            ->orderBy('uploaded_at', 'desc')
            ->paginate(20);
            
        return view('master.pmb.syarat-uploads', $data);
    }
    
    public function approveUpload(Request $request, $id)
    {
        $upload = PMBRequirementUpload::findOrFail($id);
        
        $upload->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->back()->with('success', 'Upload dokumen berhasil disetujui');
    }
    
    public function rejectUpload(Request $request, $id)
    {
        $upload = PMBRequirementUpload::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $upload->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->back()->with('success', 'Upload dokumen berhasil ditolak');
    }
    
    public function toggleActive($id)
    {
        $requirement = PMBRequirement::findOrFail($id);
        $requirement->update(['is_active' => !$requirement->is_active]);
        
        $status = $requirement->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Syarat pendaftaran berhasil {$status}");
    }
}
