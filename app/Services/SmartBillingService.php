<?php

namespace App\Services;

use App\Models\Keuangan\BillingTemplate;
use App\Models\Keuangan\TagihanKuliahGroup;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Akademik\TahunAkademik;
use App\Models\Mahasantri;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SmartBillingService
{
    /**
     * Generate semester billing for all active templates
     */
    public function generateSemesterBilling($tahunAkademikId, $semester = null)
    {
        try {
            Log::info('Starting smart semester billing generation', [
                'tahun_akademik_id' => $tahunAkademikId,
                'semester' => $semester
            ]);

            DB::beginTransaction();

            // Get all active semester templates
            $templates = BillingTemplate::active()
                ->where('type', BillingTemplate::TYPE_SEMESTER)
                ->where('auto_generate', true)
                ->get();

            $results = [];

            foreach ($templates as $template) {
                $result = $this->generateFromTemplate($template, $tahunAkademikId, [
                    'semester' => $semester
                ]);
                $results[] = $result;
            }

            DB::commit();

            Log::info('Smart semester billing generation completed', [
                'templates_processed' => count($templates),
                'results' => $results
            ]);

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in smart semester billing generation: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate tagihan from a specific template
     */
    public function generateFromTemplate(BillingTemplate $template, $tahunAkademikId, $additionalData = [])
    {
        try {
            Log::info("Generating tagihan from template: {$template->name}");

            // Get tahun akademik for dynamic naming
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);

            // Create TagihanKuliahGroup from template
            $group = $this->createGroupFromTemplate($template, $tahunAkademikId, $additionalData);

            // Get eligible students
            $students = $template->getEligibleStudents();

            $createdCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($students as $student) {
                try {
                    // Check if tagihan already exists for this student and template
                    $existingTagihan = TagihanKuliah::where('mahasiswa_id', $student->id)
                        ->where('group_id', $group->id)
                        ->first();

                    if ($existingTagihan) {
                        $skippedCount++;
                        continue;
                    }

                    // Calculate amount for this specific student
                    $amount = $template->calculateAmount($student, $additionalData);

                    // Calculate due date (can be NULL for no due date templates)
                    $dueDate = $template->calculateDueDate();
                    
                    // Use dynamic naming for individual tagihan description if enabled
                    if ($template->use_dynamic_naming) {
                        // Generate dynamic name with student's semester
                        $tagihanDescription = $template->generateDynamicName($tahunAkademik, $student->semester);
                    } else {
                        // Fallback to template description or name if dynamic naming is disabled
                        $tagihanDescription = $template->description ?: $template->name;
                    }

                    // Create individual tagihan
                    $tagihan = TagihanKuliah::create([
                        'code' => $this->generateTagihanCode($student),
                        'taka_id' => $tahunAkademikId,
                        'mahasiswa_id' => $student->id,
                        'group_id' => $group->id,
                        'amount' => $amount,
                        'due_date' => $dueDate,
                        'grace_period_days' => $template->grace_period_days,
                        'status' => TagihanKuliah::PAYMENT_PENDING,
                        'overdue_status' => $dueDate ? TagihanKuliah::STATUS_ON_TIME : null,
                        'payment_priority' => TagihanKuliah::PRIORITY_NORMAL,
                        'desc' => $tagihanDescription,
                        'created_by' => auth()->id(),
                    ]);

                    $createdCount++;

                } catch (\Exception $e) {
                    $errors[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Error creating tagihan for student {$student->id}: " . $e->getMessage());
                }
            }

            // Update group status and amount if tagihan were created
            if ($createdCount > 0) {
                // Recalculate amounts properly using the new methods
                $group->recalculateAmounts();
                $group->update([
                    'status' => 'Published',
                    'updated_by' => auth()->id()
                ]);
            }

            $result = [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'group_id' => $group->id,
                'group_code' => $group->code,
                'eligible_students' => $students->count(),
                'created_count' => $createdCount,
                'skipped_count' => $skippedCount,
                'errors_count' => count($errors),
                'errors' => $errors
            ];

            Log::info("Template generation completed", $result);

            return $result;

        } catch (\Exception $e) {
            Log::error("Error generating from template {$template->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create TagihanKuliahGroup from template
     */
    protected function createGroupFromTemplate(BillingTemplate $template, $tahunAkademikId, $additionalData = [])
    {
        // Get tahun akademik for dynamic naming
        $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        
        // Use dynamic naming if enabled, otherwise use traditional suffix
        if ($template->use_dynamic_naming && $tahunAkademik) {
            $groupName = $template->generateDynamicName($tahunAkademik);
        } else {
            $suffix = isset($additionalData['semester']) ? 
                " - Semester {$additionalData['semester']}" : 
                " - " . now()->format('Y-m-d H:i:s');
            $groupName = $template->name . $suffix;
        }

        // Generate unique slug
        $baseSlug = Str::slug($groupName);
        $slug = $baseSlug;
        $counter = 1;
        
        // Check if slug exists and make it unique
        while (TagihanKuliahGroup::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return TagihanKuliahGroup::create([
            'name' => $groupName,
            'code' => 'TKG-' . strtoupper(Str::random(8)),
            'slug' => $slug,
            'template_id' => $template->id,
            'taka_id' => $tahunAkademikId,
            'amount' => 0, // Will be calculated per student
            'amount_per_student' => 0, // Will be calculated from template
            'total_amount' => 0, // Will be calculated after creating tagihan
            'expected_student_count' => 0, // Will be set after counting eligible students
            'amount_type' => 'per_student', // Templates always use per-student calculation
            'due_date' => $template->calculateDueDate(),
            'desc' => $template->description,
            'prodi_id' => null, // Template handles multiple programs
            'kelas_id' => null, // Template handles multiple classes
            'semester' => $additionalData['semester'] ?? null,
            'status' => 'Draft',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Generate unique tagihan code
     */
    protected function generateTagihanCode($student)
    {
        return 'TGH-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
    }

    /**
     * Generate billing for specific template and criteria
     */
    public function generateSpecificBilling($templateId, $criteria = [])
    {
        try {
            $template = BillingTemplate::findOrFail($templateId);

            if ($template->status !== BillingTemplate::STATUS_ACTIVE) {
                throw new \Exception('Template must be active to generate billing');
            }

            // Get tahun akademik
            $tahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
            if (!$tahunAkademik) {
                throw new \Exception('No active academic year found');
            }

            return $this->generateFromTemplate($template, $tahunAkademik->id, $criteria);

        } catch (\Exception $e) {
            Log::error("Error in specific billing generation: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get billing generation statistics
     */
    public function getBillingStatistics($templateId = null)
    {
        $query = TagihanKuliahGroup::query();

        if ($templateId) {
            $query->where('template_id', $templateId);
        }

        $groups = $query->with(['tagihanKuliahs'])->get();

        $stats = [
            'total_groups' => $groups->count(),
            'published_groups' => $groups->where('status', 'Published')->count(),
            'draft_groups' => $groups->where('status', 'Draft')->count(),
            'total_tagihan' => $groups->sum(function ($group) {
                return $group->tagihanKuliahs->count();
            }),
            'total_amount' => $groups->sum(function ($group) {
                return $group->tagihanKuliahs->sum('amount');
            }),
            'pending_amount' => $groups->sum(function ($group) {
                return $group->tagihanKuliahs->where('status', TagihanKuliah::PAYMENT_PENDING)->sum('amount');
            }),
            'paid_amount' => $groups->sum(function ($group) {
                return $group->tagihanKuliahs->where('status', TagihanKuliah::PAYMENT_SUKSES)->sum('amount');
            }),
        ];

        return $stats;
    }

    /**
     * Preview billing generation without actually creating records
     */
    public function previewBillingGeneration($templateId, $criteria = [])
    {
        try {
            $template = BillingTemplate::findOrFail($templateId);

            // Get eligible students
            $students = $template->getEligibleStudents();

            $preview = [
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'type' => $template->type,
                    'status' => $template->status
                ],
                'eligible_students_count' => $students->count(),
                'estimated_due_date' => $template->calculateDueDate(),
                'grace_period_days' => $template->grace_period_days,
                'sample_calculations' => []
            ];

            // Show sample calculations for first 5 students
            foreach ($students->take(5) as $student) {
                $amount = $template->calculateAmount($student, $criteria);
                $preview['sample_calculations'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_nim' => $student->nim,
                    'semester' => $student->semester,
                    'program' => $student->prodi->name ?? 'N/A',
                    'calculated_amount' => $amount
                ];
            }

            $preview['total_estimated_amount'] = $students->sum(function ($student) use ($template, $criteria) {
                return $template->calculateAmount($student, $criteria);
            });

            return $preview;

        } catch (\Exception $e) {
            Log::error("Error in billing preview: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Schedule automatic billing generation
     */
    public function scheduleAutomaticGeneration()
    {
        try {
            // Get templates that should auto-generate
            $templates = BillingTemplate::active()
                ->where('auto_generate', true)
                ->where('generation_trigger', BillingTemplate::TRIGGER_SCHEDULED)
                ->get();

            $results = [];

            foreach ($templates as $template) {
                // Check if it's time to generate based on schedule
                if ($this->shouldGenerateNow($template)) {
                    $tahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
                    if ($tahunAkademik) {
                        $result = $this->generateFromTemplate($template, $tahunAkademik->id);
                        $results[] = $result;
                    }
                }
            }

            return $results;

        } catch (\Exception $e) {
            Log::error("Error in scheduled billing generation: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if template should generate now based on schedule
     */
    protected function shouldGenerateNow(BillingTemplate $template)
    {
        // Simple implementation - you can enhance this with cron expression parsing
        $schedule = $template->generation_schedule;
        
        if (!$schedule) {
            return false;
        }

        // For now, just check if it's the first day of the month
        if ($schedule === 'monthly' && now()->day === 1) {
            return true;
        }

        // Add more schedule logic as needed
        return false;
    }

    /**
     * Bulk update template settings
     */
    public function bulkUpdateTemplates($templateIds, $updates)
    {
        try {
            DB::beginTransaction();

            $updated = BillingTemplate::whereIn('id', $templateIds)
                ->update(array_merge($updates, [
                    'updated_by' => auth()->id(),
                    'updated_at' => now()
                ]));

            DB::commit();

            Log::info("Bulk updated {$updated} templates", [
                'template_ids' => $templateIds,
                'updates' => $updates
            ]);

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in bulk template update: " . $e->getMessage());
            throw $e;
        }
    }
}
