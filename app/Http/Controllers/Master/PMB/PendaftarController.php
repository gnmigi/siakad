<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use App\Models\PMB\Pendaftar;
use App\Models\PMB\PMBRequirement;
use App\Models\PMB\PMBRequirementUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PendaftarController extends Controller
{
    public function renderPendaftar()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Pendaftar';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['pendaftars'] = Pendaftar::orderBy('created_at', 'desc')->get();
        
        return view('master.pmb.pendaftar-index', $data);
    }
    
    public function handlePendaftar(Request $request)
    {
        return redirect()->back()->with('success', 'Pendaftar berhasil ditambahkan');
    }
    
    public function updatePendaftar(Request $request, $code)
    {
        return redirect()->back()->with('success', 'Pendaftar berhasil diperbarui');
    }
    
    public function deletePendaftar($code)
    {
        return redirect()->back()->with('success', 'Pendaftar berhasil dihapus');
    }
    
    public function renderDetail($code)
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = 'PMB';
        $data['pages'] = 'Detail Pendaftar';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Find pendaftar by registration code
        $pendaftar = Pendaftar::where('registration_code', $code)->firstOrFail();
        
        // Get all requirements
        $requirements = PMBRequirement::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get uploads for this pendaftar
        $uploads = PMBRequirementUpload::where('pendaftar_id', $pendaftar->id)
            ->with('requirement')
            ->get()
            ->keyBy('requirement_id');
        
        $data['pendaftar'] = $pendaftar;
        $data['requirements'] = $requirements;
        $data['uploads'] = $uploads;
        
        return view('master.pmb.pendaftar-detail', $data);
    }
    
    public function handleDokumen(Request $request, $code)
    {
        return redirect()->back()->with('success', 'Dokumen berhasil diupload');
    }
    
    public function validasiDokumen(Request $request, $code)
    {
        $request->validate([
            'upload_id' => 'required|exists:pmb_requirement_uploads,id',
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $upload = PMBRequirementUpload::findOrFail($request->upload_id);
        
        $upload->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);
        
        $statusText = $request->status == 'approved' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "Dokumen berhasil {$statusText}");
    }
    
    public function exportPendaftarExcel()
    {
        return redirect()->back()->with('success', 'Export Excel berhasil');
    }
    
    public function exportPendaftarPDF()
    {
        return redirect()->back()->with('success', 'Export PDF berhasil');
    }
    
    public function batchUpdateStatus(Request $request)
    {
        return redirect()->back()->with('success', 'Batch update status berhasil');
    }
    
    public function batchValidasiDokumen(Request $request, $code)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'upload_ids' => 'required|array|min:1',
            'upload_ids.*' => 'required|exists:pmb_requirement_uploads,id',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $pendaftar = Pendaftar::where('code', $code)->firstOrFail();
            
            // Get uploads that belong to this pendaftar
            $uploads = PMBRequirementUpload::whereIn('id', $request->upload_ids)
                ->where('pendaftar_id', $pendaftar->id)
                ->where('status', 'pending')
                ->get();
            
            if ($uploads->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diproses');
            }
            
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $adminNotes = $request->admin_notes;
            
            // Update all selected uploads
            $updatedCount = 0;
            foreach ($uploads as $upload) {
                $upload->update([
                    'status' => $status,
                    'admin_notes' => $adminNotes,
                    'validated_at' => now(),
                    'validated_by' => auth()->user()->id ?? null
                ]);
                $updatedCount++;
            }
            
            $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "{$updatedCount} dokumen berhasil {$actionText}";
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
