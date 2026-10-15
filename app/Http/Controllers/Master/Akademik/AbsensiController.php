<?php

namespace App\Http\Controllers\Master\Akademik;

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

class AbsensiController extends Controller
{
    // LOGIKA SAKTI: FILTER KRS & URUT NIM (SAMA PERSIS DENGAN DOSEN)
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
            // JIKA SAKLAR OFF: Ambil mahasantri yang semester aktifnya sama dengan semester matkul
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
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $user = Auth::user();
        $spref = $user && !empty($user->prefix)
            ? $user->prefix
            : (($user && (int) ($user->raw_type ?? 0) === 2) ? 'lecture.' : 'root.');
        $data['menus'] = "Rekap Absensi (Master)";
        $data['pages'] = "Pantau Kehadiran Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        $mataKuliahId = $request->query('mata_kuliah_id');
        $kelompokPilih = $request->query('kelompok');

        // BEDA DENGAN DOSEN: Tarik SEMUA Mata Kuliah tanpa filter dosen_id
        $data['mataKuliahList'] = MataKuliah::orderBy('name', 'asc')->get();
        // TAMBAHKAN INI:
        $data['kelasList'] = \App\Models\Akademik\Kelas::orderBy('name', 'asc')->get();

        $data['selectedMatkul'] = null;
        $data['groupedMahasiswa'] = collect();
        $data['rekapData'] = [];
        $data['kelompokPilih'] = 'all';

        if ($mataKuliahId) {
            $data['selectedMatkul'] = $data['mataKuliahList']->where('id', $mataKuliahId)->first();
            $onlyKrs = $request->has('krs_only') ? $request->query('krs_only') == '1' : true;
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
                // Potong kelompok di DB (misal BAS-02 REG A) karena dari dropdown cuma kekirim nama mentahnya (kalau hack filter dijalankan)
                $cleanMhsKelompok = explode(' (', $mhs->kelompok)[0];
                if ($kelompokPilih != 'all' && $mhs->kelompok != $kelompokPilih && $cleanMhsKelompok != $kelompokPilih) continue;

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
                    
                    // LOGIKA SAKTI ANTI-BUG
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
                $groupPertemuanCount = $pertemuans->where('kelompok', $studentGroup)->count();
                $rekap[$id]['persentase'] = ($groupPertemuanCount > 0) 
                    ? round(($dataRekap['total_hadir'] / $groupPertemuanCount) * 100, 0)
                    : 100;
            }

