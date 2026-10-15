<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasantri;
use App\Models\Mahasantri\KrsMahasiswa;
use App\Models\Mahasantri\KrsDetailMahasiswa;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\TahunAkademik;
use App\Models\NilaiMahasiswa;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Keuangan\PaymentTransaction;

class MahasiswaController extends Controller
{
    /**
     * Get Mahasiswa Profile
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'nim' => $user->numb_nim ?? '-',
                'photo' => $user->photo,
                'prodi' => $user->prodi->name ?? '-',
                'kelas' => $user->kelas->name ?? '-'
            ]
        ]);
    }

    /**
     * Get Dashboard Summary for Mahasiswa
     */
    public function getDashboard(Request $request)
    {
        $user = $request->user();
        $user->load(['prodi', 'kelas', 'taka', 'dosenPenasehat']);

        $tagihanPending = $user->tagihanKuliah()->where('status', 'Pending')->sum('amount');

        // Calculate actual statistics
        $totalMataKuliah = 0;
        $tugasPending = 0;
        $kehadiran = 0;

        $currentTahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
        if ($currentTahunAkademik) {
            $currentKrs = KrsMahasiswa::where('mahasiswa_id', $user->id)
                ->where('tahun_akademik_id', $currentTahunAkademik->id)
                ->where('semester', $user->semester)
                ->first();

            if (!$currentKrs) {
                $currentKrs = KrsMahasiswa::where('mahasiswa_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('semester', 'desc')
                    ->first();
            }

            if ($currentKrs) {
                $krsDetails = KrsDetailMahasiswa::where('krs_mahasiswa_id', $currentKrs->id)->get();
                $totalMataKuliah = $krsDetails->count();
                $mataKuliahIds = $krsDetails->pluck('mata_kuliah_id');

                // 1. Calculate Kehadiran (Attendance Percentage)
                $kelasName = $user->kelas->name ?? '';
                if ($mataKuliahIds->isNotEmpty() && !empty($kelasName)) {
                    $cleanKel = $kelasName;
                    $exactKel = $kelasName . ' (Semester Aktif ' . $currentKrs->semester . ')';
                    
                    $meetings = \App\Models\Akademik\PertemuanKuliah::whereIn('mata_kuliah_id', $mataKuliahIds)
                        ->where(function($q) use ($cleanKel, $exactKel) {
                            $q->where('kelompok', $exactKel)
                              ->orWhere('kelompok', 'like', $cleanKel . ' %')
                              ->orWhere('kelompok', $cleanKel);
                        })
                        ->get();
                    
                    $meetingIds = $meetings->pluck('id');
                    
                    if ($meetingIds->isNotEmpty()) {
                        $totalMeetings = $meetingIds->count();
                        
                        $totalHadir = \App\Models\Akademik\AbsensiMahasiswa::whereIn('pertemuan_id', $meetingIds)
                            ->where('mahasiswa_id', $user->id)
                            ->where('status', 'Hadir')
                            ->count();
                        
                        $kehadiran = round(($totalHadir / $totalMeetings) * 100, 2);
                    }
                }

                // 2. Calculate Tugas Pending
                $gradedCoursesCount = NilaiMahasiswa::where('mahasiswa_id', $user->id)
                    ->whereIn('mata_kuliah_id', $mataKuliahIds)
                    ->whereNotNull('nilai_akhir')
                    ->count();
                
                $tugasPending = max(0, $totalMataKuliah - $gradedCoursesCount);
            }
        }

        $stats = [
            'semester_aktif' => $user->semester ?? 1,
            'total_mata_kuliah' => $totalMataKuliah,
            'tugas_pending' => $tugasPending,
            'kehadiran' => $kehadiran . '%',
            'total_tagihan' => 'Rp ' . number_format($tagihanPending, 0, ',', '.'),
            'tagihan_pending' => 'Rp ' . number_format($tagihanPending, 0, ',', '.'),
            'total_terbayar' => 'Rp 0',
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_info' => [
                    'name' => $user->name,
                    'nim' => $user->numb_nim,
                    'prodi' => $user->prodi->name ?? '-',
                    'kelas' => $user->kelas->name ?? '-',
                    'tahun_akademik' => $user->taka->name ?? '-',
                    'semester' => $user->semester_active ?? '-',
                    'status' => $user->type,
                ],
                'stats' => $stats,
                'dosen_penasehat' => [
                    'name' => $user->dosenPenasehat->name ?? '-',
                    'email' => $user->dosenPenasehat->email ?? '-',
                    'photo' => $user->dosenPenasehat->photo ?? null,
                ]
            ]
        ]);
    }

    /**
     * Get KRS for Mobile
     */
    public function getKrs(Request $request)
    {
        $user = $request->user();
        $semester = $request->get('semester', $user->semester_active ?? 1);

        $krs = KrsMahasiswa::where('mahasiswa_id', $user->id)
            ->where('semester', $semester)
            ->with(['details.mataKuliah.dosen1', 'tahunAkademik'])
            ->first();

        $availableCourses = MataKuliah::where('semester', $semester)
            ->where('prodi_id', $user->prodi_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'current_krs' => $krs,
                'available_courses' => $availableCourses,
                'selected_semester' => (int)$semester
            ]
        ]);
    }

    /**
     * Save KRS from Mobile
     */
    public function saveKrs(Request $request)
    {
        $request->validate([
            'mata_kuliah_ids' => 'required|array',
            'semester' => 'required|integer'
        ]);

        $user = $request->user();
        $semester = $request->semester;
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();

        if (!$tahunAkademik) {
            return response()->json(['status' => 'error', 'message' => 'Tahun akademik aktif tidak ditemukan'], 422);
        }

        DB::beginTransaction();
        try {
            $krs = KrsMahasiswa::updateOrCreate(
                ['mahasiswa_id' => $user->id, 'semester' => $semester],
                ['tahun_akademik_id' => $tahunAkademik->id, 'tanggal_pengisian' => now(), 'status' => 'draft']
            );

            $krs->details()->delete();
            $totalSks = 0;

            foreach ($request->mata_kuliah_ids as $mkId) {
                $mk = MataKuliah::find($mkId);
                if ($mk) {
                    $sks = $mk->bsks ?? $mk->sks ?? 0;
                    KrsDetailMahasiswa::create([
                        'krs_mahasiswa_id' => $krs->id,
                        'mata_kuliah_id' => $mkId,
                        'sks' => $sks,
                        'status' => 'aktif'
                    ]);
                    $totalSks += $sks;
                }
            }

            $krs->update(['total_sks' => $totalSks]);
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'KRS berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get KHS for Mobile
     */
    public function getKhs(Request $request)
    {
        $user = $request->user();
        $semester = $request->get('semester', $user->semester_active ?? 1);

        $grades = NilaiMahasiswa::getSemesterGrades($user->id, $semester);
        $gpa = NilaiMahasiswa::calculateGPA($user->id, $semester);

        return response()->json([
            'status' => 'success',
            'data' => [
                'grades' => $grades,
                'semester_gpa' => $gpa,
                'selected_semester' => (int)$semester
            ]
        ]);
    }

    /**
     * Get Transcript for Mobile
     */
    public function getTranskrip(Request $request)
    {
        $user = $request->user();
        $grades = NilaiMahasiswa::getTranscript($user->id);
        $cgpa = NilaiMahasiswa::calculateGPA($user->id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'grades' => $grades,
                'cumulative_gpa' => $cgpa
            ]
        ]);
    }

    /**
     * Get Tagihan for Mobile
     */
    public function getTagihan(Request $request)
    {
        $user = $request->user();
        $tagihan = TagihanKuliah::where('mahasiswa_id', $user->id)
            ->whereIn('status', ['Pending', 'Cicilan'])
            ->with('group')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tagihan
        ]);
    }

    /**
     * Get Payment History for Mobile
     */
    public function getPaymentHistory(Request $request)
    {
        $user = $request->user();
        $history = PaymentTransaction::where('mahasiswa_id', $user->id)
            ->with(['tagihan', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
}
