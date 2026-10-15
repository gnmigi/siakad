<?php

namespace App\Http\Controllers\Master\Publikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// Use Models
use App\Models\Publikasi\Pengumuman;
use App\Models\Publikasi\Kategori;
use App\Models\Pengaturan\WebSetting;
// Use Plugins

class PengumumanController extends Controller
{
    public function renderPengumuman()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Pengumuman";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['pengumuman'] = Pengumuman::latest()->get();
        $data['kategori'] = Kategori::all();
        
        return view('master.publikasi.pengumuman-index', $data, compact('user'));
    }

    public function viewPengumuman($slug)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Pengumuman";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['pengumuman'] = Pengumuman::where('slug', $slug)->firstOrFail();
        
        return view('master.publikasi.pengumuman-view', $data, compact('user'));
    }

    public function handlePengumuman(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'kategori_id' => 'required|exists:kategoris,id',
                'name' => 'required|string|max:255',
                'content' => 'required|string',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Draft,Publish,Archive',
                'attachment_files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
                'attachment_links.*' => 'nullable|url',
                'attachment_titles.*' => 'nullable|string|max:255'
            ]);

            // Generate unique slug
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            
            // Check if slug exists and generate unique one
            while (Pengumuman::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $code = 'PNG-' . strtoupper(Str::random(8));
            
            // Handle photo upload
            $photoName = time() . '-' . $code . '-' . uniqid() .'.' . $request->photo->getClientOriginalExtension();
            $request->photo->storeAs('images/pengumuman', $photoName, 'public');
            
            // Handle attachments
            $attachments = $this->processAttachments($request, $code);
            
            $pengumuman = Pengumuman::create([
                'code' => $code,
                'kategori_id' => $request->kategori_id,
                'name' => $request->name,
                'slug' => $slug,
                'content' => $request->content,
                'photo' => $photoName,
                'status' => $request->status,
                'attachments' => $attachments,
                'created_by' => Auth::id()
            ]);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'publikasi.pengumuman-render')->with('success', 'Pengumuman berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePengumuman(Request $request, $code)
    {
        try {
            DB::beginTransaction();
            
            $pengumuman = Pengumuman::where('code', $code)->firstOrFail();
            
            $request->validate([
                'kategori_id' => 'required|exists:kategoris,id',
                'name' => 'required|string|max:255',
                'content' => 'required|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Draft,Publish,Archive',
                'attachment_files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
                'attachment_links.*' => 'nullable|url',
                'attachment_titles.*' => 'nullable|string|max:255'
            ]);

            // Generate unique slug (exclude current record)
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            
            // Check if slug exists and generate unique one (excluding current record)
            while (Pengumuman::where('slug', $slug)->where('code', '!=', $code)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            // Handle attachments
            $attachments = $this->processAttachments($request, $code, $pengumuman->attachments);
            
            $updateData = [
                'kategori_id' => $request->kategori_id,
                'name' => $request->name,
                'slug' => $slug,
                'content' => $request->content,
                'status' => $request->status,
                'attachments' => $attachments,
                'updated_by' => Auth::id()
            ];
            
            // Handle photo upload if new photo is provided
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($pengumuman->photo) {
                    Storage::disk('public')->delete('images/pengumuman/' . $pengumuman->photo);
                }
                
                // Save new photo
                $photoName = time() . '-' . $code . '-' . uniqid() .'.' . $request->photo->getClientOriginalExtension();
                $request->photo->storeAs('images/pengumuman', $photoName, 'public');
                $updateData['photo'] = $photoName;
            }
            
            $pengumuman->update($updateData);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'publikasi.pengumuman-render')->with('success', 'Data pengumuman berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function deletePengumuman($code)
    {
        try {
            DB::beginTransaction();

            $pengumuman = Pengumuman::where('code', $code)->firstOrFail();
            
            // Delete photo
            if ($pengumuman->photo) {
                Storage::disk('public')->delete('images/pengumuman/' . $pengumuman->photo);
            }

            // Delete attachment files
            if ($pengumuman->attachments) {
                foreach ($pengumuman->attachments as $attachment) {
                    if ($attachment['type'] === 'file' && isset($attachment['path'])) {
                        Storage::disk('public')->delete('attachments/pengumuman/' . $attachment['path']);
                    }
                }
            }

            $pengumuman->update([
                'deleted_by' => Auth::id()
            ]);
            $pengumuman->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Pengumuman berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pengumuman: ' . $e->getMessage());
        }
    }

    /**
     * Process attachments from request
     */
    protected function processAttachments(Request $request, $code, $existingAttachments = null)
    {
        $attachments = [];
        
        // Handle file uploads
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $index => $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '-' . $code . '-' . $index . '-' . uniqid() . '.' . $extension;
                    
                    // Store file
                    $file->storeAs('attachments/pengumuman', $fileName, 'public');
                    
                    $attachments[] = [
                        'type' => 'file',
                        'title' => $request->input('attachment_titles.' . $index) ?: $originalName,
                        'path' => $fileName,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }
        }
        
        // Handle external links
        if ($request->has('attachment_links')) {
            foreach ($request->input('attachment_links') as $index => $link) {
                if (!empty($link)) {
                    $title = $request->input('attachment_titles.' . ($index + count($attachments))) ?: 'Link External';
                    
                    $attachments[] = [
                        'type' => 'link',
                        'title' => $title,
                        'url' => $link
                    ];
                }
            }
        }
        
        // If updating, merge with existing attachments that should be kept
        if ($existingAttachments && $request->has('keep_attachments')) {
            $keepAttachments = $request->input('keep_attachments', []);
            foreach ($existingAttachments as $index => $attachment) {
                if (in_array($index, $keepAttachments)) {
                    $attachments[] = $attachment;
                }
            }
        }
        
        return $attachments;
    }
}
