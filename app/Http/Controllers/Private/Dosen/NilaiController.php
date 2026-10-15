<?php

namespace App\Http\Controllers\Private\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// USE MODELS
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\BobotNilaiSetting;
use App\Models\Akademik\GradeScaleSetting;
use App\Models\Mahasantri;
use App\Exports\DosenNilaiTemplateByClassExport;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    public function renderInputNilai(Request $request)
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Input Nilai";
        $data['pages'] = "Input Nilai Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get mata kuliah where current dosen is assigned
        $mataKuliah = MataKuliah::with(['programStudi'])
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $data['mataKuliah'] = $mataKuliah;

        // Check if this is edit mode (mahasiswa_id and mata_kuliah_id provided)
        $existingNilai = null;
        $selectedMahasiswa = null;

        if ($request->filled('mahasiswa_id') && $request->filled('mata_kuliah_id')) {
            // Verify that the dosen is assigned to this mata kuliah
            $mataKuliah = MataKuliah::where('id', $request->mata_kuliah_id)
                ->where(function($query) use ($user) {
                    $query->where('dosen1_id', $user->id)
                          ->orWhere('dosen2_id', $user->id)
                          ->orWhere('dosen3_id', $user->id);
                })
                ->first();

            if ($mataKuliah) {
                // Get existing nilai data
                $existingNilai = DB::table('nilai_mahasiswa')
                    ->where('mata_kuliah_id', $request->mata_kuliah_id)
                    ->where('mahasiswa_id', $request->mahasiswa_id)
                    ->where('dosen_id', $user->id)
                    ->first();

                // Get mahasiswa data
                $selectedMahasiswa = Mahasantri::find($request->mahasiswa_id);

                if ($existingNilai) {
                    $data['menus'] = "Edit Nilai";
                    $data['pages'] = "Edit Nilai Mahasantri";
                }
            }
        }

        $data['existingNilai'] = $existingNilai;
        $data['selectedMahasiswa'] = $selectedMahasiswa;

        // Get dynamic bobot nilai settings
        $data['bobotNilaiSettings'] = BobotNilaiSetting::getActiveComponents();

        return view('central.backpage.dosen-input-nilai', $data, compact('user'));
    }

    public function renderRekapNilai(Request $request)
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Rekap Nilai";
        $data['pages'] = "Rekap Nilai Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get mata kuliah where current dosen is assigned with statistics
        $mataKuliah = MataKuliah::with(['programStudi'])
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        // Add statistics to each mata kuliah
        foreach ($mataKuliah as $mk) {
            $mk->total_mahasiswa = $this->getTotalMahasiswaByMataKuliah($mk->id);
            $mk->belum_dinilai = $this->getBelumDinilaiByMataKuliah($mk->id, $user->id);
        }

        $data['mataKuliah'] = $mataKuliah;

        // Get rekap nilai if mata_kuliah_id is provided
        $rekapNilai = [];
        $selectedMataKuliah = null;
        if ($request->filled('mata_kuliah_id')) {
            $selectedMataKuliah = $mataKuliah->where('id', $request->mata_kuliah_id)->first();
            if ($selectedMataKuliah) {
                $rekapNilai = $this->getRekapNilaiData($request->mata_kuliah_id, $user->id);
            }
        }

        $data['rekapNilai'] = $rekapNilai;
        $data['selectedMataKuliah'] = $selectedMataKuliah;

        // Get dynamic bobot nilai settings for table headers
        $data['bobotNilaiSettings'] = BobotNilaiSetting::getActiveComponents();

        return view('central.backpage.dosen-rekap-nilai', $data, compact('user'));
    }

    // Helper methods for statistics - using centralized query logic
    private function getTotalMahasiswaByMataKuliah($mataKuliahId)
    {
        try {
            $mahasiswaData = $this->getMahasiswaDataByMataKuliah($mataKuliahId, 1); // Use dummy dosen ID for count
            return count($mahasiswaData);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getBelumDinilaiByMataKuliah($mataKuliahId, $dosenId)
    {
        try {
            $mahasiswaData = $this->getMahasiswaDataByMataKuliah($mataKuliahId, $dosenId);
            return count(array_filter($mahasiswaData, function($item) {
                return !$item['has_nilai'];
            }));
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Centralized method to get mahasiswa data - used by both statistics and API
    private function getMahasiswaDataByMataKuliah($mataKuliahId, $dosenId)
    {
        try {
            // Get mata kuliah info
            $mataKuliah = MataKuliah::find($mataKuliahId);
            if (!$mataKuliah) {
                return [];
            }

            $mahasiswaWithNilai = collect();

            // Strategy 1: Primary strategy - get students from program studi
            if ($mataKuliah->prodi_id) {
                $mahasiswaWithNilai = DB::table('mahasiswas as m')
                    ->leftJoin('program_studis as ps', 'm.prodi_id', '=', 'ps.id')
                    ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                    ->leftJoin('nilai_mahasiswa as nm', function($join) use ($mataKuliahId, $dosenId) {
                        $join->on('m.id', '=', 'nm.mahasiswa_id')
                             ->where('nm.mata_kuliah_id', '=', $mataKuliahId)
                             ->where('nm.dosen_id', '=', $dosenId);
                    })
                    ->where('m.prodi_id', $mataKuliah->prodi_id)
                    ->whereNotNull('m.name')
                    ->select(
                        'm.id',
                        'm.name',
                        'm.numb_nim as nim',
                        'm.prodi_id',
                        'm.kelas_id',
                        'm.semester',
                        'ps.name as program_studi_name',
                        'kl.name as kelas_name',
                        'nm.tugas',
                        'nm.quiz',
                        'nm.uts',
                        'nm.uas',
                        'nm.nilai_akhir',
                        'nm.huruf_nilai',
                        DB::raw('CASE WHEN (nm.nilai_akhir IS NOT NULL AND (nm.uts IS NOT NULL OR nm.uas IS NOT NULL OR nm.tugas IS NOT NULL)) THEN 1 ELSE 0 END as has_nilai')
                    )
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Strategy 2: If no students found from program studi, try KRS
            if ($mahasiswaWithNilai->isEmpty() && DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa')) {
                $mahasiswaWithNilai = DB::table('krs_detail_mahasiswa as kd')
                    ->join('krs_mahasiswa as km', 'kd.krs_mahasiswa_id', '=', 'km.id')
                    ->join('mahasiswas as m', 'km.mahasiswa_id', '=', 'm.id')
                    ->leftJoin('program_studis as ps', 'm.prodi_id', '=', 'ps.id')
                    ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                    ->leftJoin('nilai_mahasiswa as nm', function($join) use ($mataKuliahId, $dosenId) {
                        $join->on('m.id', '=', 'nm.mahasiswa_id')
                             ->where('nm.mata_kuliah_id', '=', $mataKuliahId)
                             ->where('nm.dosen_id', '=', $dosenId);
                    })
                    ->where('kd.mata_kuliah_id', $mataKuliahId)
                    ->whereNotNull('m.name')
                    ->select(
                        'm.id',
                        'm.name',
                        'm.numb_nim as nim',
                        'm.prodi_id',
                        'm.kelas_id',
                        'm.semester',
                        'ps.name as program_studi_name',
                        'kl.name as kelas_name',
                        'nm.tugas',
                        'nm.quiz',
                        'nm.uts',
                        'nm.uas',
                        'nm.nilai_akhir',
                        'nm.huruf_nilai',
                        DB::raw('CASE WHEN (nm.nilai_akhir IS NOT NULL AND (nm.uts IS NOT NULL OR nm.uas IS NOT NULL OR nm.tugas IS NOT NULL)) THEN 1 ELSE 0 END as has_nilai')
                    )
                    ->distinct()
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Strategy 3: If still no students, get students who already have grades for this course
            if ($mahasiswaWithNilai->isEmpty()) {
                $mahasiswaWithNilai = DB::table('nilai_mahasiswa as nm')
                    ->join('mahasiswas as m', 'nm.mahasiswa_id', '=', 'm.id')
                    ->leftJoin('program_studis as ps', 'm.prodi_id', '=', 'ps.id')
                    ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                    ->where('nm.mata_kuliah_id', $mataKuliahId)
                    ->where('nm.dosen_id', $dosenId)
                    ->whereNotNull('m.name')
                    ->select(
                        'm.id',
                        'm.name',
                        'm.numb_nim as nim',
                        'm.prodi_id',
                        'm.kelas_id',
                        'm.semester',
                        'ps.name as program_studi_name',
                        'kl.name as kelas_name',
                        'nm.tugas',
                        'nm.quiz',
                        'nm.uts',
                        'nm.uas',
                        'nm.nilai_akhir',
                        'nm.huruf_nilai',
                        DB::raw('1 as has_nilai')
                    )
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Strategy 4: Last fallback - get all students
            if ($mahasiswaWithNilai->isEmpty()) {
                $mahasiswaWithNilai = DB::table('mahasiswas as m')
                    ->leftJoin('program_studis as ps', 'm.prodi_id', '=', 'ps.id')
                    ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                    ->leftJoin('nilai_mahasiswa as nm', function($join) use ($mataKuliahId, $dosenId) {
                        $join->on('m.id', '=', 'nm.mahasiswa_id')
                             ->where('nm.mata_kuliah_id', '=', $mataKuliahId)
                             ->where('nm.dosen_id', '=', $dosenId);
                    })
                    ->whereNotNull('m.name')
                    ->select(
                        'm.id',
                        'm.name',
                        'm.numb_nim as nim',
                        'm.prodi_id',
                        'm.kelas_id',
                        'm.semester',
                        'ps.name as program_studi_name',
                        'kl.name as kelas_name',
                        'nm.tugas',
                        'nm.quiz',
                        'nm.uts',
                        'nm.uas',
                        'nm.nilai_akhir',
                        'nm.huruf_nilai',
                        DB::raw('CASE WHEN (nm.nilai_akhir IS NOT NULL AND (nm.uts IS NOT NULL OR nm.uas IS NOT NULL OR nm.tugas IS NOT NULL)) THEN 1 ELSE 0 END as has_nilai')
                    )
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Convert to array format for consistent processing with strict null filtering
            return $mahasiswaWithNilai->filter(function($item) {
                return $item &&
                       isset($item->id) &&
                       !empty($item->id) &&
                       isset($item->name) &&
                       !empty(trim($item->name));
            })->map(function($item) {
                return [
                    'id' => (int)$item->id,
                    'name' => trim($item->name),
                    'nim' => $item->nim ?? '',
                    'prodi_id' => $item->prodi_id,
                    'kelas_id' => $item->kelas_id,
                    'semester' => $item->semester,
                    'program_studi_name' => $item->program_studi_name ?? 'N/A',
                    'kelas_name' => $item->kelas_name ?? 'N/A',
                    'tugas' => $item->tugas,
                    'quiz' => $item->quiz,
                    'uts' => $item->uts,
                    'uas' => $item->uas,
                    'nilai_akhir' => $item->nilai_akhir,
                    'huruf_nilai' => $item->huruf_nilai ?? ($item->nilai_akhir !== null ? $this->getGradeLetter($item->nilai_akhir) : null),
                    'has_nilai' => (int)($item->has_nilai ?? 0),
                    'status' => $item->nilai_akhir !== null ? ($item->nilai_akhir >= 60 ? 'Lulus' : 'Tidak Lulus') : null
                ];
            })->values()->toArray();

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaDataByMataKuliah: ' . $e->getMessage());
            return [];
        }
    }

    public function renderUploadNilai()
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Upload Nilai";
        $data['pages'] = "Upload Nilai Excel";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get mata kuliah where current dosen is assigned
        $mataKuliah = MataKuliah::with(['programStudi'])
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $data['mataKuliah'] = $mataKuliah;
        $data['bobotNilaiSettings'] = BobotNilaiSetting::getActiveComponents();

        return view('central.backpage.dosen-upload-nilai', $data, compact('user'));
    }

    public function getMahasiswaByMataKuliah(Request $request)
    {
        try {
            $mataKuliahId = $request->mata_kuliah_id;
            $user = Auth::guard('dosen')->user();

            if (!$mataKuliahId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mata kuliah ID is required'
                ], 400);
            }

            // Verify that the dosen is assigned to this mata kuliah
            $mataKuliah = MataKuliah::with('programStudi')->where('id', $mataKuliahId)
                ->where(function($query) use ($user) {
                    $query->where('dosen1_id', $user->id)
                          ->orWhere('dosen2_id', $user->id)
                          ->orWhere('dosen3_id', $user->id);
                })
                ->first();

            if (!$mataKuliah) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to this mata kuliah'
                ], 403);
            }

            // Use centralized method to get consistent data
            $mahasiswaData = $this->getMahasiswaDataByMataKuliah($mataKuliahId, $user->id);

            return response()->json([
                'success' => true,
                'mahasiswa' => $mahasiswaData,
                'mata_kuliah' => [
                    'id' => $mataKuliah->id,
                    'name' => $mataKuliah->name,
                    'code' => $mataKuliah->code,
                    'bsks' => $mataKuliah->bsks ?? $mataKuliah->sks,
                    'prodi_id' => $mataKuliah->prodi_id,
                    'program_studi' => $mataKuliah->programStudi
                ],
                'debug' => [
                    'mata_kuliah_prodi_id' => $mataKuliah->prodi_id,
                    'mahasiswa_count' => count($mahasiswaData),
                    'program_studi' => $mataKuliah->programStudi->name ?? 'N/A',
                    'strategy_used' => count($mahasiswaData) > 0 ? 'success' : 'no_data',
                    'krs_table_exists' => DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaByMataKuliah: ' . $e->getMessage(), [
                'mata_kuliah_id' => $request->mata_kuliah_id ?? null,
                'user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch mahasiswa data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function handleInputNilai(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
                'mahasiswa_id' => 'required|exists:mahasiswas,id',
                'tugas' => 'nullable|numeric|min:0|max:100',
                'quiz' => 'nullable|numeric|min:0|max:100',
                'uts' => 'nullable|numeric|min:0|max:100',
                'uas' => 'nullable|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = Auth::guard('dosen')->user();
            $data = $validator->validated();

            // Verify that the dosen is assigned to this mata kuliah
            $mataKuliah = MataKuliah::where('id', $data['mata_kuliah_id'])
                ->where(function($query) use ($user) {
                    $query->where('dosen1_id', $user->id)
                          ->orWhere('dosen2_id', $user->id)
                          ->orWhere('dosen3_id', $user->id);
                })
                ->first();

            if (!$mataKuliah) {
                return redirect()->back()->with('error', 'Unauthorized access to this mata kuliah');
            }

            // Tangkap input, jika kosong ('') simpan sebagai null
            $tugas = isset($data['tugas']) && $data['tugas'] !== '' ? (float) $data['tugas'] : null;
            $quiz = isset($data['quiz']) && $data['quiz'] !== '' ? (float) $data['quiz'] : null;
            $uts = isset($data['uts']) && $data['uts'] !== '' ? (float) $data['uts'] : null;
            $uas = isset($data['uas']) && $data['uas'] !== '' ? (float) $data['uas'] : null;

            // Jika semua kolom input dikosongkan oleh dosen, kita asumsikan nilainya ingin di-reset
            $isAllEmpty = ($tugas === null && $quiz === null && $uts === null && $uas === null);

            if ($isAllEmpty) {
                // Menghapus record jika belum dinilai (reset)
                DB::table('nilai_mahasiswa')
                    ->where('mata_kuliah_id', $data['mata_kuliah_id'])
                    ->where('mahasiswa_id', $data['mahasiswa_id'])
                    ->where('dosen_id', $user->id)
                    ->delete();

                return redirect()->back()->with('success', 'Nilai mahasiswa berhasil di-reset / dikosongkan.');
            }

            // Get dynamic weights from settings
            $bobotSettings = BobotNilaiSetting::getWeightsArray();
            $activeComponents = BobotNilaiSetting::getActiveComponents();

            $currentValues = [
                'tugas' => $tugas,
                'quiz' => $quiz,
                'uts' => $uts,
                'uas' => $uas,
            ];

            // Cek apakah semua komponen aktif sudah memiliki nilai (bukan NULL)
            $allFilled = true;
            if ($activeComponents->isNotEmpty()) {
                foreach ($activeComponents as $component) {
                    $key = $component->component_key;
                    if ($currentValues[$key] === null) {
                        $allFilled = false;
                        break;
                    }
                }
            } else {
                // Fallback jika tidak ada setting (minimal UTS/UAS)
                $allFilled = ($uts !== null && $uas !== null);
            }

            if ($allFilled) {
                // Calculate final grade
                $nilaiAkhir = 0;
                if ($activeComponents->isNotEmpty()) {
                    foreach ($activeComponents as $component) {
                        $key = $component->component_key;
                        $weight = ($bobotSettings[$key] ?? 0) / 100;
                        $nilaiAkhir += ($currentValues[$key] * $weight);
                    }
                } else {
                    // Fallback weights
                    $weightTugas = ($bobotSettings['tugas'] ?? 20) / 100;
                    $weightQuiz = ($bobotSettings['quiz'] ?? 20) / 100;
                    $weightUts = ($bobotSettings['uts'] ?? 30) / 100;
                    $weightUas = ($bobotSettings['uas'] ?? 30) / 100;

                    if ($tugas !== null) $nilaiAkhir += ($tugas * $weightTugas);
                    if ($quiz !== null) $nilaiAkhir += ($quiz * $weightQuiz);
                    if ($uts !== null) $nilaiAkhir += ($uts * $weightUts);
                    if ($uas !== null) $nilaiAkhir += ($uas * $weightUas);
                }

                // Determine grade letter
                $hurufNilai = $this->getGradeLetter($nilaiAkhir);
            } else {
                // Jika belum lengkap, nilai_akhir dan huruf_nilai tetap NULL
                $nilaiAkhir = null;
                $hurufNilai = null;
            }

            $gradeData = [
                'mata_kuliah_id' => $data['mata_kuliah_id'],
                'mahasiswa_id' => $data['mahasiswa_id'],
                'dosen_id' => $user->id,
                'tugas' => $tugas,
                'quiz' => $quiz,
                'uts' => $uts,
                'uas' => $uas,
                'nilai_akhir' => $nilaiAkhir,
                'huruf_nilai' => $hurufNilai,
                'updated_at' => now(),
            ];

            // Menyimpan/mengupdate ke database
            DB::table('nilai_mahasiswa')->updateOrInsert(
                [
                    'mata_kuliah_id' => $data['mata_kuliah_id'],
                    'mahasiswa_id' => $data['mahasiswa_id']
                ],
                array_merge($gradeData, ['created_at' => now()])
            );

            return redirect()->back()->with('success', 'Nilai berhasil disimpan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to save grade: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function downloadUploadTemplate(Request $request)
    {
        $user = Auth::guard('dosen')->user();
        $mataKuliahId = $request->query('mata_kuliah_id');

        if (!$mataKuliahId) {
            return redirect()->back()->with('error', 'Silakan pilih mata kuliah terlebih dahulu.');
        }

        $mataKuliah = MataKuliah::with('programStudi')
            ->where('id', $mataKuliahId)
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->first();

        if (!$mataKuliah) {
            return redirect()->back()->with('error', 'Unauthorized access to this mata kuliah');
        }

        $components = BobotNilaiSetting::getActiveComponents();

        if ($components->isEmpty()) {
            return redirect()->back()->with('error', 'Komponen bobot nilai belum diset oleh admin.');
        }

        $students = collect($this->getMahasiswaDataByMataKuliah($mataKuliahId, $user->id))
            ->map(function ($item) use ($components) {
                $kelas = $item['kelas_name'] ?? 'Tanpa Kelas';
                $studentData = [
                    'name' => $item['name'] ?? '',
                    'numb_nim' => $item['nim'] ?? '',
                    'kelas_full_name' => $kelas,
                    'semester' => $item['semester'] ?? '',
                ];
                foreach ($components as $component) {
                    $key = $component->component_key;
                    $studentData[$key] = $item[$key] ?? '';
                }
                return (object) $studentData;
            });

        $groupedStudents = $students->groupBy(function ($student) {
            return $student->kelas_full_name ?: 'Tanpa Kelas';
        });

        $filename = 'template-nilai-per-kelas-' . preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower($mataKuliah->name)) . '-' . date('Ymd-His') . '.xlsx';

        return Excel::download(new DosenNilaiTemplateByClassExport($groupedStudents, $components), $filename);
    }

    public function handleUploadExcel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
                'excel_file' => 'required|file|mimes:xlsx|max:5120',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = Auth::guard('dosen')->user();
            $mataKuliahId = $request->mata_kuliah_id;

            $mataKuliah = MataKuliah::where('id', $mataKuliahId)
                ->where(function($query) use ($user) {
                    $query->where('dosen1_id', $user->id)
                          ->orWhere('dosen2_id', $user->id)
                          ->orWhere('dosen3_id', $user->id);
                })
                ->first();

            if (!$mataKuliah) {
                return redirect()->back()->with('error', 'Unauthorized access to this mata kuliah');
            }

            $components = BobotNilaiSetting::getActiveComponents();
            if ($components->isEmpty()) {
                return redirect()->back()->with('error', 'Komponen bobot nilai belum diset oleh admin.');
            }

            $sheets = Excel::toArray([], $request->file('excel_file'));
            if (empty($sheets)) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak dapat dibaca.');
            }

            $componentKeys = $components->pluck('component_key')->map(fn($k) => strtolower($k))->toArray();
            $weights = BobotNilaiSetting::getWeightsArray();

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($sheets as $sheetIndex => $sheetRows) {
                if (empty($sheetRows) || count($sheetRows) < 2) {
                    continue;
                }

                // EKSTRAK HEADER: Baca teks dalam kurung jika formatnya "Tugas (tugas)"
                $header = array_map(function ($h) {
                    $cleanStr = strtolower(trim((string) $h));
                    if (preg_match('/\((.*?)\)$/', $cleanStr, $matches)) {
                        return trim($matches[1]);
                    }
                    return $cleanStr;
                }, $sheetRows[0]);

                $requiredBaseColumns = ['nama_mahasiswa', 'nim', 'kelas', 'semester'];
                foreach ($requiredBaseColumns as $baseColumn) {
                    if (!in_array($baseColumn, $header, true)) {
                        $errors[] = "Sheet #" . ($sheetIndex + 1) . ": Kolom wajib '{$baseColumn}' tidak ditemukan.";
                        $errorCount++;
                        continue 2;
                    }
                }

                foreach ($componentKeys as $componentKey) {
                    if (!in_array($componentKey, $header, true)) {
                        $errors[] = "Sheet #" . ($sheetIndex + 1) . ": Kolom komponen '{$componentKey}' tidak ditemukan.";
                        $errorCount++;
                        continue 2;
                    }
                }

                $headerIndex = array_flip($header);

                foreach (array_slice($sheetRows, 1) as $rowIndex => $row) {
                    $excelRowNumber = $rowIndex + 2;

                    if (empty(array_filter($row, fn($v) => $v !== null && trim((string) $v) !== ''))) {
                        continue;
                    }

                    $nimValue = trim((string) ($row[$headerIndex['nim']] ?? ''));
                    if ($nimValue === '') {
                        $errors[] = "Sheet #" . ($sheetIndex + 1) . " Baris {$excelRowNumber}: NIM kosong.";
                        $errorCount++;
                        continue;
                    }

                    $mahasiswa = Mahasantri::where('numb_nim', $nimValue)->first();
                    if (!$mahasiswa) {
                        $errors[] = "Sheet #" . ($sheetIndex + 1) . " Baris {$excelRowNumber}: Mahasantri dengan NIM {$nimValue} tidak ditemukan.";
                        $errorCount++;
                        continue;
                    }

                    $componentValues = [];
                    $invalidValue = false;
                    $isAllEmpty = true; // Penanda kalau baris ini komponen nilainya kosong semua

                    foreach ($componentKeys as $componentKey) {
                        $rawVal = $row[$headerIndex[$componentKey]] ?? null;
                        $rawVal = is_null($rawVal) ? '' : trim((string) $rawVal);

                        if ($rawVal === '') {
                            $componentValues[$componentKey] = null; // Simpan sbg null
                            continue;
                        }

                        if (!is_numeric($rawVal)) {
                            $errors[] = "Sheet #" . ($sheetIndex + 1) . " Baris {$excelRowNumber}: Nilai '{$componentKey}' harus angka.";
                            $errorCount++;
                            $invalidValue = true;
                            break;
                        }

                        $num = (float) $rawVal;
                        if ($num < 0 || $num > 100) {
                            $errors[] = "Sheet #" . ($sheetIndex + 1) . " Baris {$excelRowNumber}: Nilai '{$componentKey}' harus 0-100.";
                            $errorCount++;
                            $invalidValue = true;
                            break;
                        }

                        $componentValues[$componentKey] = $num;
                        $isAllEmpty = false; // Jika ada nilai (termasuk nol), flag ganti ke false
                    }

                    if ($invalidValue) {
                        continue;
                    }

                    // Jika dosen tidak mengisi apa pun untuk mahasiswa ini, lompati
                    if ($isAllEmpty) {
                        continue;
                    }

                    $nilaiAkhir = 0;
                    $allFilled = true;
                    foreach ($componentKeys as $componentKey) {
                        if ($componentValues[$componentKey] === null) {
                            $allFilled = false;
                            break;
                        }
                    }

                    if ($allFilled) {
                        foreach ($componentValues as $key => $value) {
                            $weight = ((float) ($weights[$key] ?? 0)) / 100;
                            $nilaiAkhir += ($value * $weight);
                        }
                        $hurufNilai = $this->getGradeLetter($nilaiAkhir);
                    } else {
                        $nilaiAkhir = null;
                        $hurufNilai = null;
                    }

                    $gradeData = [
                        'mata_kuliah_id' => $mataKuliahId,
                        'mahasiswa_id' => $mahasiswa->id,
                        'dosen_id' => $user->id,
                        'nilai_akhir' => $nilaiAkhir,
                        'huruf_nilai' => $hurufNilai,
                        'updated_at' => now(),
                    ];

                    foreach ($componentKeys as $componentKey) {
                        $gradeData[$componentKey] = $componentValues[$componentKey];
                    }

                    if (!isset($gradeData['created_at'])) {
                        $gradeData['created_at'] = now();
                    }

                    DB::table('nilai_mahasiswa')->updateOrInsert(
                        [
                            'mata_kuliah_id' => $mataKuliahId,
                            'mahasiswa_id' => $mahasiswa->id,
                            'dosen_id' => $user->id,
                        ],
                        $gradeData
                    );

                    $successCount++;
                }
            }

            DB::commit();

            $message = "Import Excel selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(' | ', array_slice($errors, 0, 5));
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to upload nilai excel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to upload Excel: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getRekapNilaiData($mataKuliahId, $dosenId)
    {
        try {
            $nilaiData = DB::table('nilai_mahasiswa as n')
                ->join('mahasiswas as m', 'n.mahasiswa_id', '=', 'm.id')
                ->where('n.mata_kuliah_id', $mataKuliahId)
                ->where('n.dosen_id', $dosenId)
                ->select(
                    'm.id',
                    'm.numb_nim as nim',
                    'm.name as nama',
                    'n.tugas',
                    'n.quiz',
                    'n.uts',
                    'n.uas',
                    'n.nilai_akhir',
                    'n.huruf_nilai as huruf',
                    DB::raw('CASE WHEN n.nilai_akhir IS NOT NULL THEN (CASE WHEN n.nilai_akhir >= 60 THEN "Lulus" ELSE "Tidak Lulus" END) ELSE NULL END as status')
                )
                ->orderBy('m.name', 'asc')
                ->get();

            return $nilaiData->toArray();

        } catch (\Exception $e) {
            return [];
        }
    }

    public function getExistingGrades(Request $request)
    {
        try {
            $mataKuliahId = $request->mata_kuliah_id;
            $mahasiswaId = $request->mahasiswa_id;
            $user = Auth::guard('dosen')->user();

            $mataKuliah = MataKuliah::where('id', $mataKuliahId)
                ->where(function($query) use ($user) {
                    $query->where('dosen1_id', $user->id)
                          ->orWhere('dosen2_id', $user->id)
                          ->orWhere('dosen3_id', $user->id);
                })
                ->first();

            if (!$mataKuliah) {
                return response()->json(['error' => 'Unauthorized access to this mata kuliah'], 403);
            }

            $existingGrades = DB::table('nilai_mahasiswa')
                ->where('mata_kuliah_id', $mataKuliahId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('dosen_id', $user->id)
                ->first();

            if ($existingGrades) {
                return response()->json([
                    'success' => true,
                    'nilai' => [
                        'tugas' => $existingGrades->tugas,
                        'quiz' => $existingGrades->quiz,
                        'uts' => $existingGrades->uts,
                        'uas' => $existingGrades->uas,
                        'nilai_akhir' => $existingGrades->nilai_akhir,
                        'huruf_nilai' => $existingGrades->huruf_nilai
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No existing grades found'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch existing grades: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getGradeLetter($score)
    {
        try {
            return GradeScaleSetting::convertToLetterGrade($score);
        } catch (\Exception $e) {
            if ($score >= 85) return 'A';
            if ($score >= 80) return 'A-';
            if ($score >= 75) return 'B+';
            if ($score >= 70) return 'B';
            if ($score >= 65) return 'B-';
            if ($score >= 60) return 'C+';
            if ($score >= 55) return 'C';
            if ($score >= 50) return 'C-';
            if ($score >= 45) return 'D+';
            if ($score >= 40) return 'D';
            return 'E';
        }
    }
}
