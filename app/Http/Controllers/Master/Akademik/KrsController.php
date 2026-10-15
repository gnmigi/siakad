<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasantri\KrsMahasiswa;
use App\Models\Mahasantri\KrsDetailMahasiswa;
use App\Models\Pengaturan\WebSetting;
use App\Models\Mahasantri;
use App\Models\Akademik\TahunAkademik;
use RealRashid\SweetAlert\Facades\Alert;
use Rap2hpoutre\FastExcel\FastExcel;

class KrsController extends Controller
{
    public function index()
    {
        $data['webs'] = WebSetting::first();
        $data['user'] = Auth::user();
        $data['pages'] = "KRS Mahasantri";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $data['spref'] = $prefix . '.';

        // Get all KRS with mahasiswa data
        $krsData = KrsMahasiswa::with(['mahasiswa.prodi', 'tahunAkademik', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by status for better organization
        $data['krsSubmitted'] = $krsData->where('status', 'submitted');
        $data['krsApproved'] = $krsData->where('status', 'approved');
        $data['krsRejected'] = $krsData->where('status', 'rejected');
        $data['krsDraft'] = $krsData->where('status', 'draft');
        $data['krsAll'] = $krsData;

        // Statistics
        $data['totalKrs'] = $krsData->count();
        $data['pendingApproval'] = $krsData->where('status', 'submitted')->count();
        $data['approved'] = $krsData->where('status', 'approved')->count();
        $data['rejected'] = $krsData->where('status', 'rejected')->count();
        $data['draft'] = $krsData->where('status', 'draft')->count();

        return view('master.akademik.krs-index', $data);
    }

    public function show($id)
    {
        $data['webs'] = WebSetting::first();
        $data['user'] = Auth::user();
        $data['pages'] = "Detail KRS";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $data['spref'] = $prefix . '.';

        $krs = KrsMahasiswa::with(['mahasiswa.prodi', 'tahunAkademik', 'details.mataKuliah.dosen1', 'approvedBy', 'rejectedBy'])
            ->findOrFail($id);

        $data['krs'] = $krs;

        return view('master.akademik.krs-show', $data);
    }

    public function approve($id)
    {
        try {
            $krs = KrsMahasiswa::findOrFail($id);

            if ($krs->status !== 'submitted') {
                Alert::error('Error', 'KRS tidak dapat disetujui. Status harus submitted.');
                return redirect()->back();
            }

            $krs->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            Alert::success('Berhasil', 'KRS berhasil disetujui.');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function reject($id, Request $request)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            $krs = KrsMahasiswa::findOrFail($id);

            if ($krs->status !== 'submitted') {
                Alert::error('Error', 'KRS tidak dapat ditolak. Status harus submitted.');
                return redirect()->back();
            }

            $krs->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            Alert::success('Berhasil', 'KRS berhasil ditolak.');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'krs_ids' => 'required|array',
            'krs_ids.*' => 'exists:krs_mahasiswa,id'
        ]);

        $approvedCount = 0;
        foreach ($request->krs_ids as $krsId) {
            $krs = KrsMahasiswa::find($krsId);
            if ($krs && $krs->status === 'submitted') {
                $krs->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now()
                ]);
                $approvedCount++;
            }
        }

