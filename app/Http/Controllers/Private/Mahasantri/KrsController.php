<?php

namespace App\Http\Controllers\Private\Mahasantri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Mahasantri\KrsMahasiswa;
use App\Models\Mahasantri\KrsDetailMahasiswa;
use App\Models\Akademik\RencanaStudi;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\TahunAkademik;
use App\Models\Pengaturan\WebSetting;
use App\Models\Mahasantri;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;

class KrsController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Check if student is in terminal status
        if ($mahasiswa->isTerminal()) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            $message = 'Akses KRS tidak tersedia. Anda sedang dalam status terminal.';
            if ($terminalInfo && $terminalInfo['reason']) {
                $message .= ' Alasan: ' . $terminalInfo['reason'];
            }
            return redirect()->route('mahasiswa.dashboard-render')
                ->with('error', $message)
                ->with('alert-type', 'error')
                ->with('alert-title', 'Akses Ditolak');
        }
        
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['pages'] = "KRS";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get semester from request or default to current semester
        $semester = $request->input('semester', $mahasiswa->semester);

        // Get KRS for selected semester
        $currentKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->with(['details.mataKuliah', 'tahunAkademik'])
            ->first();

        $data['currentKrs'] = $currentKrs;
        $data['krsDetails'] = $currentKrs ? $currentKrs->details : collect();

        // Get available courses for selected semester
        $availableCourses = RencanaStudi::where('semester', $semester)
            ->with(['mataKuliahs' => function($query) use ($mahasiswa) {
                $query->where('prodi_id', $mahasiswa->prodi_id);
            }])
            ->first();

        $data['availableCourses'] = $availableCourses ? $availableCourses->mataKuliahs : collect();

        // Get all semesters that student has KRS for with status
        $semestersWithStatus = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('semester', 'desc')
            ->get(['semester', 'status'])
            ->groupBy('semester')
            ->map(function ($items) {
                return $items->first(); // Get the first (latest) KRS for each semester
            });

        $data['semestersWithStatus'] = $semestersWithStatus;
        $data['selectedSemester'] = $semester;

        return view('central.backpage.krs-index', $data);
    }

    public function create(Request $request)
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();
        $mahasiswa = Mahasantri::with(['prodi', 'kelas', 'taka'])->find($mahasiswaId);
        
        // Check if student is in terminal status
        if ($mahasiswa->isTerminal()) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            $message = 'Akses KRS tidak tersedia. Anda sedang dalam status terminal.';
            if ($terminalInfo && $terminalInfo['reason']) {
                $message .= ' Alasan: ' . $terminalInfo['reason'];
            }
            Alert::error('Akses Ditolak', $message);
            return redirect()->route('mahasiswa.krs.index');
        }
        
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['pages'] = "Buat KRS";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get semester from request or default to current semester
        $semester = $request->input('semester', $mahasiswa->semester);
        
        // Validate semester (should be between 1 and current semester or max 8)
        if ($semester < 1 || $semester > max($mahasiswa->semester, 8)) {
            Alert::error('Error', 'Semester tidak valid.');
            return redirect()->route('mahasiswa.krs.index');
        }

        // Check if KRS already exists for selected semester
        $existingKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->first();

        if ($existingKrs) {
            Alert::warning('Peringatan', 'KRS untuk semester ' . $semester . ' sudah ada. Silakan edit KRS yang sudah ada.');
            return redirect()->route('mahasiswa.krs.index');
        }

        // Get available courses for selected semester and program
        $availableCourses = MataKuliah::where('semester', $semester)
            ->when($mahasiswa->prodi_id, function($query) use ($mahasiswa) {
                return $query->where('prodi_id', $mahasiswa->prodi_id);
            })
            ->with(['dosen1', 'dosen2', 'dosen3', 'programStudi'])
            ->get();

        // If no courses found with prodi filter, try without prodi filter
        if ($availableCourses->isEmpty()) {
            $availableCourses = MataKuliah::where('semester', $semester)
                ->with(['dosen1', 'dosen2', 'dosen3', 'programStudi'])
                ->get();
        }

        $data['availableCourses'] = $availableCourses;
        $data['selectedSemester'] = $semester;

        return view('central.backpage.krs-create', $data);
    }

    public function edit($id)
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();
        $mahasiswa = Mahasantri::with(['prodi', 'kelas', 'taka'])->find($mahasiswaId);
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['pages'] = "Edit KRS";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->with('details')
            ->firstOrFail();

        // Get available courses for the semester and program
        $availableCourses = MataKuliah::where('semester', $krs->semester)
            ->when($mahasiswa->prodi_id, function($query) use ($mahasiswa) {
                return $query->where('prodi_id', $mahasiswa->prodi_id);
            })
            ->with(['dosen1', 'dosen2', 'dosen3', 'programStudi'])
            ->get();

        if ($availableCourses->isEmpty()) {
            $availableCourses = MataKuliah::where('semester', $krs->semester)
                ->with(['dosen1', 'dosen2', 'dosen3', 'programStudi'])
                ->get();
        }

        $data['availableCourses'] = $availableCourses;
        $data['krs'] = $krs;

        return view('central.backpage.krs-create', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'mata_kuliah_ids' => 'required|array',
            'mata_kuliah_ids.*' => 'exists:mata_kuliahs,id'
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();

        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if ($krs->status !== 'draft') {
            Alert::error('Error', 'KRS tidak dapat diubah karena sudah disubmit atau disetujui.');
            return redirect()->back();
        }

        // Delete existing details
        $krs->details()->delete();

        // Add updated courses
        $totalSks = 0;
        foreach ($request->mata_kuliah_ids as $mataKuliahId) {
            $mataKuliah = MataKuliah::find($mataKuliahId);
            $sks = (int)($mataKuliah->bsks ?? $mataKuliah->sks ?? 0);

            KrsDetailMahasiswa::create([
                'krs_mahasiswa_id' => $krs->id,
                'mata_kuliah_id' => $mataKuliahId,
                'sks' => $sks,
                'status' => 'aktif'
            ]);

            $totalSks += $sks;
        }

        $krs->update(['total_sks' => $totalSks]);

            // ==== TAMBAHKAN BAGIAN INI ====
            $isAjax = $request->expectsJson() || $request->ajax();
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'KRS berhasil diperbarui.',
                    'krs_id' => $krs->id, // Kirimkan kembali ID-nya agar terbaca oleh Javascript
                    'status' => $krs->status
                ]);
            }
        // ==============================

        Alert::success('Berhasil', 'KRS berhasil diperbarui.');
        return redirect()->route('mahasiswa.krs.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_ids' => 'required|array',
            'mata_kuliah_ids.*' => 'exists:mata_kuliahs,id',
            'semester' => 'required|integer|min:1|max:8'
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();
        $semester = $request->input('semester');
        $isAjax = $request->expectsJson() || $request->ajax();

        // Validate semester (should be between 1 and current semester or max 8)
        if ($semester < 1 || $semester > max($mahasiswa->semester, 8)) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester tidak valid.'
                ], 422);
            }

            Alert::error('Error', 'Semester tidak valid.');
            return redirect()->back();
        }

        // Check if KRS already exists for the specified semester
        $existingKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->first();

        if ($existingKrs) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'KRS untuk semester ' . $semester . ' sudah ada.'
                ], 422);
            }

            Alert::error('Error', 'KRS untuk semester ' . $semester . ' sudah ada.');
            return redirect()->back();
        }

        // Get current active academic year
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();

        if (!$tahunAkademik) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tahun akademik aktif.'
                ], 422);
            }

            Alert::error('Error', 'Tidak ada tahun akademik aktif.');
            return redirect()->back();
        }

        // Create KRS for the specified semester
        $krs = KrsMahasiswa::create([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester' => $semester, // Use the semester from request
            'tanggal_pengisian' => now(),
            'status' => 'draft',
            'created_by' => $mahasiswa->id
        ]);

        // Add selected courses to KRS
        $totalSks = 0;
        foreach ($request->mata_kuliah_ids as $mataKuliahId) {
            $mataKuliah = MataKuliah::find($mataKuliahId);
            $sks = (int)($mataKuliah->bsks ?? $mataKuliah->sks ?? 0);

            KrsDetailMahasiswa::create([
                'krs_mahasiswa_id' => $krs->id,
                'mata_kuliah_id' => $mataKuliahId,
                'sks' => $sks,
                'status' => 'aktif'
            ]);

            $totalSks += $sks;
        }

        // Update total SKS
        $krs->update(['total_sks' => $totalSks]);

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'KRS semester ' . $semester . ' berhasil dibuat.',
                'krs_id' => $krs->id,
                'semester' => $semester
            ]);
        }

        Alert::success('Berhasil', 'KRS semester ' . $semester . ' berhasil dibuat.');
        return redirect()->route('mahasiswa.krs.index');
    }

    public function show($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['details.mataKuliah.dosen1', 'tahunAkademik'])
            ->firstOrFail();

        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['pages'] = "Detail KRS";
        $data['menus'] = "Kartu Rencana Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['krs'] = $krs;

        return view('central.backpage.krs-show', $data);
    }

    public function print($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['details.mataKuliah.dosen1', 'tahunAkademik'])
            ->firstOrFail();

        if (!$krs->canBePrinted()) {
            Alert::error('Error', 'KRS belum dapat dicetak. Status harus disetujui terlebih dahulu.');
            return redirect()->back();
        }

        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['krs'] = $krs;

        return view('mahasantri.krs.krs-print-new', $data);
    }

    public function submit(Request $request, $id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $isAjax = $request->expectsJson() || $request->ajax();

        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if ($krs->status !== 'draft') {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'KRS sudah disubmit atau disetujui.'
                ], 422);
            }

            Alert::error('Error', 'KRS sudah disubmit atau disetujui.');
            return redirect()->back();
        }

        $krs->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        // Send WA notification to admin
        try {
            app(\App\Services\NotificationService::class)->notifyAdminKrsSubmitted($krs);
        } catch (\Exception $e) {
            Log::error('KRS submit notification trigger failed: ' . $e->getMessage());
        }

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'KRS berhasil disubmit untuk persetujuan.',
                'krs_id' => $krs->id,
                'status' => $krs->status
            ]);
        }

        Alert::success('Berhasil', 'KRS berhasil disubmit untuk persetujuan.');
        return redirect()->route('mahasiswa.krs.index');
    }

    public function exportPdf($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $krs = KrsMahasiswa::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['details.mataKuliah.dosen1', 'tahunAkademik', 'approvedBy', 'rejectedBy'])
            ->firstOrFail();

        if (!$krs->canBePrinted()) {
            Alert::error('Error', 'KRS belum dapat diexport. Status harus disetujui terlebih dahulu.');
            return redirect()->back();
        }

        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        $data['krs'] = $krs;

        // Generate PDF
        $pdf = Pdf::loadView('mahasantri.krs.krs-print-new', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Set options for better rendering
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Times-Roman',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
        ]);

        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $mahasiswa->name));
        $filename = 'Krs-Sem' . $krs->semester . '-' . $safeName . '-' . $mahasiswa->nim . '-' . date('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }
}
