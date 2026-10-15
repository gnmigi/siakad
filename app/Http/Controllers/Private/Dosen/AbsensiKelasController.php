<?php

namespace App\Http\Controllers\Private\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\PertemuanKuliah;
use App\Models\Akademik\AbsensiMahasiswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DosenAbsensiExport;

class AbsensiKelasController extends Controller
{
    public function index()
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Absensi Kelas";
        $data['pages'] = "Daftar Mata Kuliah";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        $data['mataKuliah'] = MataKuliah::with(['programStudi'])
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)
                      ->orWhere('dosen2_id', $user->id)
                      ->orWhere('dosen3_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('central.backpage.dosen-absensi-kelas-index', $data, compact('user'));
    }

    public function show(Request $request, $mata_kuliah_id)
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Isi Absensi";
        $data['pages'] = "Absensi Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        $mataKuliah = MataKuliah::where('id', $mata_kuliah_id)
            ->where(function($query) use ($user) {
                $query->where('dosen1_id', $user->id)->orWhere('dosen2_id', $user->id)->orWhere('dosen3_id', $user->id);
            })->firstOrFail();

        $data['mataKuliah'] = $mataKuliah;
        $pertemuan_ke = $request->query('pertemuan_ke', 1);
        $data['pertemuan_ke'] = $pertemuan_ke;

        // TANGKAP TOGGLE KRS
        $onlyKrs = $request->query('krs_only') == '1';
        $data['krs_only'] = $onlyKrs;

        $pertemuansRaw = PertemuanKuliah::where('mata_kuliah_id', $mata_kuliah_id)
            ->where('pertemuan_ke', $pertemuan_ke)
            ->get();

        // PANGGIL MAHASISWA DENGAN FILTER KRS
        $mahasiswaList = $this->getMahasiswaDataByMataKuliah($mata_kuliah_id, $onlyKrs);

        $absensiData = [];
        if ($pertemuansRaw->isNotEmpty()) {
            $absensiData = AbsensiMahasiswa::whereIn('pertemuan_id', $pertemuansRaw->pluck('id'))
                ->pluck('status', 'mahasiswa_id')
                ->toArray();
        }

        $groupedMahasiswa = $mahasiswaList->map(function ($mhs) use ($absensiData) {
            $mhs->status_absen = $absensiData[$mhs->id] ?? 'Hadir';
            $kelas = $mhs->kelas_name ?? 'Tanpa Kelas';
            $semester = $mhs->semester ? ' (Semester Aktif ' . $mhs->semester . ')' : '';
            $mhs->kelompok = $kelas . $semester;
            return $mhs;
        })->groupBy('kelompok');

        $pertemuans = collect();
        foreach ($groupedMahasiswa as $kelompok => $mhsList) {
            $cleanKelompok = explode(' (', $kelompok)[0];

            // Try exact match first
            $match = $pertemuansRaw->where('kelompok', $kelompok)->first();

            // Fallback: match by base class name
            if (!$match) {
                $match = $pertemuansRaw->first(function($p) use ($cleanKelompok) {
                    return explode(' (', $p->kelompok ?? '')[0] === $cleanKelompok;
                });
            }

            if ($match) {
                $pertemuans->put($kelompok, $match);
            }
        }
        $data['pertemuans'] = $pertemuans;

        $data['groupedMahasiswa'] = $groupedMahasiswa;

        // Ambil nama kelompok/kelas yang sudah absen di tiap pertemuan
        $data['meetingGroups'] = PertemuanKuliah::where('mata_kuliah_id', $mata_kuliah_id)
            ->select('pertemuan_ke', 'kelompok')
            ->get()
            ->groupBy('pertemuan_ke')
            ->map(function($meetings) {
                return $meetings->pluck('kelompok')->toArray();
            })
            ->toArray();
        $data['totalGroups'] = count($groupedMahasiswa);

        return view('central.backpage.dosen-absensi-kelas-show', $data, compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'pertemuan_ke' => 'required|integer|min:1|max:14',
            'kelompok' => 'required|string',
            'tanggal' => 'required|date',
            'materi' => 'nullable|string',
            'absensi' => 'required|array',
        ]);

        $user = Auth::guard('dosen')->user();
        $syncNilai = $request->has('sync_nilai');

        try {
            DB::beginTransaction();

            $cleanKelompok = explode(' (', $request->kelompok)[0];

            // Find existing meeting by exact name or matching base class name
            $pertemuan = PertemuanKuliah::where('mata_kuliah_id', $request->mata_kuliah_id)
                ->where('pertemuan_ke', $request->pertemuan_ke)
                ->where(function($q) use ($request, $cleanKelompok) {
                    $q->where('kelompok', $request->kelompok)
                      ->orWhere('kelompok', 'like', $cleanKelompok . ' %')
                      ->orWhere('kelompok', $cleanKelompok);
                })
                ->first();

            if ($pertemuan) {
                // Update the existing record and update kelompok name to the current one
                $pertemuan->update([
                    'kelompok' => $request->kelompok,
                    'dosen_id' => $user->id,
                    'tanggal' => $request->tanggal,
                    'materi' => $request->materi,
                    'status' => 'Selesai'
                ]);
            } else {
                // Create a new meeting
                $pertemuan = PertemuanKuliah::create([
                    'mata_kuliah_id' => $request->mata_kuliah_id,
                    'pertemuan_ke' => $request->pertemuan_ke,
                    'kelompok' => $request->kelompok,
                    'dosen_id' => $user->id,
                    'tanggal' => $request->tanggal,
                    'materi' => $request->materi,
                    'status' => 'Selesai'
                ]);
            }

            foreach ($request->absensi as $mahasiswa_id => $status) {
                AbsensiMahasiswa::updateOrCreate(
                    ['pertemuan_id' => $pertemuan->id, 'mahasiswa_id' => $mahasiswa_id],
                    ['status' => $status]
                );

                if ($syncNilai) {
                    $cleanKel = explode(' (', $request->kelompok)[0];
                    $pertemuanIds = PertemuanKuliah::where('mata_kuliah_id', $request->mata_kuliah_id)
                        ->where(function($q) use ($request, $cleanKel) {
                            $q->where('kelompok', $request->kelompok)
                              ->orWhere('kelompok', 'like', $cleanKel . ' %')
                              ->orWhere('kelompok', $cleanKel);
                        })
                        ->pluck('id');

                    $totalHadir = AbsensiMahasiswa::whereIn('pertemuan_id', $pertemuanIds)
                        ->where('mahasiswa_id', $mahasiswa_id)
                        ->where('status', 'Hadir')
                        ->count();

                    $totalMeetings = count($pertemuanIds);
                    $persentaseKehadiran = ($totalMeetings > 0) ? round(($totalHadir / $totalMeetings) * 100, 2) : 100;
                    $this->syncToNilai($request->mata_kuliah_id, $mahasiswa_id, $user->id, $persentaseKehadiran);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Absensi ' . $request->kelompok . ' berhasil disimpan!' . ($syncNilai ? ' (Nilai Keaktifan Disinkronkan)' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error simpan absensi UI: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan.')->withInput();
        }
    }

    private function syncToNilai($mkId, $mhsId, $dsnId, $skorKeaktifan) {
        $nilaiMhs = DB::table('nilai_mahasiswa')->where('mata_kuliah_id', $mkId)->where('mahasiswa_id', $mhsId)->first();

        $gradeData = [
            'mata_kuliah_id' => $mkId, 'mahasiswa_id' => $mhsId, 'dosen_id' => $dsnId,
            'quiz' => $skorKeaktifan, 'updated_at' => now()
        ];

        // Ambil komponen aktif dan bobotnya
        try {
            $activeComponents = \App\Models\Akademik\BobotNilaiSetting::getActiveComponents();
            $weights = \App\Models\Akademik\BobotNilaiSetting::getWeightsArray();
        } catch (\Exception $e) {
            // Fallback jika model/table belum siap
            $activeComponents = collect();
            $weights = ['tugas' => 20, 'quiz' => 15, 'uts' => 30, 'uas' => 35];
        }

        // Siapkan nilai saat ini (dari DB atau dari sinkronisasi baru)
        $currentValues = [
            'tugas' => $nilaiMhs->tugas ?? null,
            'quiz' => $skorKeaktifan, // Nilai baru dari sinkron
            'uts' => $nilaiMhs->uts ?? null,
            'uas' => $nilaiMhs->uas ?? null,
        ];

        // Cek apakah semua komponen aktif sudah memiliki nilai (bukan NULL)
        $allFilled = true;
        if ($activeComponents->isNotEmpty()) {
            foreach ($activeComponents as $component) {
                $key = $component->component_key;
                if (!isset($currentValues[$key]) || $currentValues[$key] === null) {
                    $allFilled = false;
                    break;
                }
            }
        } else {
            // Fallback lama jika setting tidak ditemukan
            $allFilled = ($currentValues['tugas'] !== null && $currentValues['uts'] !== null && $currentValues['uas'] !== null);
        }

        // Hanya hitung nilai_akhir jika SEMUA komponen sudah terisi
        if ($allFilled) {
            $nilaiAkhir = 0;
            if ($activeComponents->isNotEmpty()) {
                foreach ($activeComponents as $component) {
                    $key = $component->component_key;
                    $weight = ($weights[$key] ?? 0) / 100;
                    $nilaiAkhir += ($currentValues[$key] * $weight);
                }
            } else {
                // Fallback kalkulasi lama
                $nilaiAkhir = ($currentValues['tugas'] * 0.2) + ($skorKeaktifan * 0.15) + ($currentValues['uts'] * 0.3) + ($currentValues['uas'] * 0.35);
            }

            $gradeData['nilai_akhir'] = $nilaiAkhir;
            $gradeData['huruf_nilai'] = $this->getGradeLetterFallback($nilaiAkhir);
        } else {
            // Jika belum lengkap, nilai_akhir dan huruf_nilai tetap NULL agar statusnya "Belum Dinilai"
            $gradeData['nilai_akhir'] = null;
            $gradeData['huruf_nilai'] = null;
        }

        DB::table('nilai_mahasiswa')->updateOrInsert(['mata_kuliah_id' => $mkId, 'mahasiswa_id' => $mhsId], array_merge($gradeData, ['created_at' => now()]));
    }
    private function getGradeLetterFallback($nilai)
    {
        return \App\Models\NilaiMahasiswa::convertToLetterGrade($nilai);
    }

    // FUNGSI INI SUDAH DILENGKAPI FILTER KRS YANG SUPER KETAT
    private function getMahasiswaDataByMataKuliah($mataKuliahId, $onlyKrs = false)
    {
        $mataKuliah = MataKuliah::find($mataKuliahId);
        if (!$mataKuliah) return collect([]);

        $mahasiswaList = collect();

        // 1. Ambil mahasiswa aktif berdasarkan saklar KRS / Prodi
        if ($onlyKrs && DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa')) {
            $mahasiswaList = DB::table('krs_detail_mahasiswa as kd')
                ->join('krs_mahasiswa as km', 'kd.krs_mahasiswa_id', '=', 'km.id')
                ->join('mahasiswas as m', 'km.mahasiswa_id', '=', 'm.id')
                ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                ->where('kd.mata_kuliah_id', $mataKuliahId)
                ->whereNotNull('m.name')
                ->whereIn('km.status', ['submitted', 'approved'])
                ->select('m.id', 'm.name', 'm.numb_nim as nim', 'kl.name as kelas_name', 'm.semester')
                ->distinct()
                ->get();
        } else {
            // JIKA SAKLAR OFF: Ambil mahasantri yang semester aktifnya sama dengan atau lebih tinggi dari semester matkul
            $query = DB::table('mahasiswas as m')
                ->leftJoin('program_studis as ps', 'm.prodi_id', '=', 'ps.id')
                ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                ->where('m.prodi_id', $mataKuliah->prodi_id)
                ->whereNotNull('m.name');

            if ($mataKuliah->semester) {
                $query->where('m.semester', '>=', $mataKuliah->semester);
            }

            $mahasiswaList = $query->select('m.id', 'm.name', 'm.numb_nim as nim', 'kl.name as kelas_name', 'm.semester')
                ->get();

            if ($mahasiswaList->isEmpty() && DB::getSchemaBuilder()->hasTable('krs_detail_mahasiswa')) {
                $mahasiswaList = DB::table('krs_detail_mahasiswa as kd')
                    ->join('krs_mahasiswa as km', 'kd.krs_mahasiswa_id', '=', 'km.id')
                    ->join('mahasiswas as m', 'km.mahasiswa_id', '=', 'm.id')
                    ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                    ->where('kd.mata_kuliah_id', $mataKuliahId)
                    ->whereNotNull('m.name')
                    ->select('m.id', 'm.name', 'm.numb_nim as nim', 'kl.name as kelas_name', 'm.semester')
                    ->distinct()
                    ->get();
            }
        }

        // 2. Ambil mahasiswa yang sudah memiliki riwayat absensi di mata kuliah ini (agar data lama/riwayat tidak hilang)
        if (DB::getSchemaBuilder()->hasTable('pertemuan_kuliahs') && DB::getSchemaBuilder()->hasTable('absensi_mahasiswas')) {
            $historicalStudents = DB::table('absensi_mahasiswas as am')
                ->join('pertemuan_kuliahs as pk', 'am.pertemuan_id', '=', 'pk.id')
                ->join('mahasiswas as m', 'am.mahasiswa_id', '=', 'm.id')
                ->leftJoin('kelas as kl', 'm.kelas_id', '=', 'kl.id')
                ->where('pk.mata_kuliah_id', $mataKuliahId)
                ->whereNotNull('m.name')
                ->select('m.id', 'm.name', 'm.numb_nim as nim', 'kl.name as kelas_name', 'm.semester')
                ->distinct()
                ->get();

            if ($historicalStudents->isNotEmpty()) {
                $mahasiswaList = $mahasiswaList->merge($historicalStudents)->unique('id');
            }
        }

        // 3. Urutkan kembali berdasarkan nama kelas dan NIM
        return $mahasiswaList->sortBy(function($m) {
            return ($m->kelas_name ?? 'ZZZ') . '-' . ($m->nim ?? '9999999999');
        })->values();
    }

    public function rekap(Request $request)
    {
        $user = Auth::guard('dosen')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Rekap Absensi";
        $data['pages'] = "Rekap Kehadiran Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        $mataKuliahId = $request->query('mata_kuliah_id');
        $kelompokPilih = $request->query('kelompok');

        $data['mataKuliahList'] = MataKuliah::where(function($query) use ($user) {
            $query->where('dosen1_id', $user->id)->orWhere('dosen2_id', $user->id)->orWhere('dosen3_id', $user->id);
        })->get();

        $data['selectedMatkul'] = null;
        $data['groupedMahasiswa'] = collect();
        $data['rekapData'] = [];
        $data['kelompokPilih'] = 'all';

        if ($mataKuliahId) {
            $data['selectedMatkul'] = $data['mataKuliahList']->where('id', $mataKuliahId)->first();

            // TANGKAP TOGGLE KRS
            $onlyKrs = $request->query('krs_only') == '1';
            $data['krs_only'] = $onlyKrs;

            $mahasiswaList = $this->getMahasiswaDataByMataKuliah($mataKuliahId, $onlyKrs);

            $data['groupedMahasiswa'] = $mahasiswaList->map(function ($mhs) {
                $kelas = $mhs->kelas_name ?? 'Tanpa Kelas';
                $semester = $mhs->semester ? ' (Semester Aktif ' . $mhs->semester . ')' : '';
                $mhs->kelompok = $kelas . $semester;
                return $mhs;
            })->groupBy('kelompok');

            if (!$kelompokPilih && $data['groupedMahasiswa']->isNotEmpty()) {
                $kelompokPilih = $data['groupedMahasiswa']->keys()->sortBy(function($key) {
                    preg_match('/Semester (?:Aktif )?(\d+)/', $key, $matches);
                    return isset($matches[1]) ? (int)$matches[1] : 99;
                })->first();
            } elseif (!$kelompokPilih) {
                $kelompokPilih = 'all';
            }

            $data['kelompokPilih'] = $kelompokPilih;

            $pertemuans = PertemuanKuliah::where('mata_kuliah_id', $mataKuliahId)->get();
            $absensiData = [];
            if ($pertemuans->isNotEmpty()) {
                $absensiData = AbsensiMahasiswa::whereIn('pertemuan_id', $pertemuans->pluck('id'))->get();
            }

            $rekap = [];
            foreach ($mahasiswaList as $mhs) {
                if ($kelompokPilih != 'all' && $mhs->kelompok != $kelompokPilih) continue;

                $rekap[$mhs->id] = [
                    'mhs' => $mhs,
                    'pertemuan' => array_fill(1, 14, '-'),
                    'total_hadir' => 0,
                ];
            }

            foreach ($absensiData as $absen) {
                $pertemuan = $pertemuans->where('id', $absen->pertemuan_id)->first();
                if ($pertemuan && isset($rekap[$absen->mahasiswa_id])) {
                    $mhsBaseKelas = $rekap[$absen->mahasiswa_id]['mhs']->kelas_name ?? '';
                    $cleanPertemuanKelompok = explode(' (', $pertemuan->kelompok ?? '')[0];
                    $cleanKelompokPilih = $kelompokPilih != 'all' ? explode(' (', $kelompokPilih)[0] : 'all';

                    if ($kelompokPilih == 'all' ||
                        $pertemuan->kelompok == $kelompokPilih ||
                        $cleanPertemuanKelompok == $cleanKelompokPilih ||
                        (!empty($mhsBaseKelas) && $cleanPertemuanKelompok == $mhsBaseKelas)
                    ) {
                        $statusKode = '-';
                        if($absen->status == 'Hadir') { $statusKode = 'H'; $rekap[$absen->mahasiswa_id]['total_hadir']++; }
                        elseif($absen->status == 'Sakit') $statusKode = 'S';
                        elseif($absen->status == 'Izin') $statusKode = 'I';
                        elseif($absen->status == 'Alpa') $statusKode = 'A';

                        $rekap[$absen->mahasiswa_id]['pertemuan'][$pertemuan->pertemuan_ke] = $statusKode;
                    }
                }
            }

            foreach($rekap as $id => $dataRekap) {
                $studentGroup = $dataRekap['mhs']->kelompok;
                $cleanStudentGroup = explode(' (', $studentGroup)[0];
                $groupPertemuanCount = $pertemuans->filter(function($p) use ($studentGroup, $cleanStudentGroup) {
                    $cleanP = explode(' (', $p->kelompok ?? '')[0];
                    return $p->kelompok == $studentGroup || $cleanP == $cleanStudentGroup;
                })->count();
                $rekap[$id]['persentase'] = ($groupPertemuanCount > 0)
                    ? round(($dataRekap['total_hadir'] / $groupPertemuanCount) * 100, 0)
                    : 100;
            }

            $data['rekapDataByKelas'] = collect($rekap)->groupBy(function($item) {
                return $item['mhs']->kelompok;
            });
        }

        return view('central.backpage.dosen-absensi-kelas-rekap', $data, compact('user'));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::guard('dosen')->user();
        $mataKuliahId = $request->query('mata_kuliah_id');
        $kelompokPilih = $request->query('kelompok', 'all');

        // TANGKAP TOGGLE KRS
        $onlyKrs = $request->query('krs_only') == '1';

        if (!$mataKuliahId) return back()->with('error', 'Pilih mata kuliah terlebih dahulu.');

        $matkul = MataKuliah::findOrFail($mataKuliahId);
        $mahasiswaList = $this->getMahasiswaDataByMataKuliah($mataKuliahId, $onlyKrs);

        $groupedMahasiswa = $mahasiswaList->map(function ($mhs) {
            $kelas = $mhs->kelas_name ?? 'Tanpa Kelas';
            $semester = $mhs->semester ? ' (Semester Aktif ' . $mhs->semester . ')' : '';
            $mhs->kelompok = $kelas . $semester;
            return $mhs;
        });

        $pertemuans = PertemuanKuliah::where('mata_kuliah_id', $mataKuliahId)->get();
        $absensiData = $pertemuans->isNotEmpty() ? AbsensiMahasiswa::whereIn('pertemuan_id', $pertemuans->pluck('id'))->get() : collect();

        $rekap = [];
        foreach ($mahasiswaList as $mhs) {
            $cleanMhsKelompok = explode(' (', $mhs->kelompok)[0];
            $cleanKelompokPilih = $kelompokPilih != 'all' ? explode(' (', $kelompokPilih)[0] : 'all';
            if ($kelompokPilih != 'all' && $mhs->kelompok != $kelompokPilih && $cleanMhsKelompok != $cleanKelompokPilih) continue;
            $rekap[$mhs->id] = ['mhs' => $mhs, 'pertemuan' => array_fill(1, 14, ''), 'total_hadir' => 0];
        }

        foreach ($absensiData as $absen) {
            $pertemuan = $pertemuans->where('id', $absen->pertemuan_id)->first();
            if ($pertemuan && isset($rekap[$absen->mahasiswa_id])) {
                $mhsBaseKelas = $rekap[$absen->mahasiswa_id]['mhs']->kelas_name ?? '';
                $cleanPertemuanKelompok = explode(' (', $pertemuan->kelompok ?? '')[0];
                $cleanKelompokPilih = $kelompokPilih != 'all' ? explode(' (', $kelompokPilih)[0] : 'all';

                if ($kelompokPilih == 'all' ||
                    $pertemuan->kelompok == $kelompokPilih ||
                    $cleanPertemuanKelompok == $cleanKelompokPilih ||
                    (!empty($mhsBaseKelas) && $cleanPertemuanKelompok == $mhsBaseKelas)
                ) {
                    $s = '';
                    if($absen->status == 'Hadir') { $s = 'H'; $rekap[$absen->mahasiswa_id]['total_hadir']++; }
                    elseif($absen->status == 'Sakit') $s = 'S';
                    elseif($absen->status == 'Izin') $s = 'I';
                    elseif($absen->status == 'Alpa') $s = 'A';
                    $rekap[$absen->mahasiswa_id]['pertemuan'][$pertemuan->pertemuan_ke] = $s;
                }
            }
        }

        foreach($rekap as $id => $dataRekap) {
            $studentGroup = $dataRekap['mhs']->kelompok;
            $cleanStudentGroup = explode(' (', $studentGroup)[0];
            $groupPertemuanCount = $pertemuans->filter(function($p) use ($studentGroup, $cleanStudentGroup) {
                $cleanP = explode(' (', $p->kelompok ?? '')[0];
                return $p->kelompok == $studentGroup || $cleanP == $cleanStudentGroup;
            })->count();
            $rekap[$id]['persentase'] = ($groupPertemuanCount > 0)
                ? round(($dataRekap['total_hadir'] / $groupPertemuanCount) * 100, 0)
                : 100;
        }

        // ==========================================
        // TAMBAHKAN PENGECEKAN ANTI-CRASH DISINI
        // ==========================================
        if (empty($rekap)) {
            return back()->with('error', 'Gagal Download: Tidak ada data mahasantri (KRS Aktif) di kelas ini. Pastikan KRS sudah disetujui Admin.');
        }

        $dataExport = [
            'dosen' => $user,
            'matkul' => $matkul,
            'rekapDataByKelas' => collect($rekap)->groupBy(function($item) { return $item['mhs']->kelompok; })
        ];

        $safeKelompok = $kelompokPilih === 'all' ? 'Semua_Kelas' : preg_replace('/[^A-Za-z0-9\-]/', '_', $kelompokPilih);
        $namaFile = 'Absensi_' . str_replace(' ', '_', $matkul->name) . '_' . $safeKelompok . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DosenAbsensiExport($dataExport), $namaFile);
    }

    public function importExcel(Request $request)
    {
        // Tetap sama seperti kode sebelumnya (sudah ada di sistem kamu)
        $request->validate(['mata_kuliah_id' => 'required|exists:mata_kuliahs,id', 'file_excel' => 'required|file|mimes:xlsx,xls']);
        $user = Auth::guard('dosen')->user();
        $mataKuliahId = $request->mata_kuliah_id;
        $syncNilai = $request->has('sync_nilai');

        $mataKuliah = MataKuliah::where('id', $mataKuliahId)->where(function($q) use ($user) {
            $q->where('dosen1_id', $user->id)->orWhere('dosen2_id', $user->id)->orWhere('dosen3_id', $user->id);
        })->firstOrFail();

        try {
            $sheets = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('file_excel'));
            if (empty($sheets)) return back()->with('error', 'File Excel kosong.');

            $successCount = 0;
            DB::beginTransaction();

            foreach ($sheets as $rows) {
                for ($i = 6; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $nim = trim((string)($row[2] ?? ''));
                    if (empty($nim)) continue;

                    $mahasiswa = \App\Models\Mahasantri::with('kelas')->where('numb_nim', $nim)->first();
                    if (!$mahasiswa) continue;

                    $kelompok = ($mahasiswa->kelas->name ?? 'Tanpa Kelas') . ($mahasiswa->semester ? ' (Semester Aktif ' . $mahasiswa->semester . ')' : '');
                    $totalHadir = 0;
                    $totalValidMeetings = 0;

                    for ($p = 1; $p <= 14; $p++) {
                        $colIndex = $p + 2;
                        $statusKode = strtoupper(trim((string)($row[$colIndex] ?? '')));

                        $status = null;
                        if ($statusKode === 'H') { $status = 'Hadir'; $totalHadir++; $totalValidMeetings++; }
                        elseif ($statusKode === 'S') { $status = 'Sakit'; $totalValidMeetings++; }
                        elseif ($statusKode === 'I') { $status = 'Izin'; $totalValidMeetings++; }
                        elseif ($statusKode === 'A') { $status = 'Alpa'; $totalValidMeetings++; }

                        if ($status) {
                            $cleanKelompok = explode(' (', $kelompok)[0];
                            $pertemuan = PertemuanKuliah::where('mata_kuliah_id', $mataKuliahId)
                                ->where('pertemuan_ke', $p)
                                ->where(function($q) use ($kelompok, $cleanKelompok) {
                                    $q->where('kelompok', $kelompok)
                                      ->orWhere('kelompok', 'like', $cleanKelompok . ' %')
                                      ->orWhere('kelompok', $cleanKelompok);
                                })
                                ->first();

                            if ($pertemuan) {
                                $pertemuan->update([
                                    'kelompok' => $kelompok,
                                    'dosen_id' => $user->id,
                                    'tanggal' => date('Y-m-d'),
                                    'status' => 'Selesai'
                                ]);
                            } else {
                                $pertemuan = PertemuanKuliah::create([
                                    'mata_kuliah_id' => $mataKuliahId,
                                    'pertemuan_ke' => $p,
                                    'kelompok' => $kelompok,
                                    'dosen_id' => $user->id,
                                    'tanggal' => date('Y-m-d'),
                                    'status' => 'Selesai'
                                ]);
                            }

                            AbsensiMahasiswa::updateOrCreate(
                                ['pertemuan_id' => $pertemuan->id, 'mahasiswa_id' => $mahasiswa->id],
                                ['status' => $status]
                            );
                        }
                    }

                    if ($syncNilai) {
                        $persentaseKehadiran = ($totalValidMeetings > 0) ? round(($totalHadir / $totalValidMeetings) * 100, 2) : 100;
                        $nilaiMhs = DB::table('nilai_mahasiswa')->where('mata_kuliah_id', $mataKuliahId)->where('mahasiswa_id', $mahasiswa->id)->first();

                        $gradeData = ['mata_kuliah_id' => $mataKuliahId, 'mahasiswa_id' => $mahasiswa->id, 'dosen_id' => $user->id, 'quiz' => $persentaseKehadiran, 'updated_at' => now()];

                        if ($nilaiMhs) {
                            try { $bobot = \App\Models\Akademik\BobotNilaiSetting::getWeightsArray(); }
                            catch (\Exception $e) { $bobot = ['tugas' => 20, 'quiz' => 15, 'uts' => 30, 'uas' => 35]; }

                            $nilaiAkhir = 0;
                            if ($nilaiMhs->tugas !== null) $nilaiAkhir += ($nilaiMhs->tugas * (($bobot['tugas'] ?? 20) / 100));
                            $nilaiAkhir += ($persentaseKehadiran * (($bobot['quiz'] ?? 15) / 100));
                            if ($nilaiMhs->uts !== null) $nilaiAkhir += ($nilaiMhs->uts * (($bobot['uts'] ?? 30) / 100));
                            if ($nilaiMhs->uas !== null) $nilaiAkhir += ($nilaiMhs->uas * (($bobot['uas'] ?? 35) / 100));

                            $gradeData['nilai_akhir'] = $nilaiAkhir;
                            try { $gradeData['huruf_nilai'] = \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($nilaiAkhir); } catch (\Exception $e) { }
                        }

                        DB::table('nilai_mahasiswa')->updateOrInsert(['mata_kuliah_id' => $mataKuliahId, 'mahasiswa_id' => $mahasiswa->id], array_merge($gradeData, ['created_at' => now()]));
                    }
                    $successCount++;
                }
            }

            DB::commit();
            $msg = "Upload sukses! Kehadiran $successCount mahasantri diperbarui.";
            if ($syncNilai) $msg .= " (Sistem telah mensinkronisasi nilai Keaktifan).";

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload Excel Absensi Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
