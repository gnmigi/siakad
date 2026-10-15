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
use App\Models\Publikasi\Berita;
use App\Models\Publikasi\Kategori;
use App\Models\Pengaturan\WebSetting;
// Use Plugins

class BeritaController extends Controller
{
    public function renderBerita()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Berita";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Only get non-deleted data
        $data['berita'] = Berita::whereNull('deleted_at')->latest()->get();
        $data['kategori'] = Kategori::whereNull('deleted_at')->get();
        
        return view('master.publikasi.berita-index', $data, compact('user'));
    }

    public function viewBerita($slug)
    {
        $user = Auth::user();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Berita";
        $data['webs'] = WebSetting::first();
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['berita'] = Berita::where('slug', $slug)->firstOrFail();
        
        return view('master.publikasi.berita-view', $data, compact('user'));
    }

    public function handleBerita(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'kategori_id' => 'required|exists:kategoris,id',
                'name' => 'required|string|max:255',
                'content' => 'required_if:content_type,simple|nullable|string',
                'content_type' => 'required|in:simple,dynamic',
                'dynamic_content' => 'required_if:content_type,dynamic|nullable|string',
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
            while (Berita::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $code = 'BRT-' . strtoupper(Str::random(8));
            
            // Handle photo upload
            $photoName = time() . '-' . $code . '-' . uniqid() .'.' . $request->photo->getClientOriginalExtension();
            $request->photo->storeAs('images/berita', $photoName, 'public');
            
            // Handle attachments
            $attachments = $this->processAttachments($request, $code);
            
            // Handle dynamic content
            $dynamicContent = null;
            if ($request->content_type === 'dynamic') {
                $dynamicContent = $this->processDynamicContent($request, $code);
            }
            
            $berita = Berita::create([
                'code' => $code,
                'kategori_id' => $request->kategori_id,
                'name' => $request->name,
                'slug' => $slug,
                'content' => $request->content_type === 'dynamic' ? '' : $request->content,
                'content_type' => $request->content_type,
                'dynamic_content' => $dynamicContent,
                'photo' => $photoName,
                'status' => $request->status,
                'attachments' => $attachments,
                'created_by' => Auth::id()
            ]);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'publikasi.berita-render')->with('success', 'Berita berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBerita(Request $request, $code)
    {
        try {
            DB::beginTransaction();
            
            $berita = Berita::where('code', $code)->firstOrFail();
            
            $request->validate([
                'kategori_id' => 'required|exists:kategoris,id',
                'name' => 'required|string|max:255',
                'content' => 'required_if:content_type,simple|nullable|string',
                'content_type' => 'required|in:simple,dynamic',
                'dynamic_content' => 'required_if:content_type,dynamic|nullable|string',
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
            while (Berita::where('slug', $slug)->where('code', '!=', $code)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            // Handle attachments
            $attachments = $this->processAttachments($request, $code, $berita->attachments);
            
            // Handle dynamic content
            $dynamicContent = $berita->dynamic_content;
            if ($request->content_type === 'dynamic') {
                $dynamicContent = $this->processDynamicContent($request, $code, $berita->dynamic_content);
            }
            
            $updateData = [
                'kategori_id' => $request->kategori_id,
                'name' => $request->name,
                'slug' => $slug,
                'content' => $request->content_type === 'dynamic' ? '' : $request->content,
                'content_type' => $request->content_type,
                'dynamic_content' => $dynamicContent,
                'status' => $request->status,
                'attachments' => $attachments,
                'updated_by' => Auth::id()
            ];
            
            // Handle photo upload if new photo is provided
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($berita->photo) {
                    Storage::disk('public')->delete('images/berita/' . $berita->photo);
                }
                
                // Save new photo
                $photoName = time() . '-' . $code . '-' . uniqid() .'.' . $request->photo->getClientOriginalExtension();
                $request->photo->storeAs('images/berita', $photoName, 'public');
                $updateData['photo'] = $photoName;
            }
            
            $berita->update($updateData);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'publikasi.berita-render')->with('success', 'Data berita berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteBerita($code)
    {
        try {
            DB::beginTransaction();

            $berita = Berita::where('code', $code)->firstOrFail();
            
            // Delete photo
            if ($berita->photo) {
                Storage::disk('public')->delete('images/berita/' . $berita->photo);
            }

            // Delete attachment files
            if ($berita->attachments) {
                foreach ($berita->attachments as $attachment) {
                    if ($attachment['type'] === 'file' && isset($attachment['path'])) {
                        Storage::disk('public')->delete('attachments/berita/' . $attachment['path']);
                    }
                }
            }

            $berita->update([
                'deleted_by' => Auth::id()
            ]);
            $berita->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Berita berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus berita: ' . $e->getMessage());
        }
    }

    /**
     * Process attachments from request
     */
    private function processAttachments(Request $request, $code, $existingAttachments = null)
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
                    $file->storeAs('attachments/berita', $fileName, 'public');
                    
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

    /**
     * Process dynamic content from request
     */
    private function processDynamicContent(Request $request, $code, $existingContent = null)
    {
        $dynamicContentData = json_decode($request->input('dynamic_content'), true);
        
        if (!$dynamicContentData) {
            return null;
        }

        $processedContent = [];
        
        // Track existing images to delete unused ones
        $existingImages = [];
        if ($existingContent) {
            foreach ($existingContent as $existingBlock) {
                if ($existingBlock['type'] === 'image' && isset($existingBlock['data']['image_path'])) {
                    $existingImages[] = $existingBlock['data']['image_path'];
                }
            }
        }
        
        $usedImages = [];
        
        foreach ($dynamicContentData as $index => $block) {
            $processedBlock = [
                'id' => $block['id'] ?? uniqid(),
                'type' => $block['type'],
                'order' => $index,
                'data' => []
            ];

            switch ($block['type']) {
                case 'text':
                    $processedBlock['data'] = [
                        'content' => $block['data']['content'] ?? ''
                    ];
                    break;

                case 'image':
                    // Handle new image upload
                    if ($request->hasFile('content_images')) {
                        $contentImages = $request->file('content_images');
                        if (isset($contentImages[$block['id']]) && $contentImages[$block['id']]->isValid()) {
                            $imageFile = $contentImages[$block['id']];
                            $imageName = time() . '-' . $code . '-content-' . $index . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                            $imageFile->storeAs('images/berita/content', $imageName, 'public');
                            
                            // If updating and there's an existing image for this block, delete it
                            if ($existingContent && isset($existingContent[$index]) && 
                                $existingContent[$index]['type'] === 'image' && 
                                isset($existingContent[$index]['data']['image_path'])) {
                                $oldImagePath = $existingContent[$index]['data']['image_path'];
                                Storage::disk('public')->delete('images/berita/content/' . $oldImagePath);
                            }
                            
                            $processedBlock['data'] = [
                                'image_path' => $imageName,
                                'caption' => $block['data']['caption'] ?? '',
                                'alt_text' => $block['data']['alt_text'] ?? '',
                                'alignment' => $block['data']['alignment'] ?? 'center'
                            ];
                            
                            $usedImages[] = $imageName;
                        } else {
                            // No new upload, keep existing image if available
                            if (isset($block['data']['image_path']) && !empty($block['data']['image_path'])) {
                                $processedBlock['data'] = [
                                    'image_path' => $block['data']['image_path'],
                                    'caption' => $block['data']['caption'] ?? '',
                                    'alt_text' => $block['data']['alt_text'] ?? '',
                                    'alignment' => $block['data']['alignment'] ?? 'center'
                                ];
                                $usedImages[] = $block['data']['image_path'];
                            }
                        }
                    } elseif (isset($block['data']['image_path']) && !empty($block['data']['image_path'])) {
                        // Keep existing image
                        $processedBlock['data'] = [
                            'image_path' => $block['data']['image_path'],
                            'caption' => $block['data']['caption'] ?? '',
                            'alt_text' => $block['data']['alt_text'] ?? '',
                            'alignment' => $block['data']['alignment'] ?? 'center'
                        ];
                        $usedImages[] = $block['data']['image_path'];
                    }
                    break;

                case 'link':
                    $processedBlock['data'] = [
                        'url' => $block['data']['url'] ?? '',
                        'title' => $block['data']['title'] ?? '',
                        'description' => $block['data']['description'] ?? ''
                    ];
                    break;

                case 'divider':
                    $processedBlock['data'] = [];
                    break;
            }

            $processedContent[] = $processedBlock;
        }

        // Delete unused images from existing content
        if ($existingContent) {
            foreach ($existingImages as $existingImage) {
                if (!in_array($existingImage, $usedImages)) {
                    Storage::disk('public')->delete('images/berita/content/' . $existingImage);
                }
            }
        }

        return $processedContent;
    }
}
