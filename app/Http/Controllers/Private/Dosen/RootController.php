<?php

namespace App\Http\Controllers\Private\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// USE MODELS
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\MataKuliah;
use App\Models\NilaiMahasiswa;
use App\Models\Mahasantri;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RootController extends Controller
{
    public function renderDashboard()
    {
        $user = Auth::guard('dosen')->user();
        
        // Load mahasiswa bimbingan relationship
        if ($user) {
            $user = \App\Models\Dosen::with(['mahasiswaBimbingan.prodi', 'mahasiswaBimbingan.kelas'])->find($user->id);
        }
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Dashboard";
        $data['pages'] = "Dashboard Ustadz";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // STATISTIK DASHBOARD REAL DARI DATABASE - Fixed and improved logic
        if ($user) {
            // Initialize default values first
            $data['total_mata_kuliah'] = 0;
            $data['total_mahasantri'] = 0;
            $data['total_mahasantri_sudah_krs'] = 0;
            $data['total_mahasantri_belum_krs'] = 0;
            $data['tugas_pending'] = 0;
            $data['jadwal_hari_ini'] = collect();
            $data['mata_kuliah_diampu'] = collect();
            $data['tugas_terbaru'] = collect();
            $data['progress_mata_kuliah'] = 0;
            $data['progress_mahasantri'] = 0;
            $data['progress_tugas'] = 0;
            $data['progress_jadwal'] = 0;
            
            try {
                // Get mata kuliah where current dosen is assigned as dosen1, dosen2, or dosen3
                $mataKuliah = \App\Models\Akademik\MataKuliah::with(['programStudi', 'kurikulum', 'dosen1', 'dosen2', 'dosen3'])
                    ->where(function($query) use ($user) {
                        $query->where('dosen1_id', $user->id)
                              ->orWhere('dosen2_id', $user->id)
                              ->orWhere('dosen3_id', $user->id);
                    })
                    ->orderBy('name', 'asc')
                    ->get();

                $data['total_mata_kuliah'] = $mataKuliah->count();
                $data['mata_kuliah_diampu'] = $mataKuliah;
                
                // Ambil ID mata kuliah untuk query lainnya
                $mataKuliahIds = $mataKuliah->pluck('id');
                
                // Kembali ke logika asli yang berfungsi, hanya tambahkan breakdown KRS
                $totalMahasantri = 0;
                $totalMahasantriSudahKRS = 0;
                $totalMahasantriBelumKRS = 0;
                
                if ($mataKuliahIds->count() > 0) {
                    // Cek dari KRS Detail Mahasiswa - gunakan krs_mahasiswa_id untuk mendapatkan mahasiswa_id yang unik
                    if (DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa')) {
                        // Get unique mahasiswa IDs from KRS mahasiswa table
                        $krsIds = DB::table('krs_detail_mahasiswa')
                            ->whereIn('mata_kuliah_id', $mataKuliahIds)
                            ->pluck('krs_mahasiswa_id')
                            ->unique();
                        
                        if (DB::getSchemaBuilder()->hasTable('krs_mahasiswa')) {
                            $totalMahasantriSudahKRS = DB::table('krs_mahasiswa')
                                ->whereIn('id', $krsIds)
                                ->distinct('mahasiswa_id')
                                ->count('mahasiswa_id');
                        } else {
                            $totalMahasantriSudahKRS = $krsIds->count();
                        }
                    }
                    
                    // Untuk total mahasiswa, gunakan logika yang sama seperti sebelumnya
                    $totalMahasantri = $totalMahasantriSudahKRS;
                    
                    // Tambahkan mahasiswa dari program studi yang belum mengisi KRS
                    $prodiIds = $mataKuliah->pluck('prodi_id')->unique()->filter();
                    if ($prodiIds->count() > 0) {
                        // Hitung total mahasiswa di program studi tersebut (tanpa filter status karena kolom tidak ada)
                        $allMahasiswaInProdi = DB::table('mahasiswas')
                            ->whereIn('prodi_id', $prodiIds)
                            ->count();
                        
                        // Jika ada lebih banyak mahasiswa di prodi daripada yang sudah KRS
                        if ($allMahasiswaInProdi > $totalMahasantriSudahKRS) {
                            $totalMahasantri = $allMahasiswaInProdi;
                            $totalMahasantriBelumKRS = $allMahasiswaInProdi - $totalMahasantriSudahKRS;
                        }
                    }
                }
                
                $data['total_mahasantri'] = $totalMahasantri;
                $data['total_mahasantri_sudah_krs'] = $totalMahasantriSudahKRS;
                $data['total_mahasantri_belum_krs'] = $totalMahasantriBelumKRS;
                
                // Nilai pending - optimized to query once per course
                $coursesMahasiswa = [];
                $nilaiPending = 0;
                if ($mataKuliahIds->count() > 0) {
                    foreach ($mataKuliahIds as $mataKuliahId) {
                        $mahasiswaData = $this->getMahasiswaDataByMataKuliah($mataKuliahId, $user->id);
                        $coursesMahasiswa[$mataKuliahId] = $mahasiswaData;
                        $nilaiPending += count(array_filter($mahasiswaData, function($item) {
                            return !$item['has_nilai'];
                        }));
                    }
                }
                $data['tugas_pending'] = $nilaiPending;
                
                // Jadwal hari ini - improved logic
                $today = now()->format('l');
                $hariIndonesia = [
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa', 
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu'
                ];
                $hariIni = $hariIndonesia[$today] ?? 'Senin';
                
                $jadwalHariIni = collect();
                if (DB::getSchemaBuilder()->hasTable('jadwal_kuliahs')) {
                    try {
                        $jadwalHariIni = \App\Models\Akademik\JadwalKuliah::with(['mataKuliah', 'ruang'])
                            ->where('dosen_id', $user->id)
                            ->where('hari', $hariIni)
                            ->get();
                    } catch (\Exception $e) {
                        Log::warning('Jadwal kuliah query error: ' . $e->getMessage());
                        $jadwalHariIni = collect();
                    }
                }
                $data['jadwal_hari_ini'] = $jadwalHariIni;
                
                // REAL DATA QUERY - Menggunakan logika yang sama dengan rekap nilai
                $data['tugas_terbaru'] = collect();
                
                if ($mataKuliahIds->count() > 0) {
                    try {
                        Log::info('REAL DATA QUERY - Mengambil data mahasiswa pending nilai (NEW LOGIC):', [
                            'mata_kuliah_ids' => $mataKuliahIds->toArray(),
                            'pending_count' => $data['tugas_pending']
                        ]);
                        
                        // Reuse query results to avoid duplicate database calls
                        foreach ($mataKuliahIds as $mataKuliahId) {
                            $mahasiswaData = $coursesMahasiswa[$mataKuliahId] ?? [];
                            
                            // Filter hanya mahasiswa yang belum ada nilai
                            $mahasiswaBelumNilai = array_filter($mahasiswaData, function($item) {
                                return !$item['has_nilai'];
                            });
                            
                            // Ambil mata kuliah info
                            $currentMataKuliah = $mataKuliah->where('id', $mataKuliahId)->first();
                            
                            // Transform data ke format yang dibutuhkan blade
                            foreach (array_slice($mahasiswaBelumNilai, 0, 5) as $mahasiswa) { // Batasi 5 per mata kuliah
                                // Extract angkatan dari 4 digit pertama NIM
                                $angkatan = '2024'; // default
                                if ($mahasiswa['nim'] && strlen($mahasiswa['nim']) >= 4) {
                                    $firstFour = substr($mahasiswa['nim'], 0, 4);
                                    // Cek apakah 4 digit pertama adalah tahun yang valid (2000-2030)
                                    if (is_numeric($firstFour) && $firstFour >= 2000 && $firstFour <= 2030) {
                                        $angkatan = $firstFour;
                                    }
                                }
                                
                                $data['tugas_terbaru']->push((object)[
                                    'mahasantri' => (object)[
                                        'id' => $mahasiswa['id'],
                                        'name' => $mahasiswa['name'],
                                        'nim' => $mahasiswa['nim'],
                                        'angkatan' => $angkatan
                                    ],
                                    'mataKuliah' => (object)[
                                        'id' => $currentMataKuliah->id,
                                        'name' => $currentMataKuliah->name ?? 'Mata Kuliah',
                                        'code' => $currentMataKuliah->code ?? 'MK'
                                    ],
                                    'prodi' => (object)[
                                        'name' => $mahasiswa['program_studi_name'] ?? 'Program Studi'
                                    ],
                                    'kelas_info' => ($mahasiswa['program_studi_name'] ?? 'Program Studi') . ' - Angkatan ' . $angkatan,
                                    'status' => 'Belum input nilai akhir',
                                    'created_at' => now()
                                ]);
                            }
                        }
                        
                        Log::info('REAL DATA QUERY - Berhasil mengambil data (NEW LOGIC):', [
                            'tugas_terbaru_count' => $data['tugas_terbaru']->count(),
                            'sample_data' => $data['tugas_terbaru']->take(2)->map(function($item) {
                                return [
                                    'mahasiswa' => $item->mahasantri->name,
                                    'nim' => $item->mahasantri->nim,
                                    'mata_kuliah' => $item->mataKuliah->name
                                ];
                            })->toArray()
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('REAL DATA QUERY - Error mengambil data (NEW LOGIC):', [
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]);
                        
                        // Fallback ke data kosong jika ada error
                        $data['tugas_terbaru'] = collect();
                    }
                } else {
                    Log::info('REAL DATA QUERY - Tidak ada mata kuliah:', [
                        'mata_kuliah_count' => $mataKuliahIds->count()
                    ]);
                }
                
                // Perhitungan persentase untuk progress bar - improved
                $totalJadwalHariIni = $data['jadwal_hari_ini']->count();
                $data['progress_mata_kuliah'] = $data['total_mata_kuliah'] > 0 ? min(($data['total_mata_kuliah'] / 10) * 100, 100) : 0;
                $data['progress_mahasantri'] = $data['total_mahasantri'] > 0 ? min(($data['total_mahasantri'] / 100) * 100, 100) : 0;
                $data['progress_tugas'] = $data['tugas_pending'] > 0 ? min(($data['tugas_pending'] / 50) * 100, 100) : 0;
                $data['progress_jadwal'] = $totalJadwalHariIni > 0 ? min(($totalJadwalHariIni / 5) * 100, 100) : 0;
                
                // Enhanced debug logging with more details
                Log::info('Dashboard Dosen Data:', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'mata_kuliah_count' => $data['total_mata_kuliah'],
                    'mata_kuliah_ids' => $mataKuliahIds->toArray(),
                    'mata_kuliah_names' => $mataKuliah->pluck('name')->toArray(),
                    'mahasantri_total' => $data['total_mahasantri'],
                    'mahasantri_sudah_krs' => $data['total_mahasantri_sudah_krs'],
                    'mahasantri_belum_krs' => $data['total_mahasantri_belum_krs'],
                    'pending_count' => $data['tugas_pending'],
                    'jadwal_count' => $totalJadwalHariIni,
                    'tugas_terbaru_count' => $data['tugas_terbaru']->count(),
                    'pending_vs_terbaru' => [
                        'nilai_pending_total' => $nilaiPending,
                        'tugas_terbaru_count' => $data['tugas_terbaru']->count(),
                        'should_match' => $nilaiPending > 0 ? 'Yes' : 'No',
                        'has_pending_data' => $data['tugas_terbaru']->count() > 0 ? 'Yes' : 'No'
                    ],
                    'query_validation' => [
                        'krs_table_exists' => DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa'),
                        'nilai_table_exists' => DB::getSchemaBuilder()->hasTable('nilai_mahasiswa'),
                        'mahasiswa_table_exists' => DB::getSchemaBuilder()->hasTable('mahasiswas')
                    ]
                ]);
                
            } catch (\Exception $e) {
                // Log error dengan detail lebih lengkap
                Log::error('Dashboard Dosen Error: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Keep default values that were already set above
                // Don't override them here to prevent data loss
            }
        } else {
            // Default values jika user tidak login
            $data['total_mata_kuliah'] = 0;
            $data['total_mahasantri'] = 0;
            $data['total_mahasantri_sudah_krs'] = 0;
            $data['total_mahasantri_belum_krs'] = 0;
            $data['tugas_pending'] = 0;
            $data['jadwal_hari_ini'] = collect();
            $data['mata_kuliah_diampu'] = collect();
            $data['tugas_terbaru'] = collect();
            $data['progress_mata_kuliah'] = 0;
            $data['progress_mahasantri'] = 0;
            $data['progress_tugas'] = 0;
            $data['progress_jadwal'] = 0;
        }

        // Log final data with new implementation
        Log::info('Dashboard Dosen - SIMPLE FIX Result:', [
            'tugas_pending' => $data['tugas_pending'],
            'tugas_terbaru_count' => $data['tugas_terbaru']->count(),
            'mata_kuliah_count' => $data['mata_kuliah_diampu']->count(),
            'implementation' => 'SIMPLE_FIX_ACTIVE'
        ]);
        
        return view('central.backpage.dashboard-dosen', array_merge($data, compact('user')));
    }

    // Helper methods untuk menggunakan logika yang sama dengan rekap nilai
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

    // Centralized method to get mahasiswa data - menggunakan logika yang sama dengan NilaiController
    private function getMahasiswaDataByMataKuliah($mataKuliahId, $dosenId)
    {
        try {
            // Get mata kuliah info
            $mataKuliah = MataKuliah::find($mataKuliahId);
            if (!$mataKuliah) {
                return [];
            }

            $mahasiswaWithNilai = collect();
            
            // Strategy 1: Primary strategy - get students from program studi (this ensures we get all 88 students)
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
                        DB::raw('CASE WHEN nm.nilai_akhir IS NOT NULL THEN 1 ELSE 0 END as has_nilai')
                    )
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Strategy 2: If no students found from program studi, try KRS (Course Registration System)
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
                        DB::raw('CASE WHEN nm.nilai_akhir IS NOT NULL THEN 1 ELSE 0 END as has_nilai')
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
                        DB::raw('CASE WHEN nm.nilai_akhir IS NOT NULL THEN 1 ELSE 0 END as has_nilai')
                    )
                    ->orderBy('m.name', 'asc')
                    ->get();
            }

            // Convert to array format for consistent processing with strict null filtering
            return $mahasiswaWithNilai->filter(function($item) {
                // Filter out null/invalid entries
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
                    'tugas' => $item->tugas ?? 0,
                    'quiz' => $item->quiz ?? 0,
                    'uts' => $item->uts ?? 0,
                    'uas' => $item->uas ?? 0,
                    'nilai_akhir' => $item->nilai_akhir ?? 0,
                    'huruf_nilai' => $item->huruf_nilai ?? $this->getGradeLetter($item->nilai_akhir ?? 0),
                    'has_nilai' => (int)($item->has_nilai ?? 0),
                    'status' => $item->nilai_akhir >= 60 ? 'Lulus' : ($item->nilai_akhir > 0 ? 'Tidak Lulus' : null)
                ];
            })->values()->toArray(); // Use values() to reset array keys after filtering

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaDataByMataKuliah: ' . $e->getMessage());
            return [];
        }
    }

    private function getGradeLetter($score)
    {
        try {
            return \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($score);
        } catch (\Exception $e) {
            // Fallback to hardcoded values if GradeScaleSetting fails
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

    public function renderProfile()
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Detail";
        $data['pages'] = "Profile Dosen";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        return view('central.backpage.profile-dosen', $data, compact('user'));
    }

    public function handleProfile(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                // Personal Information
                'name' => 'required|string|max:255',
                'title_front' => 'nullable|string|max:50',
                'title_behind' => 'nullable|string|max:50',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'bio_placebirth' => 'nullable|string|max:100',
                'bio_datebirth' => 'nullable|date',
                'bio_gender' => 'nullable|in:Laki-laki,Perempuan',
                'bio_religion' => 'nullable|string|max:50',
                'bio_nationality' => 'nullable|string|max:50',
                'bio_blood' => 'nullable|string|max:5',
                'bio_height' => 'nullable|numeric',
                'bio_weight' => 'nullable|numeric',

                // Contact Information
                'email' => 'required|email|unique:dosens,email,' . Auth::guard('dosen')->user()->id,
                'phone' => 'required|string|unique:dosens,phone,' . Auth::guard('dosen')->user()->id,
                'link_ig' => 'nullable|url',
                'link_fb' => 'nullable|url',
                'link_in' => 'nullable|url',

                // Address Information
                'ktp_addres' => 'nullable|string',
                'ktp_rt' => 'nullable|string|max:10',
                'ktp_rw' => 'nullable|string|max:10',
                'ktp_village' => 'nullable|string|max:100',
                'ktp_subdistrict' => 'nullable|string|max:100',
                'ktp_city' => 'nullable|string|max:100',
                'ktp_province' => 'nullable|string|max:100',
                'ktp_poscode' => 'nullable|string|max:10',
                'domicile_same' => 'required|in:Yes,No',
                'domicile_addres' => 'nullable|required_if:domicile_same,No|string',
                'domicile_rt' => 'nullable|required_if:domicile_same,No|string|max:10',
                'domicile_rw' => 'nullable|required_if:domicile_same,No|string|max:10',
                'domicile_village' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_subdistrict' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_city' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_province' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_poscode' => 'nullable|required_if:domicile_same,No|string|max:10',

                // Education Information
                'edu1_type' => 'required|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu1_place' => 'required|string|max:255',
                'edu1_major' => 'required|string|max:255',
                'edu1_average_score' => 'required|string|max:10',
                'edu1_graduate_year' => 'required|string|max:4',
                'edu2_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu2_place' => 'nullable|string|max:255',
                'edu2_major' => 'nullable|string|max:255',
                'edu2_average_score' => 'nullable|string|max:10',
                'edu2_graduate_year' => 'nullable|string|max:4',
                'edu3_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu3_place' => 'nullable|string|max:255',
                'edu3_major' => 'nullable|string|max:255',
                'edu3_average_score' => 'nullable|string|max:10',
                'edu3_graduate_year' => 'nullable|string|max:4',

                // Identity Information
                'numb_kk' => 'nullable|string|max:20',
                'numb_ktp' => 'nullable|string|max:20',
                'numb_npsn' => 'nullable|string|max:20',
                'numb_nidn' => 'nullable|string|max:20',
                'numb_nitk' => 'nullable|string|max:20',
                'numb_staff' => 'nullable|string|max:20',
            ], [
                // Custom messages untuk field yang lebih user-friendly
                'name.required' => 'Nama lengkap wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.unique' => 'Nomor telepon sudah digunakan',
                
                // Custom messages untuk domicile fields
                'domicile_addres.required_if' => 'Alamat domisili wajib diisi jika berbeda dengan KTP',
                'domicile_rt.required_if' => 'RT domisili wajib diisi jika berbeda dengan KTP',
                'domicile_rw.required_if' => 'RW domisili wajib diisi jika berbeda dengan KTP',
                'domicile_village.required_if' => 'Kelurahan domisili wajib diisi jika berbeda dengan KTP',
                'domicile_subdistrict.required_if' => 'Kecamatan domisili wajib diisi jika berbeda dengan KTP',
                'domicile_city.required_if' => 'Kota domisili wajib diisi jika berbeda dengan KTP',
                'domicile_province.required_if' => 'Provinsi domisili wajib diisi jika berbeda dengan KTP',
                'domicile_poscode.required_if' => 'Kode pos domisili wajib diisi jika berbeda dengan KTP',
                
                // Custom messages untuk pendidikan
                'edu1_type.required' => 'Jenis pendidikan 1 wajib dipilih',
                'edu1_place.required' => 'Institusi pendidikan 1 wajib diisi',
                'edu1_major.required' => 'Jurusan pendidikan 1 wajib diisi',
                'edu1_average_score.required' => 'IPK pendidikan 1 wajib diisi',
                'edu1_graduate_year.required' => 'Tahun lulus pendidikan 1 wajib diisi',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $dosen = Auth::guard('dosen')->user();
            $data = $validator->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($dosen->photo && $dosen->photo !== 'default.jpg') {
                    Storage::disk('public')->delete('images/profile/' . $dosen->photo);
                }

                // Kompres dan simpan foto profil
                $photoName = time() . '-' . $dosen->code . '-' . uniqid() . '-' . uniqid() . '.jpg';
                
                // Buat instance ImageManager dengan driver GD
                $manager = new ImageManager(new Driver());
                
                // Baca dan kompres gambar
                $image = $manager->read($request->photo->getRealPath());
                
                // Resize dengan ukuran yang lebih besar untuk foto profil
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200);
                }
                
                // Simpan dengan kualitas tinggi (90%)
                Storage::disk('public')->put('images/profile/' . $photoName, $image->toJpeg(90));
                
                $data['photo'] = $photoName;
            }

            // Update dosen information
            $dosen->update($data);

            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
