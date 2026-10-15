<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mahasantri;
use App\Models\Dosen;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\GradeScaleSetting;

class NilaiMahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nilai_mahasiswa';
    protected $guarded = [];

    protected $casts = [
        'tugas' => 'decimal:2',
        'quiz' => 'decimal:2',
        'uts' => 'decimal:2',
        'uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    // Relationships
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class, 'mahasiswa_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    // Helper methods
    public function getGradePointAttribute()
    {
        return $this->convertToGradePoint($this->nilai_akhir);
    }

    public function getLetterGradeAttribute()
    {
        return $this->convertToLetterGrade($this->nilai_akhir);
    }

    /**
     * Convert numerical score to letter grade using configurable grade scales
     * Falls back to hardcoded values if no grade scales are configured
     */
    public static function convertToLetterGrade($nilai)
    {
        // Try to use configurable grade scales first
        try {
            return GradeScaleSetting::convertToLetterGrade($nilai);
        } catch (\Exception $e) {
            // Fallback to hardcoded values for backward compatibility
            if ($nilai >= 85) return 'A';
            if ($nilai >= 80) return 'A-';
            if ($nilai >= 75) return 'B+';
            if ($nilai >= 70) return 'B';
            if ($nilai >= 65) return 'B-';
            if ($nilai >= 60) return 'C+';
            if ($nilai >= 55) return 'C';
            if ($nilai >= 50) return 'C-';
            if ($nilai >= 45) return 'D+';
            if ($nilai >= 40) return 'D';
            return 'E';
        }
    }

    /**
     * Convert numerical score to grade point using configurable grade scales
     * Falls back to hardcoded values if no grade scales are configured
     */
    public static function convertToGradePoint($nilai)
    {
        // Try to use configurable grade scales first
        try {
            return GradeScaleSetting::convertToGradePoint($nilai);
        } catch (\Exception $e) {
            // Fallback to hardcoded values for backward compatibility
            if ($nilai >= 85) return 4.00;
            if ($nilai >= 80) return 3.70;
            if ($nilai >= 75) return 3.30;
            if ($nilai >= 70) return 3.00;
            if ($nilai >= 65) return 2.70;
            if ($nilai >= 60) return 2.30;
            if ($nilai >= 55) return 2.00;
            if ($nilai >= 50) return 1.70;
            if ($nilai >= 45) return 1.30;
            if ($nilai >= 40) return 1.00;
            return 0.00;
        }
    }

    public static function calculateGPA($mahasiswaId, $semester = null, $bobotNilaiSettings = null)
    {
        $query = self::where('mahasiswa_id', $mahasiswaId)
                    ->with('mataKuliah');

        // Filter berdasarkan semester jika dipilih
        if ($semester !== null && $semester !== '') {
            // OPSI 1: Jika semester ada di tabel mata_kuliah
            $query->whereHas('mataKuliah', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
            
            // OPSI 2: Jika semester ada di tabel krs_mahasiswa
            // $query->whereExists(function($q) use ($mahasiswaId, $semester) {
            //     $q->select(\DB::raw(1))
            //       ->from('krs_mahasiswa')
            //       ->whereRaw('krs_mahasiswa.mahasiswa_id = nilai_mahasiswa.mahasiswa_id')
            //       ->whereRaw('krs_mahasiswa.mata_kuliah_id = nilai_mahasiswa.mata_kuliah_id')
            //       ->where('krs_mahasiswa.semester', $semester);
            // });
        }

        $grades = $query->get();
        
        if ($grades->isEmpty()) {
            return 0.00;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $credits = $grade->mataKuliah->bsks ?? 1;
            $gradePoint = self::convertToGradePoint($grade->nilai_akhir);
            
            $totalPoints += ($gradePoint * $credits);
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;
    }

    public static function getTranscript($mahasiswaId, $bobotNilaiSettings = null)
    {
        return self::where('mahasiswa_id', $mahasiswaId)
                  ->with(['mataKuliah', 'dosen'])
                  ->orderBy('created_at')
                  ->get();
    }

    public static function getSemesterGrades($mahasiswaId, $semester = null, $bobotNilaiSettings = null)
    {
        $query = self::where('mahasiswa_id', $mahasiswaId)
                    ->with(['mataKuliah', 'dosen']);

        // PERBAIKAN UTAMA: Filter berdasarkan semester
        if ($semester !== null && $semester !== '') {
            // OPSI 1: Jika field semester ada di tabel mata_kuliah (paling umum)
            $query->whereHas('mataKuliah', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
            
            // OPSI 2: Jika semester disimpan di tabel nilai_mahasiswa
            // Uncomment jika tabel nilai_mahasiswa punya field semester
            // $query->where('semester', $semester);
            
            // OPSI 3: Jika semester diambil dari tabel krs_mahasiswa
            // $query->whereExists(function($q) use ($mahasiswaId, $semester) {
            //     $q->select(\DB::raw(1))
            //       ->from('krs_mahasiswa')
            //       ->whereRaw('krs_mahasiswa.mahasiswa_id = nilai_mahasiswa.mahasiswa_id')
            //       ->whereRaw('krs_mahasiswa.mata_kuliah_id = nilai_mahasiswa.mata_kuliah_id')
            //       ->where('krs_mahasiswa.semester', $semester);
            // });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}