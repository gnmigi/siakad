<?php

namespace App\Http\Controllers\Private\Mahasantri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\NilaiMahasiswa;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\BobotNilaiSetting;
use App\Models\Akademik\TahunAkademik;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class KhsController extends Controller
{
    /**
     * Check if mahasiswa has any approved KRS.
     */
    private function hasApprovedKrs($mahasiswaId)
    {
        if (!Schema::hasTable('krs_mahasiswa')) {
            return false;
        }

        return DB::table('krs_mahasiswa')
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get approved KRS semesters for mahasiswa (Masih dipertahankan untuk referensi lama).
     */
    private function getApprovedKrsSemesters($mahasiswaId)
    {
        if (!Schema::hasTable('krs_mahasiswa')) {
            return collect();
        }

        $krsData = DB::table('krs_mahasiswa')
            ->leftJoin('tahun_akademiks', 'krs_mahasiswa.tahun_akademik_id', '=', 'tahun_akademiks.id')
            ->where('krs_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->where('krs_mahasiswa.status', 'approved')
            ->select(
                'krs_mahasiswa.semester',
                'tahun_akademiks.name as tahun_akademik_name',
                'tahun_akademiks.id as tahun_akademik_id'
            )
            ->orderBy('krs_mahasiswa.semester')
            ->get();

        $availableSemesters = collect();
        foreach ($krsData as $krs) {
            $availableSemesters->push([
                'semester' => (int) $krs->semester,
                'tahun_akademik_id' => $krs->tahun_akademik_id,
                'tahun_akademik_name' => $krs->tahun_akademik_name ?? 'Tahun Akademik Tidak Diketahui',
                'display_text' => 'Semester ' . $krs->semester . ' - ' . ($krs->tahun_akademik_name ?? 'Tahun Akademik Tidak Diketahui')
            ]);
        }

        return $availableSemesters;
    }

    /**
     * Get ALL semesters up to current semester (Untuk menampilkan Dropdown Semua Semester).
     */
    private function getAllStudentSemesters($mahasiswaId, $currentSemester)
    {
        if (!Schema::hasTable('krs_mahasiswa')) {
            return collect();
        }

        $krsData = DB::table('krs_mahasiswa')
            ->leftJoin('tahun_akademiks', 'krs_mahasiswa.tahun_akademik_id', '=', 'tahun_akademiks.id')
            ->where('krs_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->select(
                'krs_mahasiswa.semester',
                'tahun_akademiks.name as tahun_akademik_name',
                'krs_mahasiswa.status'
            )
            ->get()
            ->keyBy('semester');

        $availableSemesters = collect();
        // Ambil dari semester 1 sampai semester mahasiswa saat ini
        $maxSemester = max(1, $currentSemester);
        
        for ($i = 1; $i <= $maxSemester; $i++) {
            $krs = $krsData->get($i);
            $availableSemesters->push([
                'semester' => $i,
                'display_text' => 'Semester ' . $i . ($krs && $krs->tahun_akademik_name ? ' - ' . $krs->tahun_akademik_name : '')
            ]);
        }

        return $availableSemesters;
    }
    
    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Check if student is in terminal status
        if ($mahasiswa->isTerminal()) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            $message = 'Akses KHS tidak tersedia. Anda sedang dalam status terminal.';
            if ($terminalInfo && $terminalInfo['reason']) {
                $message .= ' Alasan: ' . $terminalInfo['reason'];
            }
            return redirect()->route('mahasiswa.dashboard-render')
                ->with('error', $message)
                ->with('alert-type', 'error')
                ->with('alert-title', 'Akses Ditolak');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'mahasiswa.';
        $data['menus'] = "KHS";
        $data['pages'] = "Kartu Hasil Studi";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Tampilkan semua semester (dari semester 1 s/d semester saat ini)
        $availableSemesters = $this->getAllStudentSemesters($mahasiswa->id, $mahasiswa->semester);
        $data['availableSemesters'] = $availableSemesters;
        
        // Get available academic years for backward compatibility
        $data['tahunAkademik'] = TahunAkademik::orderBy('name', 'desc')->get();
        
        // Dapatkan parameter semester, jika tidak ada, jadikan semester dia saat ini sebagai default
        $selectedSemester = $request->get('semester');
        if (!$selectedSemester) {
            $selectedSemester = $mahasiswa->semester;
        }
        $selectedSemester = (int) $selectedSemester;
        $data['selectedSemester'] = $selectedSemester;

        // Cek status KRS pada semester yang dipilih
        $krsStatus = DB::table('krs_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $selectedSemester)
            ->value('status');

        $isKrsFilled = ($krsStatus === 'approved');
        $data['isKrsFilled'] = $isKrsFilled;
        $data['krsStatus'] = $krsStatus; // Variabel baru untuk logika dinamis di Blade
        
        // Get active grade components from settings
        $data['bobotNilaiSettings'] = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Jika KRS sudah di-ACC, tarik nilai. Jika belum, biarkan kosong agar view menampilkan pesan.
        if ($isKrsFilled) {
            $data['grades'] = NilaiMahasiswa::getSemesterGrades($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
            $data['semesterGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
        } else {
            $data['grades'] = collect(); // Kosongkan collection
            $data['semesterGPA'] = 0;
        }
        
        // Calculate cumulative GPA
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $data['bobotNilaiSettings']);

        return view('central.backpage.mahasiswa-khs', $data, compact('mahasiswa'));
    }

    public function print(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Get selected semester
        $selectedSemester = $request->get('semester');
        if (!$selectedSemester) {
            $selectedSemester = $mahasiswa->semester;
        }
        $selectedSemester = (int) $selectedSemester;

        // Cek secara spesifik apakah KRS semester ini sudah approved
        $krsStatus = DB::table('krs_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $selectedSemester)
            ->value('status');

        if ($krsStatus !== 'approved') {
            return redirect()->route('mahasiswa.khs.index')
                ->with('error', 'KHS semester yang dipilih belum dapat dicetak karena KRS belum di-ACC admin.');
        }
        
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        
        // Get active grade components
        $data['bobotNilaiSettings'] = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Get grades for the selected semester
        $data['grades'] = NilaiMahasiswa::getSemesterGrades($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
        
        // Calculate GPA with current weights
        $data['semesterGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
        
        // Calculate cumulative GPA
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $data['bobotNilaiSettings']);
        
        // Get tahun akademik name dan semester info
        $tahunAkademikName = 'Semua Semester';
        $semesterInfo = '';
        
        if ($selectedSemester && Schema::hasTable('krs_mahasiswa')) {
            $krsData = DB::table('krs_mahasiswa')
                ->leftJoin('tahun_akademiks', 'krs_mahasiswa.tahun_akademik_id', '=', 'tahun_akademiks.id')
                ->where('krs_mahasiswa.mahasiswa_id', $mahasiswa->id)
                ->where('krs_mahasiswa.semester', $selectedSemester)
                ->select('tahun_akademiks.name as tahun_akademik_name')
                ->first();
            
            if ($krsData && $krsData->tahun_akademik_name) {
                $tahunAkademikName = $krsData->tahun_akademik_name;
                $semesterInfo = 'Semester ' . $selectedSemester;
            } else {
                $tahunAkademikName = 'Tahun Akademik Tidak Diketahui';
                $semesterInfo = 'Semester ' . $selectedSemester;
            }
        }
        
        $data['selectedSemester'] = $selectedSemester;
        $data['tahunAkademikName'] = $tahunAkademikName;
        $data['semesterInfo'] = $semesterInfo;
        $data['printDate'] = now()->format('d/m/Y H:i:s');

        return view('central.backpage.mahasiswa-khs-print', $data);
    }

    public function exportPdf(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Get selected semester
        $selectedSemester = $request->get('semester');
        if (!$selectedSemester) {
            $selectedSemester = $mahasiswa->semester;
        }
        $selectedSemester = (int) $selectedSemester;

        // Cek secara spesifik apakah KRS semester ini sudah approved
        $krsStatus = DB::table('krs_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $selectedSemester)
            ->value('status');

        if ($krsStatus !== 'approved') {
            return redirect()->route('mahasiswa.khs.index')
                ->with('error', 'KHS semester yang dipilih belum dapat didownload karena KRS belum di-ACC admin.');
        }
        
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        
        // Get active grade components
        $data['bobotNilaiSettings'] = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Get grades for the selected semester
        $data['grades'] = NilaiMahasiswa::getSemesterGrades($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
        
        // Calculate GPA with current weights
        $data['semesterGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, $selectedSemester, $data['bobotNilaiSettings']);
        
        // Calculate cumulative GPA
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $data['bobotNilaiSettings']);
        
        // Get tahun akademik name dan semester info
        $tahunAkademikName = 'Semua Semester';
        $semesterInfo = '';
        
        if ($selectedSemester && Schema::hasTable('krs_mahasiswa')) {
            $krsData = DB::table('krs_mahasiswa')
                ->leftJoin('tahun_akademiks', 'krs_mahasiswa.tahun_akademik_id', '=', 'tahun_akademiks.id')
                ->where('krs_mahasiswa.mahasiswa_id', $mahasiswa->id)
                ->where('krs_mahasiswa.semester', $selectedSemester)
                ->select('tahun_akademiks.name as tahun_akademik_name')
                ->first();
            
            if ($krsData && $krsData->tahun_akademik_name) {
                $tahunAkademikName = $krsData->tahun_akademik_name;
                $semesterInfo = 'Semester ' . $selectedSemester;
            } else {
                $tahunAkademikName = 'Tahun Akademik Tidak Diketahui';
                $semesterInfo = 'Semester ' . $selectedSemester;
            }
        }
        
        $data['selectedSemester'] = $selectedSemester;
        $data['tahunAkademikName'] = $tahunAkademikName;
        $data['semesterInfo'] = $semesterInfo;
        $data['printDate'] = now()->format('d/m/Y H:i:s');
        $data['isPdf'] = true;

        // Generate PDF
        $pdf = Pdf::loadView('central.backpage.mahasiswa-khs-print', $data);
        $pdf->setPaper('A4', 'landscape');
        
        // Set PDF options
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Times-Roman',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
        ]);

        // Generate filename
        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $mahasiswa->name));
        $filename = 'KHS-' . ($selectedSemester ? 'Sem' . $selectedSemester : 'All') . '-' . $safeName . '-' . ($mahasiswa->numb_nim ?? $mahasiswa->nim) . '-' . date('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function transkrip()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Check if mahasiswa has any approved KRS (keep behavior equivalent for non-KHS pages)
        $hasKRS = $this->hasApprovedKrs($mahasiswa->id);

        if (!$hasKRS) {
            return redirect()->route('mahasiswa.krs.index')
                ->with('error', 'Anda harus mengisi KRS terlebih dahulu sebelum dapat melihat transkrip nilai. Silakan isi KRS Anda.')
                ->with('info', 'KRS (Kartu Rencana Studi) diperlukan untuk dapat mengakses transkrip nilai.');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'mahasiswa.';
        $data['menus'] = "Transkrip";
        $data['pages'] = "Transkrip Nilai";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get active grade components
        $data['bobotNilaiSettings'] = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // Get all grades (transcript)
        $data['grades'] = NilaiMahasiswa::getTranscript($mahasiswa->id, $data['bobotNilaiSettings']);
        
        // Calculate cumulative GPA with current weights
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $data['bobotNilaiSettings']);
        
        // Calculate total credits
        $data['totalCredits'] = $data['grades']->sum(function($grade) {
            return $grade->mataKuliah->bsks ?? 0;
        });

        return view('central.backpage.mahasiswa-transkrip', $data, compact('mahasiswa'));
    }

    public function transkripPdf()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Check if mahasiswa has any approved KRS (keep behavior equivalent for non-KHS pages)
        $hasKRS = $this->hasApprovedKrs($mahasiswa->id);

        if (!$hasKRS) {
            return redirect()->route('mahasiswa.krs.index')
                ->with('error', 'Anda harus mengisi KRS terlebih dahulu sebelum dapat mendownload transkrip nilai. Silakan isi KRS Anda.')
                ->with('info', 'KRS (Kartu Rencana Studi) diperlukan untuk dapat mendownload transkrip nilai.');
        }
        
        $data['webs'] = WebSetting::first();
        $data['mahasiswa'] = $mahasiswa;
        
        // Get active grade components
        $data['bobotNilaiSettings'] = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // Get all grades (transcript)
        $data['grades'] = NilaiMahasiswa::getTranscript($mahasiswa->id, $data['bobotNilaiSettings']);
        
        // Calculate cumulative GPA with current weights
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $data['bobotNilaiSettings']);
        
        // Calculate total credits
        $data['totalCredits'] = $data['grades']->sum(function($grade) {
            return $grade->mataKuliah->bsks ?? 0;
        });
        
        $data['printDate'] = now()->format('d/m/Y H:i:s');
        $data['isPdf'] = true;

        // Generate PDF using the new print template
        $pdf = Pdf::loadView('central.backpage.mahasiswa-transkrip-print', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Set PDF options
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Times-Roman',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
        ]);

        // Generate filename
        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $mahasiswa->name));
        $filename = 'Transkrip-' . $safeName . '-' . ($mahasiswa->numb_nim ?? $mahasiswa->nim) . '-' . date('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function kelulusan()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Check if mahasiswa has any approved KRS (keep behavior equivalent for non-KHS pages)
        $hasKRS = $this->hasApprovedKrs($mahasiswa->id);

        if (!$hasKRS) {
            return redirect()->route('mahasiswa.krs.index')
                ->with('error', 'Anda harus mengisi KRS terlebih dahulu sebelum dapat melihat status kelulusan. Silakan isi KRS Anda.')
                ->with('info', 'KRS (Kartu Rencana Studi) diperlukan untuk dapat mengakses informasi status kelulusan.');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'mahasiswa.';
        $data['menus'] = "Status Kelulusan";
        $data['pages'] = "Cek Status Kelulusan";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get active grade components
        $bobotNilaiSettings = BobotNilaiSetting::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // Get all grades
        $grades = NilaiMahasiswa::getTranscript($mahasiswa->id, $bobotNilaiSettings);
        
        // Calculate cumulative GPA with current weights
        $data['cumulativeGPA'] = NilaiMahasiswa::calculateGPA($mahasiswa->id, null, $bobotNilaiSettings);
        
        // Calculate total credits
        $data['totalCredits'] = $grades->sum(function($grade) {
            return $grade->mataKuliah->bsks ?? 0;
        });
        
        // Get graduation requirements from web settings
        $webSettings = WebSetting::first();
        $data['minGPA'] = $webSettings->min_gpa_graduation ?? 2.75;
        $data['minCredits'] = $webSettings->min_credits_graduation ?? 144;
        
        // Check graduation status
        $data['gpaStatus'] = $data['cumulativeGPA'] >= $data['minGPA'];
        $data['creditsStatus'] = $data['totalCredits'] >= $data['minCredits'];
        $data['graduationStatus'] = $data['gpaStatus'] && $data['creditsStatus'];
        
        // Count failed courses (grade E)
        $data['failedCourses'] = $grades->filter(function($grade) {
            try {
                return \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($grade->nilai_akhir) === 'E';
            } catch (\Exception $e) {
                // Fallback to NilaiMahasiswa method if GradeScaleSetting fails
                return NilaiMahasiswa::convertToLetterGrade($grade->nilai_akhir) === 'E';
            }
        })->count();

        return view('central.backpage.mahasiswa-kelulusan', $data, compact('mahasiswa'));
    }
}