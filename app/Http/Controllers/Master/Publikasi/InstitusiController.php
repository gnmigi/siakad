<?php

namespace App\Http\Controllers\Master\Publikasi;

use App\Http\Controllers\Controller;
use App\Models\Institusi\InstitutionPage;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class InstitusiController extends Controller
{
    public function renderProfile()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['pages'] = 'Profil Institusi';
        $data['menus'] = 'Master Publikasi';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['page'] = InstitutionPage::getByType('profile');
        
        return view('master.publikasi.institusi-profile', $data, compact('user'));
    }

    public function handleProfile(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'youtube_url' => 'nullable|url',
        ]);

        // Handle is_active separately
        $isActive = $request->has('is_active') ? true : false;

        try {
            $page = InstitutionPage::firstOrNew(['page_type' => 'profile']);
            
            // Ensure additional_data is always an array
            $additionalData = [];
            if ($page->additional_data && is_array($page->additional_data)) {
                $additionalData = $page->additional_data;
            } elseif ($page->additional_data && is_string($page->additional_data)) {
                $additionalData = json_decode($page->additional_data, true) ?? [];
            }
            
            // Add YouTube URL to additional data
            if ($request->filled('youtube_url')) {
                $additionalData['youtube_url'] = $request->youtube_url;
            } else {
                // Remove youtube_url if empty
                unset($additionalData['youtube_url']);
            }
            
            $data = [
                'page_type' => 'profile',
                'title' => $request->title,
                'content' => $request->content,
                'additional_data' => $additionalData,
                'is_active' => $isActive
            ];

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($page->exists && $page->image && Storage::disk('public')->exists('images/institusi/' . $page->image)) {
                    Storage::disk('public')->delete('images/institusi/' . $page->image);
                }

                $imageName = 'profile-' . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->storeAs('images/institusi', $imageName, 'public');
                $data['image'] = $imageName;
            }

            $page->fill($data);
            $page->save();

            Alert::success('Berhasil', 'Profil institusi berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function renderVisionMission()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['pages'] = 'Visi & Misi';
        $data['menus'] = 'Master Publikasi';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['page'] = InstitutionPage::getByType('vision_mission');
        
        return view('master.publikasi.institusi-vision-mission', $data, compact('user'));
    }

    public function handleVisionMission(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle is_active separately
        $isActive = $request->has('is_active') ? true : false;

        try {
            $page = InstitutionPage::firstOrNew(['page_type' => 'vision_mission']);
            
            $data = [
                'page_type' => 'vision_mission',
                'title' => $request->title,
                'content' => $request->vision, // Store vision in content field
                'additional_data' => [
                    'vision' => $request->vision,
                    'mission' => $request->mission
                ],
                'is_active' => $isActive
            ];

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($page->exists && $page->image && Storage::disk('public')->exists('images/institusi/' . $page->image)) {
                    Storage::disk('public')->delete('images/institusi/' . $page->image);
                }

                $imageName = 'vision-mission-' . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->storeAs('images/institusi', $imageName, 'public');
                $data['image'] = $imageName;
            }

            $page->fill($data);
            $page->save();

            Alert::success('Berhasil', 'Visi & Misi berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function renderStructure()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['pages'] = 'Struktur Organisasi';
        $data['menus'] = 'Master Publikasi';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['page'] = InstitutionPage::getByType('structure');
        
        return view('master.publikasi.institusi-structure', $data, compact('user'));
    }

    public function handleStructure(Request $request)
    {
        // Validate basic fields first
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'structure_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'leaders' => 'nullable|array',
            'leaders.*.nama' => 'nullable|string|max:255',
            'leaders.*.jabatan' => 'nullable|string|max:255',
            'leaders.*.pendidikan' => 'nullable|string|max:255',
            'leaders.*.deskripsi' => 'nullable|string',
        ]);

        // Validate leader photos separately with better error handling
        if ($request->has('leaders') && is_array($request->leaders)) {
            foreach ($request->leaders as $index => $leader) {
                if (isset($leader['foto']) && $leader['foto'] instanceof \Illuminate\Http\UploadedFile) {
                    // Check file size (1MB = 1024KB)
                    if ($leader['foto']->getSize() > 1024 * 1024) {
                        Alert::error('Gagal', 'Foto pengurus ' . ($leader['nama'] ?? 'ke-' . ($index + 1)) . ' terlalu besar. Maksimal 1MB.');
                        return redirect()->back()->withInput();
                    }
                    
                    // Check file type
                    if (!in_array($leader['foto']->getClientOriginalExtension(), ['jpeg', 'jpg', 'png', 'gif'])) {
                        Alert::error('Gagal', 'Format foto pengurus ' . ($leader['nama'] ?? 'ke-' . ($index + 1)) . ' tidak didukung. Gunakan JPEG, PNG, atau GIF.');
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        // Handle is_active separately
        $isActive = $request->has('is_active') ? true : false;

        try {
            $page = InstitutionPage::firstOrNew(['page_type' => 'structure']);
            
            // Ensure additional_data is always an array
            $additionalData = [];
            if ($page->additional_data && is_array($page->additional_data)) {
                $additionalData = $page->additional_data;
            } elseif ($page->additional_data && is_string($page->additional_data)) {
                $additionalData = json_decode($page->additional_data, true) ?? [];
            }
            
            $leaders = [];

            $data = [
                'page_type' => 'structure',
                'title' => $request->title,
                'content' => $request->content,
                'is_active' => $isActive
            ];

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($page->exists && $page->image && Storage::disk('public')->exists('images/institusi/' . $page->image)) {
                    Storage::disk('public')->delete('images/institusi/' . $page->image);
                }

                $imageName = 'structure-' . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->storeAs('images/institusi', $imageName, 'public');
                $data['image'] = $imageName;
            }

            if ($request->hasFile('structure_image')) {
                // Delete old structure image if exists
                if (isset($additionalData['bagan_organisasi']) && Storage::disk('public')->exists($additionalData['bagan_organisasi'])) {
                    Storage::disk('public')->delete($additionalData['bagan_organisasi']);
                }

                $structureImageName = 'structure-chart-' . time() . '.' . $request->structure_image->getClientOriginalExtension();
                $request->structure_image->storeAs('images/institusi', $structureImageName, 'public');
                $additionalData['bagan_organisasi'] = 'storage/images/institusi/' . $structureImageName;
            }

            // Handle leaders data
            if ($request->has('leaders') && is_array($request->leaders)) {
                foreach ($request->leaders as $index => $leader) {
                    // Skip empty leaders
                    if (empty($leader['nama']) && empty($leader['jabatan'])) {
                        continue;
                    }

                    $leaderData = [
                        'nama' => $leader['nama'] ?? '',
                        'jabatan' => $leader['jabatan'] ?? '',
                        'pendidikan' => $leader['pendidikan'] ?? '',
                        'deskripsi' => $leader['deskripsi'] ?? ''
                    ];

                    // Handle leader photo upload
                    if (isset($leader['foto']) && $leader['foto'] instanceof \Illuminate\Http\UploadedFile && $leader['foto']->isValid()) {
                        $leaderPhotoName = 'leader-' . $index . '-' . time() . '.' . $leader['foto']->getClientOriginalExtension();
                        $leader['foto']->storeAs('images/institusi/leaders', $leaderPhotoName, 'public');
                        $leaderData['foto'] = 'storage/images/institusi/leaders/' . $leaderPhotoName;
                    } else {
                        // Keep existing photo if no new photo uploaded
                        $existingLeaders = $additionalData['struktur'] ?? [];
                        if (isset($existingLeaders[$index]['foto'])) {
                            $leaderData['foto'] = $existingLeaders[$index]['foto'];
                        }
                    }

                    $leaders[] = $leaderData;
                }
            }

            $additionalData['struktur'] = $leaders;
            $data['additional_data'] = $additionalData;

            $page->fill($data);
            $page->save();

            Alert::success('Berhasil', 'Struktur organisasi berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function renderFacilities()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['pages'] = 'Fasilitas';
        $data['menus'] = 'Master Publikasi';
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['page'] = InstitutionPage::getByType('facilities');
        
        return view('master.publikasi.institusi-facilities', $data, compact('user'));
    }

    public function handleFacilities(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facilities' => 'nullable|array',
            'facilities.*.name' => 'nullable|string|max:255',
            'facilities.*.description' => 'nullable|string',
            'facilities.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle is_active separately
        $isActive = $request->has('is_active') ? true : false;

        try {
            $page = InstitutionPage::firstOrNew(['page_type' => 'facilities']);
            
            // Ensure additional_data is always an array
            $additionalData = [];
            if ($page->additional_data && is_array($page->additional_data)) {
                $additionalData = $page->additional_data;
            } elseif ($page->additional_data && is_string($page->additional_data)) {
                $additionalData = json_decode($page->additional_data, true) ?? [];
            }
            
            $facilities = [];

            $data = [
                'page_type' => 'facilities',
                'title' => $request->title,
                'content' => $request->content,
                'is_active' => $isActive
            ];

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($page->exists && $page->image && Storage::disk('public')->exists('images/institusi/' . $page->image)) {
                    Storage::disk('public')->delete('images/institusi/' . $page->image);
                }

                $imageName = 'facilities-' . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->storeAs('images/institusi', $imageName, 'public');
                $data['image'] = $imageName;
            }

            // Handle facilities data with improved error handling
            if ($request->has('facilities') && is_array($request->facilities)) {
                foreach ($request->facilities as $index => $facility) {
                    // Skip if facility is not an array or is empty
                    if (!is_array($facility)) {
                        continue;
                    }
                    
                    // Skip empty facilities (both name and description are empty)
                    if (empty($facility['name']) && empty($facility['description'])) {
                        continue;
                    }
                    
                    // Safely get name and description with proper defaults
                    $name = $facility['name'] ?? '';
                    $description = $facility['description'] ?? '';

                    // Handle JSON decoding for name and description if they are JSON strings
                    if (is_string($name) && !empty($name)) {
                        $decodedName = json_decode($name, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedName)) {
                            $name = $decodedName;
                        }
                    }
                    
                    if (is_string($description) && !empty($description)) {
                        $decodedDesc = json_decode($description, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedDesc)) {
                            $description = $decodedDesc;
                        }
                    }

                    // Prepare facility data
                    $facilityData = [
                        'name' => is_array($name) ? json_encode($name) : (string)$name,
                        'description' => is_array($description) ? json_encode($description) : (string)$description
                    ];

                    // Handle image upload with proper validation
                    if (isset($facility['image']) && 
                        $facility['image'] instanceof \Illuminate\Http\UploadedFile && 
                        $facility['image']->isValid()) {
                        
                        $facilityImageName = 'facility-' . $index . '-' . time() . '.' . $facility['image']->getClientOriginalExtension();
                        $facility['image']->storeAs('images/institusi/facilities', $facilityImageName, 'public');
                        $facilityData['image'] = $facilityImageName;
                    } else {
                        // Keep existing image if no new image uploaded and facility exists
                        $existingFacilities = $additionalData['facilities'] ?? [];
                        if (isset($existingFacilities[$index]['image'])) {
                            $facilityData['image'] = $existingFacilities[$index]['image'];
                        }
                    }

                    $facilities[] = $facilityData;
                }
            }

            $additionalData['facilities'] = $facilities;
            $data['additional_data'] = $additionalData;

            $page->fill($data);
            $page->save();

            Alert::success('Berhasil', 'Fasilitas berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
