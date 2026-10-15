<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// Use Models
use App\Models\Akademik\ProgramStudi;
use App\Models\Dosen;
use App\Models\Pengaturan\WebSetting;
// Use Plugins

class ProgramStudiController extends Controller
{
    public function renderProdi()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Program Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Only get non-deleted data with proper relationships
        $data['prodi'] = ProgramStudi::with([
            'kaprodi' => function($query) {
                $query->whereNull('deleted_at');
            }
        ])->whereNull('deleted_at')->get();
        
        $data['dosens'] = Dosen::where('type', 1)->whereNull('deleted_at')->get(); // Fetch active Dosen for Kaprodi
        
        return view('master.akademik.prodi-index', $data, compact('user'));
    }

    public function handleProdi(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'takhasus' => 'nullable|string|max:255',
                'kaprodi_id' => 'nullable|exists:dosens,id',
                'level' => 'required|in:Diploma,Sarjana,Magister,Doktoral',
                'title' => 'required|in:D3,S1,S2,S3',
                'title_start' => 'nullable|string|max:50',
                'title_ended' => 'nullable|string|max:50',
                'accreditation' => 'nullable|string|max:10',
                'duration' => 'nullable|integer|min:1',
                'desc' => 'nullable|string',
                'objectives' => 'nullable|string',
                'careers' => 'nullable|string',
            ], [
                'name.required' => 'Nama Program Studi wajib diisi.',
                'name.max' => 'Nama Program Studi maksimal 255 karakter.',
                'takhasus.max' => 'Takhasus maksimal 255 karakter.',
                'kaprodi_id.exists' => 'Kaprodi yang dipilih tidak valid.',
                'level.required' => 'Jenjang Pendidikan wajib dipilih.',
                'level.in' => 'Jenjang Pendidikan yang dipilih tidak valid.',
                'title.required' => 'Gelar wajib dipilih.',
                'title.in' => 'Gelar yang dipilih tidak valid.',
                'title_start.max' => 'Gelar Awal maksimal 50 karakter.',
                'title_ended.max' => 'Gelar Akhir maksimal 50 karakter.',
                'accreditation.max' => 'Akreditasi maksimal 10 karakter.',
                'duration.integer' => 'Durasi harus berupa angka.',
                'duration.min' => 'Durasi minimal 1 tahun.',
            ]);

            // Generate unique code
            $code = 'PS-' . Str::random(8);
            
            // Generate unique slug with timestamp to ensure uniqueness
            $baseSlug = Str::slug($request->name);
            $timestamp = time();
            $slug = $baseSlug . '-' . $timestamp;
            
            // Double check if slug exists and add random string if needed
            while (ProgramStudi::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $timestamp . '-' . Str::random(3);
            }
            
            // Create new Program Studi
            $prodi = ProgramStudi::create([
                'name' => $request->name,
                'code' => $code,
                'slug' => $slug,
                'fakultas_id' => null, // No longer using fakultas
                'takhasus' => $request->takhasus,
                'kaprodi_id' => $request->kaprodi_id,
                'level' => $request->level,
                'title' => $request->title,
                'title_start' => $request->title_start,
                'title_ended' => $request->title_ended,
                'accreditation' => $request->accreditation,
                'duration' => $request->duration,
                'desc' => $request->desc,
                'objectives' => $request->objectives,
                'careers' => $request->careers,
                'status' => 'Aktif', // Default status
                'created_by' => Auth::id(),
            ]);

            // Create takhasus options if provided
            if ($request->filled('takhasus')) {
                $this->updateTakhasusOptions($prodi->id, $request->takhasus);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Program Studi berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Program Studi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateProdi(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'takhasus' => 'nullable|string|max:255',
                'kaprodi_id' => 'nullable|exists:dosens,id',
                'level' => 'required|in:Diploma,Sarjana,Magister,Doktoral',
                'title' => 'required|in:D3,S1,S2,S3',
                'title_start' => 'nullable|string|max:50',
                'title_ended' => 'nullable|string|max:50',
                'accreditation' => 'nullable|string|max:10',
                'duration' => 'nullable|integer|min:1',
                'desc' => 'nullable|string',
                'objectives' => 'nullable|string',
                'careers' => 'nullable|string',
                'status' => 'required|in:Aktif,Tidak Aktif',
            ], [
                'name.required' => 'Nama Program Studi wajib diisi.',
                'name.max' => 'Nama Program Studi maksimal 255 karakter.',
                'takhasus.max' => 'Takhasus maksimal 255 karakter.',
                'kaprodi_id.exists' => 'Kaprodi yang dipilih tidak valid.',
                'level.required' => 'Jenjang Pendidikan wajib dipilih.',
                'level.in' => 'Jenjang Pendidikan yang dipilih tidak valid.',
                'title.required' => 'Gelar wajib dipilih.',
                'title.in' => 'Gelar yang dipilih tidak valid.',
                'title_start.max' => 'Gelar Awal maksimal 50 karakter.',
                'title_ended.max' => 'Gelar Akhir maksimal 50 karakter.',
                'accreditation.max' => 'Akreditasi maksimal 10 karakter.',
                'duration.integer' => 'Durasi harus berupa angka.',
                'duration.min' => 'Durasi minimal 1 tahun.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status yang dipilih tidak valid.',
            ]);

            $prodi = ProgramStudi::where('code', $code)->firstOrFail();

            // Generate unique slug for update (only if name changed)
            $baseSlug = Str::slug($request->name);
            
            // If name hasn't changed, keep the existing slug
            if ($prodi->name === $request->name) {
                $slug = $prodi->slug;
            } else {
                // Generate new unique slug with timestamp
                $timestamp = time();
                $slug = $baseSlug . '-' . $timestamp;
                
                // Double check if slug exists and add random string if needed (exclude current record)
                while (ProgramStudi::where('slug', $slug)->where('id', '!=', $prodi->id)->exists()) {
                    $slug = $baseSlug . '-' . $timestamp . '-' . Str::random(3);
                }
            }

            $prodi->update([
                'name' => $request->name,
                'slug' => $slug,
                'fakultas_id' => null, // No longer using fakultas
                'takhasus' => $request->takhasus,
                'kaprodi_id' => $request->kaprodi_id,
                'level' => $request->level,
                'title' => $request->title,
                'title_start' => $request->title_start,
                'title_ended' => $request->title_ended,
                'accreditation' => $request->accreditation,
                'duration' => $request->duration,
                'desc' => $request->desc,
                'objectives' => $request->objectives,
                'careers' => $request->careers,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            // Update takhasus_program_studis table when takhasus is updated
            if ($request->filled('takhasus')) {
                $this->updateTakhasusOptions($prodi->id, $request->takhasus);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Program Studi berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Program Studi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update takhasus options for a program studi
     */
    private function updateTakhasusOptions($prodiId, $takhasusString)
    {
        // Parse comma-separated takhasus string
        $takhasusArray = array_map('trim', explode(',', $takhasusString));
        $takhasusArray = array_filter($takhasusArray); // Remove empty values

        // Get existing takhasus options for this program studi
        $existingTakhasus = DB::table('takhasus_program_studis')
            ->where('program_studi_id', $prodiId)
            ->pluck('name')
            ->toArray();

        // Add new takhasus options that don't exist yet
        foreach ($takhasusArray as $takhasusName) {
            if (!in_array($takhasusName, $existingTakhasus)) {
                DB::table('takhasus_program_studis')->insert([
                    'program_studi_id' => $prodiId,
                    'name' => $takhasusName,
                    'description' => 'Takhasus untuk program studi',
                    'status' => 'Aktif',
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Optionally, you can also remove takhasus options that are no longer in the list
        // But for safety, we'll keep them and just add new ones
    }

    public function deleteProdi($code)
    {
        try {
            DB::beginTransaction();

            $prodi = ProgramStudi::where('code', $code)->firstOrFail();

            $prodi->update([
                'deleted_by' => Auth::id()
            ]);
            $prodi->delete(); // Soft delete

            DB::commit();
            return redirect()->back()->with('success', 'Program Studi berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Program Studi: ' . $e->getMessage());
        }
    }
}