            $data['rekapDataByKelas'] = collect($rekap)->groupBy(function($item) {
                return $item['mhs']->kelompok;
            });
        }

        // Tampilkan view Admin
        return view('master.akademik.absensi-rekap', $data);
    }

    public function exportExcel(Request $request)
    {
        $mataKuliahId = $request->query('mata_kuliah_id');
        $kelompokPilih = $request->query('kelompok', 'all');
        $onlyKrs = $request->has('krs_only') ? $request->query('krs_only') == '1' : true;

        if (!$mataKuliahId) return back()->with('error', 'Pilih mata kuliah terlebih dahulu.');

        $matkul = MataKuliah::with('dosen1')->findOrFail($mataKuliahId);
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
            if ($kelompokPilih != 'all' && $mhs->kelompok != $kelompokPilih && $cleanMhsKelompok != $kelompokPilih) continue;
            $rekap[$mhs->id] = ['mhs' => $mhs, 'pertemuan' => array_fill(1, 14, ''), 'total_hadir' => 0];
        }

        foreach ($absensiData as $absen) {
            $pertemuan = $pertemuans->where('id', $absen->pertemuan_id)->first();
            if ($pertemuan && isset($rekap[$absen->mahasiswa_id])) {
                
                $mhsBaseKelas = $rekap[$absen->mahasiswa_id]['mhs']->kelas_name ?? '';
                $cleanPertemuanKelompok = explode(' (', $pertemuan->kelompok ?? '')[0];
                $cleanKelompokPilih = $kelompokPilih != 'all' ? explode(' (', $kelompokPilih)[0] : 'all';
                
                // LOGIKA SAKTI ANTI-BUG
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
            $groupPertemuanCount = $pertemuans->where('kelompok', $studentGroup)->count();
            $rekap[$id]['persentase'] = ($groupPertemuanCount > 0) 
                ? round(($dataRekap['total_hadir'] / $groupPertemuanCount) * 100, 0)
                : 100;
        }

        if (empty($rekap)) {
            return back()->with('error', 'Gagal Download: Tidak ada data mahasantri (KRS Aktif) di kelas ini. Pastikan KRS sudah disetujui Admin.');
        }

        $dataExport = [
            'dosen' => $matkul->dosen1 ?? Auth::user(), // Gunakan Dosen Pengampu aslinya
            'matkul' => $matkul,
            'rekapDataByKelas' => collect($rekap)->groupBy(function($item) { return $item['mhs']->kelompok; })
        ];

        $safeKelompok = $kelompokPilih === 'all' ? 'Semua_Kelas' : preg_replace('/[^A-Za-z0-9\-]/', '_', $kelompokPilih);
        $namaFile = 'Absensi_' . str_replace(' ', '_', $matkul->name) . '_' . $safeKelompok . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DosenAbsensiExport($dataExport), $namaFile);
    }

    public function importExcel(Request $request)
    {
        $request->validate(['mata_kuliah_id' => 'required|exists:mata_kuliahs,id', 'file_excel' => 'required|file|mimes:xlsx,xls']);
        $mataKuliahId = $request->mata_kuliah_id;
        $syncNilai = $request->has('sync_nilai'); 

        // Admin bisa import ke semua matkul
        $mataKuliah = MataKuliah::findOrFail($mataKuliahId);
        
        // Dosen ID diambil dari Pengampu Matkul (bukan Admin ID)
        $dosenPenanggungJawab = $mataKuliah->dosen1_id ?? Auth::id(); 

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
                                    'dosen_id' => $dosenPenanggungJawab,
                                    'tanggal' => date('Y-m-d'),
                                    'status' => 'Selesai'
                                ]);
                            } else {
                                $pertemuan = PertemuanKuliah::create([
                                    'mata_kuliah_id' => $mataKuliahId,
                                    'pertemuan_ke' => $p,
                                    'kelompok' => $kelompok,
                                    'dosen_id' => $dosenPenanggungJawab,
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

                        $gradeData = ['mata_kuliah_id' => $mataKuliahId, 'mahasiswa_id' => $mahasiswa->id, 'dosen_id' => $dosenPenanggungJawab, 'quiz' => $persentaseKehadiran, 'updated_at' => now()];

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
            $msg = "Upload via Admin sukses! Kehadiran $successCount mahasantri diperbarui.";
            if ($syncNilai) $msg .= " (Sistem mensinkronisasi nilai Keaktifan ke Dosen Pengampu).";
            
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload Excel Absensi Admin Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function exportExcelSemester(Request $request)
    {
        $semester = $request->query('semester');
        $kelasName = $request->query('kelas_name'); // Karena ini spesifik kelas tertentu, logikanya beda dengan kelompokPilih
        
        $onlyKrs = $request->has('krs_only') ? $request->query('krs_only') == '1' : false; 

        if (!$semester || !$kelasName) {
            return back()->with('error', 'Semester dan Kelas harus dipilih untuk export massal.');
        }

        $mataKuliahList = MataKuliah::with('dosen1')->where('semester', $semester)->get();

        if ($mataKuliahList->isEmpty()) {
            return back()->with('error', 'Tidak ada mata kuliah yang terdaftar di Semester ' . $semester);
        }

        $allRekapByMatkul = [];

        foreach ($mataKuliahList as $matkul) {
            $mahasiswaList = $this->getMahasiswaDataByMataKuliah($matkul->id, $onlyKrs);
            
            $mahasiswaKelasIni = collect();
            foreach ($mahasiswaList as $mhs) {
                if (($mhs->kelas_name ?? 'Tanpa Kelas') === $kelasName) {
                    $mahasiswaKelasIni->push($mhs);
                }
            }

            if ($mahasiswaKelasIni->isEmpty()) continue;

            $pertemuans = PertemuanKuliah::where('mata_kuliah_id', $matkul->id)->get();
            $absensiData = $pertemuans->isNotEmpty() ? AbsensiMahasiswa::whereIn('pertemuan_id', $pertemuans->pluck('id'))->get() : collect();

            $rekap = [];
            foreach ($mahasiswaKelasIni as $mhs) {
                $rekap[$mhs->id] = ['mhs' => $mhs, 'pertemuan' => array_fill(1, 14, ''), 'total_hadir' => 0];
            }

             foreach ($absensiData as $absen) {
                $pertemuan = $pertemuans->where('id', $absen->pertemuan_id)->first();
                if ($pertemuan && isset($rekap[$absen->mahasiswa_id])) {
                    
                    $mhsBaseKelas = $rekap[$absen->mahasiswa_id]['mhs']->kelas_name ?? '';
                    $cleanPertemuanKelompok = explode(' (', $pertemuan->kelompok ?? '')[0];
                    
                    // DI SINI PENYUSUPNYA! Kita ganti $kelompokPilih (yang gak ada) pakai $kelasName
                    if ($pertemuan->kelompok == $kelasName || 
                        $cleanPertemuanKelompok == $kelasName ||
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

            foreach ($rekap as $id => $dataRekap) {
                $mhsBaseKelas = $dataRekap['mhs']->kelas_name ?? '';
                $groupPertemuanCount = $pertemuans->filter(function($p) use ($kelasName, $mhsBaseKelas) {
                    $cleanPertemuanKelompok = explode(' (', $p->kelompok ?? '')[0];
                    return $p->kelompok == $kelasName || 
                           $cleanPertemuanKelompok == $kelasName ||
                           (!empty($mhsBaseKelas) && $cleanPertemuanKelompok == $mhsBaseKelas);
                })->count();
                $rekap[$id]['persentase'] = ($groupPertemuanCount > 0) 
                    ? round(($dataRekap['total_hadir'] / $groupPertemuanCount) * 100, 0)
                    : 100;
            }

            $allRekapByMatkul[$matkul->name] = [
                'dosen' => $matkul->dosen1 ?? (object)['name' => 'Belum Ditentukan'],
                'matkul' => $matkul,
                'rekap' => $rekap
            ];
        }

        if (empty($allRekapByMatkul)) {
            $msg = 'Gagal Download: Tidak ada data mahasantri di Kelas ' . $kelasName . ' untuk Semester ' . $semester;
            if ($onlyKrs) $msg .= ' (Coba matikan saklar "Hanya Tampilkan Mahasantri KRS" sebelum mendownload).';
            return back()->with('error', $msg);
        }

        $safeKelas = preg_replace('/[^A-Za-z0-9\-]/', '_', $kelasName);
        $namaFile = 'Absensi_Semester_' . $semester . '_' . $safeKelas . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MasterSemesterExport($allRekapByMatkul, $kelasName, $semester), $namaFile);
    }

}