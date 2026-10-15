<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// Use Models
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\JenisKelas;
use App\Models\Mahasantri;
use App\Models\Pengaturan\WebSetting;

class KelasController extends Controller
{
    public function renderKelas()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Kelas";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Only get non-deleted data with proper relationships
        $data['kelas'] = Kelas::with([
            'tahunAkademik' => function($query) {
                $query->whereNull('deleted_at');
            },
            'programStudi' => function($query) {
                $query->whereNull('deleted_at');
            },
            'jenisKelas' => function($query) {
                $query->whereNull('deleted_at');
            },
            'ketua' => function($query) {
                $query->whereNull('deleted_at');
            }
        ])->whereNull('deleted_at')->get();
        
        $data['tahun_akademik'] = TahunAkademik::where('status', 'Aktif')->whereNull('deleted_at')->get();
        $data['program_studi'] = ProgramStudi::where('status', 'Aktif')->whereNull('deleted_at')->get();
        $data['jenis_kelas'] = JenisKelas::where('status', 'Aktif')->whereNull('deleted_at')->get();
        $data['mahasiswa'] = Mahasantri::whereNull('deleted_at')->get();
        
        return view('master.akademik.kelas-index', $data, compact('user'));
    }

    public function handleKelas(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'taka_id' => 'required|integer',
                'prodi_id' => 'required|integer',
                'jenis_kelas_id' => 'required|integer',
                'ketua_id' => 'nullable|integer',
                'capacity' => 'required|integer|min:1',
            ]);

            // Generate unique code
            $code = 'KLS-' . Str::random(8);
            
            // Create new Kelas
            Kelas::create([
                'name' => $request->name,
                'code' => $code,
                'taka_id' => $request->taka_id,
                'prodi_id' => $request->prodi_id,
                'jenis_kelas_id' => $request->jenis_kelas_id,
                'ketua_id' => $request->ketua_id,
                'capacity' => $request->capacity,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateKelas(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'taka_id' => 'required|integer',
                'prodi_id' => 'required|integer',
                'jenis_kelas_id' => 'required|integer',
                'ketua_id' => 'nullable|integer',
                'capacity' => 'required|integer|min:1',
            ]);

            $kelas = Kelas::where('code', $code)->firstOrFail();

            $kelas->update([
                'name' => $request->name,
                'taka_id' => $request->taka_id,
                'prodi_id' => $request->prodi_id,
                'jenis_kelas_id' => $request->jenis_kelas_id,
                'ketua_id' => $request->ketua_id,
                'capacity' => $request->capacity,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteKelas($code)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::where('code', $code)->firstOrFail();

            // Update deleted_by before soft deletion
            $kelas->update([
                'deleted_by' => Auth::id()
            ]);

            // Soft delete (data masih ada di database dengan deleted_at timestamp)
            $kelas->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil dihapus (soft delete)');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Kelas: ' . $e->getMessage());
        }
    }

    /**
     * Force delete kelas (permanent deletion)
     */
    public function forceDeleteKelas($code)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::withTrashed()->where('code', $code)->firstOrFail();

            // Check if Kelas has related JadwalKuliah before deleting
            if ($kelas->jadwalKuliah()->count() > 0) {
                // Detach all related jadwal kuliah first
                $kelas->jadwalKuliah()->detach();
            }

            // Check if Kelas has related Mahasantri (anggota) before deleting
            if ($kelas->anggota()->count() > 0) {
                // Update mahasiswa to remove kelas_id reference
                $kelas->anggota()->update(['kelas_id' => 0]);
            }

            // Force delete (permanent deletion from database)
            $kelas->forceDelete();

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil dihapus permanen dari database');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Kelas permanen: ' . $e->getMessage());
        }
    }

    /**
     * Restore soft deleted kelas
     */
    public function restoreKelas($code)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::withTrashed()->where('code', $code)->firstOrFail();
            
            if (!$kelas->trashed()) {
                return redirect()->back()->with('error', 'Kelas tidak dalam status terhapus');
            }

            $kelas->restore();
            $kelas->update([
                'deleted_by' => null,
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil dipulihkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memulihkan Kelas: ' . $e->getMessage());
        }
    }

    /**
     * View students in a class with multi-select interface
     */
    public function viewStudents($code)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Kelola Mahasiswa Kelas";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Get the class
        $data['kelas'] = Kelas::with([
            'tahunAkademik',
            'programStudi',
            'jenisKelas',
            'anggota' => function($query) {
                $query->whereNull('deleted_at')->orderBy('name');
            }
        ])->where('code', $code)->whereNull('deleted_at')->firstOrFail();
        
        // Get available students (students without any class OR students from other classes)
        $data['available_students'] = Mahasantri::where(function($query) use ($data) {
                $query->where('kelas_id', 0) // Students without class (kelas_id = 0)
                      ->orWhere('kelas_id', '!=', $data['kelas']->id);
            })
            ->where('type', 1) // Only active students
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
        
        // Get students currently in this class
        $data['current_students'] = $data['kelas']->anggota;
        
        return view('master.akademik.kelas-students', $data, compact('user'));
    }

    /**
     * Add multiple students to a class
     */
    public function addStudents(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'integer|exists:mahasiswas,id'
            ]);

            $kelas = Kelas::where('code', $code)->firstOrFail();
            
            // Check capacity
            $currentCount = $kelas->anggota()->count();
            $newStudentsCount = count($request->student_ids);
            
            if (($currentCount + $newStudentsCount) > $kelas->capacity) {
                return redirect()->back()->with('error', 
                    'Kapasitas kelas tidak mencukupi. Kapasitas: ' . $kelas->capacity . 
                    ', Saat ini: ' . $currentCount . 
                    ', Akan ditambah: ' . $newStudentsCount);
            }

            // Add students to class
            $addedCount = 0;
            foreach ($request->student_ids as $studentId) {
                $student = Mahasantri::find($studentId);
                if ($student && $student->kelas_id !== $kelas->id) {
                    $student->update([
                        'kelas_id' => $kelas->id,
                        'updated_by' => Auth::id()
                    ]);
                    $addedCount++;
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 
                $addedCount . ' mahasiswa berhasil ditambahkan ke kelas ' . $kelas->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan mahasiswa ke kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove a student from class
     */
    public function removeStudent($code, $studentId)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::where('code', $code)->firstOrFail();
            $student = Mahasantri::where('id', $studentId)
                                 ->where('kelas_id', $kelas->id)
                                 ->firstOrFail();

            $student->update([
                'kelas_id' => 0, // Set to 0 instead of null since column is NOT NULL with default 0
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 
                'Mahasiswa ' . $student->name . ' berhasil dikeluarkan dari kelas ' . $kelas->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengeluarkan mahasiswa dari kelas: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple students from class
     */
    public function removeStudents(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'integer|exists:mahasiswas,id'
            ]);

            $kelas = Kelas::where('code', $code)->firstOrFail();
            
            // Remove students from class
            $removedCount = 0;
            foreach ($request->student_ids as $studentId) {
                $student = Mahasantri::where('id', $studentId)
                                     ->where('kelas_id', $kelas->id)
                                     ->first();
                
                if ($student) {
                    $student->update([
                        'kelas_id' => 0, // Set to 0 instead of null since column is NOT NULL with default 0
                        'updated_by' => Auth::id()
                    ]);
                    $removedCount++;
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 
                $removedCount . ' mahasiswa berhasil dikeluarkan dari kelas ' . $kelas->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengeluarkan mahasiswa dari kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get available students for AJAX requests
     */
    public function getAvailableStudents($code)
    {
        try {
            $kelas = Kelas::where('code', $code)->firstOrFail();
            
            $availableStudents = Mahasantri::where(function($query) use ($kelas) {
                    $query->where('kelas_id', 0) // Students without class (kelas_id = 0)
                          ->orWhere('kelas_id', '!=', $kelas->id);
                })
                ->where('type', 1) // Only active students
                ->whereNull('deleted_at')
                ->select('id', 'name', 'nim', 'kelas_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $availableStudents
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }
}