        Alert::success('Berhasil', "Berhasil menyetujui {$approvedCount} KRS.");
        return redirect()->back();
    }

    public function reopen($id)
    {
        $krs = KrsMahasiswa::findOrFail($id);

        if ($krs->status !== 'rejected') {
            Alert::error('Error', 'Hanya KRS yang ditolak yang dapat dibuka kembali.');
            return redirect()->back();
        }

        $krs->update([
            'status' => 'draft',
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'reopened_by' => Auth::id(),
            'reopened_at' => now()
        ]);

        Alert::success('Berhasil', 'KRS berhasil dibuka kembali. Mahasantri dapat mengajukan ulang.');
        return redirect()->back();
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'all');

        // Pastikan relasi details dan mataKuliah di-load agar bisa ditarik datanya
        $krsData = KrsMahasiswa::with(['mahasiswa.prodi', 'mahasiswa.kelas', 'tahunAkademik', 'approvedBy', 'details.mataKuliah'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($krsData->isEmpty()) {
            Alert::warning('Peringatan', 'Tidak ada data KRS untuk diexport.');
            return redirect()->back();
        }

        $sheets = new \Rap2hpoutre\FastExcel\SheetCollection();

        // =========================================================================
        // 1. LOGIKA RINGKASAN: PER KELAS ATAU PER SEMESTER
        // =========================================================================
        if ($type === 'kelas' || $type === 'semester') {
            
            // Pisahkan ke dalam tab-tab sheet
            $groupedData = $krsData->groupBy(function($krs) use ($type) {
                if ($type === 'kelas') return $krs->mahasiswa->kelas->name ?? 'Tanpa Kelas';
                return 'Semester ' . ($krs->semester ?? $krs->mahasiswa->semester ?? 'Unknown');
            });

            foreach ($groupedData as $groupName => $data) {
                // Di dalam sheet tersebut, kumpulkan data per Mahasiswa (Biar Ringkas)
                $summaryPerStudent = $data->groupBy('mahasiswa_id')->map(function($studentKrs) {
                    $mhs = $studentKrs->first()->mahasiswa;
                    
                    // Hitung rekap status KRS anak ini
                    $totalKrs = $studentKrs->count();
                    $acc = $studentKrs->where('status', 'approved')->count();
                    $submitted = $studentKrs->where('status', 'submitted')->count();
                    $draft = $studentKrs->where('status', 'draft')->count();
                    $rejected = $studentKrs->where('status', 'rejected')->count();
                    
                    // Hitung total SKS yang VALID (Hanya yang di-Approve)
                    $sksAcc = $studentKrs->where('status', 'approved')->sum('total_sks');

                    return [
                        'NIM' => $mhs->numb_nim ?? $mhs->nim ?? '-',
                        'Nama Mahasantri' => $mhs->name ?? '-',
                        'Program Studi' => $mhs->prodi->name ?? '-',
                        'Total Pengajuan KRS' => $totalKrs . ' Kali',
                        'Rincian Status' => "{$acc} Disetujui, {$submitted} Menunggu, {$draft} Draft, {$rejected} Ditolak",
                        'Total SKS Disetujui' => $sksAcc . ' SKS'
                    ];
                })->values()->toArray(); // Reset index array

                $safeSheetName = substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $groupName), 0, 31);
                $sheets[$safeSheetName] = $summaryPerStudent;
            }
            
            $fileName = $type === 'kelas' ? 'Ringkasan_KRS_Per_Kelas.xlsx' : 'Ringkasan_KRS_Per_Semester.xlsx';
            return (new FastExcel($sheets))->download($fileName);

        // =========================================================================
        // 2. LOGIKA IDEAL: PER SHEET = 1 KELAS, ISI = DAFTAR MATKUL & TOTAL MHS
        // =========================================================================
        } elseif ($type === 'matkul') {
            
            $summaryData = [];
            
            // HANYA AMBIL YANG VALID (Submitted & Approved) Biar SINKRON dengan Dosen
            foreach ($krsData as $krs) {
                if (in_array($krs->status, ['submitted', 'approved'])) {
                    $kelasName = $krs->mahasiswa->kelas->name ?? 'Tanpa Kelas';
                    
                    foreach ($krs->details as $detail) {
                        if ($detail->mataKuliah) {
                            $matkulName = $detail->mataKuliah->name;
                            
                            // Bikin kunci unik: Kelas + Matkul
                            $key = $kelasName . '_' . $matkulName;
                            
                            if (!isset($summaryData[$key])) {
                                $summaryData[$key] = [
                                    'kelas' => $kelasName,
                                    'matkul' => $matkulName,
                                    'sks' => $detail->sks ?? 0,
                                    'total_mhs' => 0
                                ];
                            }
                            
                            // Tambah hitungan mahasiswanya
                            $summaryData[$key]['total_mhs']++;
                        }
                    }
                }
            }

            // Kelompokkan data yang sudah dihitung berdasarkan KELAS
            $groupedByKelas = collect($summaryData)->groupBy('kelas');
            
            // Siapkan Sheet Collection
            $sheets = new \Rap2hpoutre\FastExcel\SheetCollection();

            foreach ($groupedByKelas as $kelas => $matkuls) {
                
                // Urutkan daftar matkul sesuai abjad di dalam sheet tersebut
                $sortedMatkuls = $matkuls->sortBy('matkul')->values();

                // Format tampilan kolom di Excel
                $formattedData = $sortedMatkuls->map(function($item, $index) {
                    return [
                        'No' => $index + 1,
                        'Mata Kuliah' => $item['matkul'],
                        'Bobot' => $item['sks'] . ' SKS',
                        'Total Mahasantri' => $item['total_mhs'] . ' Orang (KRS Aktif)'
                    ];
                })->toArray();

                // Amankan nama sheet sesuai nama kelas
                $safeSheetName = substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $kelas), 0, 31);
                
                // Masukkan ke Sheet
                $sheets[$safeSheetName] = $formattedData;
            }

            // Download!
            return (new FastExcel($sheets))->download('Ringkasan_Matkul_Per_Kelas.xlsx');

        // =========================================================================
        // 3. LOGIKA DEFAULT: SEMUA DATA (GLOBAL)
        // =========================================================================
        } else {
            $formatRow = function ($krs) {
                return [
                    'ID KRS' => $krs->id,
                    'Nama Mahasantri' => $krs->mahasiswa->name ?? '-',
                    'NIM' => $krs->mahasiswa->numb_nim ?? $krs->mahasiswa->nim ?? '-',
                    'Kelas' => $krs->mahasiswa->kelas->name ?? 'Tanpa Kelas',
                    'Semester' => 'Semester ' . ($krs->semester ?? $krs->mahasiswa->semester ?? '-'),
                    'Program Studi' => $krs->mahasiswa->prodi->name ?? '-',
                    'Tahun Akademik' => $krs->tahunAkademik->name ?? '-',
                    'Total SKS' => $krs->total_sks ?? 0,
                    'Status' => strtoupper($krs->status),
                    'Tanggal Submit' => $krs->created_at ? $krs->created_at->format('d/m/Y H:i') : '-',
                    'Disetujui Oleh' => $krs->approvedBy->name ?? '-'
                ];
            };
            return (new FastExcel($krsData))->download('Data_KRS_Mahasantri_Global.xlsx', $formatRow);
        }
    }
}
