<?php

namespace App\Services;

use App\Models\Akademik\TahunAkademik;
use App\Models\Mahasantri;
use App\Services\SmartBillingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AcademicYearAdvancementService
{
    /**
     * Preview what will happen when advancing academic year
     *
     * @return array
     */
    public function previewAdvancement()
    {
        $currentAcademicYear = TahunAkademik::where('status', 'Aktif')->first();
        
        if (!$currentAcademicYear) {
            throw new \Exception('Tidak ada tahun akademik yang aktif saat ini.');
        }

        // Generate next academic year info
        $nextAcademicYearInfo = $this->generateNextAcademicYearInfo($currentAcademicYear);
        
        // Get student advancement statistics
        $studentStats = $this->getStudentAdvancementStats();
        
        return [
            'current_academic_year' => [
                'name' => $currentAcademicYear->name,
                'type' => $currentAcademicYear->type,
                'period' => Carbon::parse($currentAcademicYear->start_date)->format('d M Y') . ' - ' . Carbon::parse($currentAcademicYear->ended_date)->format('d M Y')
            ],
            'next_academic_year' => $nextAcademicYearInfo,
            'student_statistics' => $studentStats,
            'total_affected_students' => $studentStats['total_advancing'] + $studentStats['total_dropped_out']
        ];
    }

    /**
     * Generate information for the next academic year
     *
     * @param TahunAkademik $currentAcademicYear
     * @return array
     */
    private function generateNextAcademicYearInfo($currentAcademicYear)
    {
        // Determine next semester type
        $nextType = $currentAcademicYear->type === 'Ganjil' ? 'Genap' : 'Ganjil';
        
        // Generate next academic year name
        $nextName = $this->generateNextAcademicYearName($currentAcademicYear->name, $currentAcademicYear->type);
        
        // Calculate next period dates based on academic calendar standards
        $dates = $this->calculateAcademicYearDates($currentAcademicYear, $nextType);
        
        return [
            'name' => $nextName,
            'type' => $nextType,
            'start_date' => $dates['start_date'],
            'ended_date' => $dates['ended_date'],
            'period' => Carbon::parse($dates['start_date'])->format('d M Y') . ' - ' . Carbon::parse($dates['ended_date'])->format('d M Y'),
            'description' => "Tahun akademik {$nextName} semester {$nextType} - Dibuat otomatis pada " . now()->format('d M Y H:i')
        ];
    }

    /**
     * Calculate academic year dates based on Indonesian academic calendar standards
     *
     * @param TahunAkademik $currentAcademicYear
     * @param string $nextType
     * @return array
     */
    private function calculateAcademicYearDates($currentAcademicYear, $nextType)
    {
        $currentEndDate = Carbon::parse($currentAcademicYear->ended_date);
        $currentYear = $currentEndDate->year;
        
        if ($nextType === 'Ganjil') {
            // Semester Ganjil: Agustus - Januari
            // Mulai pertengahan Agustus, berakhir akhir Januari tahun berikutnya
            $startDate = Carbon::create($currentYear, 8, 15); // 15 Agustus
            $endDate = Carbon::create($currentYear + 1, 1, 31); // 31 Januari tahun berikutnya
            
            // Jika tanggal sekarang sudah lewat Agustus, maka tahun berikutnya
            if ($currentEndDate->month >= 8) {
                $startDate->addYear();
                $endDate->addYear();
            }
        } else {
            // Semester Genap: Februari - Juli
            // Mulai awal Februari, berakhir akhir Juli
            $startDate = Carbon::create($currentYear, 2, 1); // 1 Februari
            $endDate = Carbon::create($currentYear, 7, 31); // 31 Juli
            
            // Jika tanggal sekarang sudah lewat Juli, maka tahun berikutnya
            if ($currentEndDate->month >= 8) {
                $startDate->addYear();
                $endDate->addYear();
            }
        }
        
        // Tambahkan jeda libur semester (2 minggu setelah semester sebelumnya berakhir)
        $minimumStartDate = $currentEndDate->copy()->addWeeks(2);
        if ($startDate->lt($minimumStartDate)) {
            $startDate = $minimumStartDate;
            
            // Adjust end date accordingly (maintain semester duration)
            if ($nextType === 'Ganjil') {
                $endDate = $startDate->copy()->addMonths(5)->addWeeks(2); // ~5.5 bulan
            } else {
                $endDate = $startDate->copy()->addMonths(5)->addWeeks(2); // ~5.5 bulan
            }
        }
        
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'ended_date' => $endDate->format('Y-m-d')
        ];
    }

    /**
     * Generate next academic year name based on current name and type
     *
     * @param string $currentName
     * @param string $currentType
     * @return string
     */
    private function generateNextAcademicYearName($currentName, $currentType)
    {
        // Extract year from current name (e.g., "2024/2025" or "2024-2025")
        if (preg_match('/(\d{4})[\/-](\d{4})/', $currentName, $matches)) {
            $startYear = (int)$matches[1];
            $endYear = (int)$matches[2];
            
            if ($currentType === 'Ganjil') {
                // If current is Ganjil, next Genap uses same academic year
                return $currentName;
            } else {
                // If current is Genap, next Ganjil uses next academic year
                $nextStartYear = $startYear + 1;
                $nextEndYear = $endYear + 1;
                return "{$nextStartYear}/{$nextEndYear}";
            }
        }
        
        // Fallback: try to increment year in the name
        return preg_replace_callback('/\d{4}/', function($matches) {
            return (int)$matches[0] + 1;
        }, $currentName);
    }

    /**
     * Get statistics about student advancement
     *
     * @return array
     */
    private function getStudentAdvancementStats()
    {
        // Use more efficient query with groupBy and selectRaw
        $semesterCounts = Mahasantri::where('type', 1)
            ->selectRaw('semester, COUNT(*) as count')
            ->groupBy('semester')
            ->pluck('count', 'semester')
            ->toArray();
        
        $totalActiveStudents = array_sum($semesterCounts);
        
        $stats = [
            'total_active_students' => $totalActiveStudents,
            'total_advancing' => 0,
            'total_dropped_out' => 0,
            'semester_distribution' => [],
            'dropped_out_students' => []
        ];

        foreach ($semesterCounts as $currentSemester => $studentCount) {
            $nextSemester = $currentSemester + 1;
            
            if ($nextSemester > 14) {
                // Students who will be dropped out (semester > 14)
                $stats['total_dropped_out'] += $studentCount;
                
                // Only get detailed student info if count is reasonable (< 100)
                if ($studentCount <= 100) {
                    $dropoutStudents = Mahasantri::where('type', 1)
                        ->where('semester', $currentSemester)
                        ->with('prodi:id,name')
                        ->select('id', 'name', 'numb_nim', 'prodi_id')
                        ->get()
                        ->map(function($student) {
                            return [
                                'id' => $student->id,
                                'name' => $student->name,
                                'nim' => $student->numb_nim,
                                'prodi' => $student->prodi->name ?? 'N/A'
                            ];
                        });
                    
                    $stats['dropped_out_students'][] = [
                        'current_semester' => $currentSemester,
                        'count' => $studentCount,
                        'students' => $dropoutStudents
                    ];
                } else {
                    // For large numbers, just show count without detailed list
                    $stats['dropped_out_students'][] = [
                        'current_semester' => $currentSemester,
                        'count' => $studentCount,
                        'students' => [] // Empty for performance
                    ];
                }
            } else {
                // Students who will advance to next semester
                $stats['total_advancing'] += $studentCount;
                $stats['semester_distribution'][] = [
                    'current_semester' => $currentSemester,
                    'next_semester' => $nextSemester,
                    'count' => $studentCount
                ];
            }
        }

        return $stats;
    }

    /**
     * Execute the academic year advancement
     *
     * @return array
     */
    public function executeAdvancement()
    {
        try {
            DB::beginTransaction();

            $currentAcademicYear = TahunAkademik::where('status', 'Aktif')->first();
            
            if (!$currentAcademicYear) {
                throw new \Exception('Tidak ada tahun akademik yang aktif saat ini.');
            }

            // Get preview data for logging
            $previewData = $this->previewAdvancement();
            
            // Create next academic year
            $nextAcademicYear = $this->createNextAcademicYear($currentAcademicYear);
            
            // Advance students
            $advancementResults = $this->advanceStudents();
            
            // Deactivate current academic year
            $currentAcademicYear->update(['status' => 'Tidak Aktif']);
            
            // Activate next academic year
            $nextAcademicYear->update(['status' => 'Aktif']);

            DB::commit();

            // Auto-generate billing for the new academic year
            $billingResults = $this->autoGenerateBilling($nextAcademicYear->id);

            // Log the advancement
            Log::info('Academic Year Advanced Successfully', [
                'previous_academic_year' => $currentAcademicYear->name,
                'new_academic_year' => $nextAcademicYear->name,
                'students_advanced' => $advancementResults['advanced_count'],
                'students_dropped_out' => $advancementResults['dropped_out_count'],
                'billing_generated' => $billingResults['success'],
                'billing_templates_processed' => $billingResults['templates_processed'] ?? 0,
                'billing_tagihan_created' => $billingResults['total_created'] ?? 0,
                'executed_by' => Auth::check() ? Auth::id() : null,
                'executed_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Tahun akademik berhasil dinaikkan secara otomatis' . 
                           ($billingResults['success'] ? ' dan tagihan semester otomatis telah dibuat' : ''),
                'data' => [
                    'previous_academic_year' => $previewData['current_academic_year'],
                    'new_academic_year' => [
                        'id' => $nextAcademicYear->id,
                        'name' => $nextAcademicYear->name,
                        'type' => $nextAcademicYear->type,
                        'period' => Carbon::parse($nextAcademicYear->start_date)->format('d M Y') . ' - ' . Carbon::parse($nextAcademicYear->ended_date)->format('d M Y')
                    ],
                    'advancement_results' => $advancementResults,
                    'billing_results' => $billingResults
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Academic Year Advancement Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'executed_by' => Auth::check() ? Auth::id() : null,
                'executed_at' => now()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menaikkan tahun akademik: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create the next academic year
     *
     * @param TahunAkademik $currentAcademicYear
     * @return TahunAkademik
     */
    private function createNextAcademicYear($currentAcademicYear)
    {
        $nextAcademicYearInfo = $this->generateNextAcademicYearInfo($currentAcademicYear);
        
        // Generate unique code
        do {
            $code = 'TAK-' . strtoupper(Str::random(6));
        } while (TahunAkademik::where('code', $code)->exists());

        return TahunAkademik::create([
            'name' => $nextAcademicYearInfo['name'],
            'type' => $nextAcademicYearInfo['type'],
            'code' => $code,
            'start_date' => $nextAcademicYearInfo['start_date'],
            'ended_date' => $nextAcademicYearInfo['ended_date'],
            'desc' => $nextAcademicYearInfo['description'],
            'status' => 'Tidak Aktif', // Will be activated after student advancement
            'created_by' => Auth::check() ? Auth::id() : null,
        ]);
    }

    /**
     * Advance all eligible students
     *
     * @return array
     */
    private function advanceStudents()
    {
        // Use bulk updates for better performance
        
        // 1. Advance students who won't drop out (semester <= 13, so next semester <= 14)
        $advancedCount = Mahasantri::where('type', 1)
            ->where('semester', '<=', 13)
            ->increment('semester');
        
        // 2. Handle dropout students (semester >= 14, so next semester > 14)
        $dropoutStudents = Mahasantri::where('type', 1)
            ->where('semester', '>=', 14)
            ->get(['id', 'name', 'numb_nim', 'semester']);
        
        $droppedOutCount = $dropoutStudents->count();
        
        if ($droppedOutCount > 0) {
            // Bulk update dropout students - set semester to 0 and change type to Non-Aktif
            Mahasantri::where('type', 1)
                ->where('semester', '>=', 14)
                ->update([
                    'type' => 2, // Non-Aktif (Drop Out)
                    'semester' => 0 // Set semester to 0 for dropped out students
                ]);
            
            // Log dropout students (limit logging for performance)
            if ($droppedOutCount <= 50) {
                foreach ($dropoutStudents as $student) {
                    Log::info('Student Dropped Out', [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'nim' => $student->numb_nim,
                        'final_semester' => $student->semester + 1,
                        'dropped_out_at' => now(),
                        'reason' => 'Exceeded maximum semester limit (14)'
                    ]);
                }
            } else {
                // For large numbers, just log summary
                Log::info('Bulk Student Dropout', [
                    'total_dropped_out' => $droppedOutCount,
                    'dropped_out_at' => now(),
                    'reason' => 'Exceeded maximum semester limit (14)'
                ]);
            }
        }

        return [
            'advanced_count' => $advancedCount,
            'dropped_out_count' => $droppedOutCount,
            'total_processed' => $advancedCount + $droppedOutCount
        ];
    }

    /**
     * Validate if advancement can be performed
     *
     * @return array
     */
    public function validateAdvancement()
    {
        $errors = [];
        
        // Check if there's an active academic year
        $currentAcademicYear = TahunAkademik::where('status', 'Aktif')->first();
        if (!$currentAcademicYear) {
            $errors[] = 'Tidak ada tahun akademik yang aktif saat ini.';
        }

        // Check if there are active students
        $activeStudentsCount = Mahasantri::where('type', 1)->count();
        if ($activeStudentsCount === 0) {
            $errors[] = 'Tidak ada mahasiswa aktif yang dapat dinaikkan semesternya.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $this->getAdvancementWarnings()
        ];
    }

    /**
     * Get warnings about the advancement
     *
     * @return array
     */
    private function getAdvancementWarnings()
    {
        $warnings = [];
        
        // Check for students who will be dropped out
        $droppingOutStudents = Mahasantri::where('type', 1)
            ->where('semester', '>=', 14)
            ->count();
            
        if ($droppingOutStudents > 0) {
            $warnings[] = "Ada {$droppingOutStudents} mahasiswa yang akan drop out (semester > 14) dan statusnya akan berubah menjadi Non-Aktif. Catatan: Status Alumni hanya diberikan melalui sistem kelulusan berdasarkan penyelesaian tugas akhir.";
        }

        return $warnings;
    }

    /**
     * Auto-generate billing for the new academic year
     *
     * @param int $tahunAkademikId
     * @return array
     */
    private function autoGenerateBilling($tahunAkademikId)
    {
        try {
            Log::info('Starting auto billing generation after academic year advancement', [
                'tahun_akademik_id' => $tahunAkademikId
            ]);

            $smartBillingService = new SmartBillingService();
            
            // Generate semester billing for all active templates with semester_start trigger
            $results = $smartBillingService->generateSemesterBilling($tahunAkademikId);

            if (empty($results)) {
                Log::info('No billing templates found for auto generation');
                return [
                    'success' => true,
                    'message' => 'Tidak ada template billing yang aktif untuk auto generation',
                    'templates_processed' => 0,
                    'total_created' => 0,
                    'results' => []
                ];
            }

            $totalCreated = 0;
            $templatesProcessed = count($results);
            
            foreach ($results as $result) {
                $totalCreated += $result['created_count'];
            }

            Log::info('Auto billing generation completed successfully', [
                'templates_processed' => $templatesProcessed,
                'total_tagihan_created' => $totalCreated,
                'results' => $results
            ]);

            return [
                'success' => true,
                'message' => "Berhasil generate {$totalCreated} tagihan dari {$templatesProcessed} template",
                'templates_processed' => $templatesProcessed,
                'total_created' => $totalCreated,
                'results' => $results
            ];

        } catch (\Exception $e) {
            Log::error('Auto billing generation failed after academic year advancement', [
                'tahun_akademik_id' => $tahunAkademikId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal generate billing otomatis: ' . $e->getMessage(),
                'templates_processed' => 0,
                'total_created' => 0,
                'results' => []
            ];
        }
    }
}
