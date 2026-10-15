<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmartBillingService;
use App\Models\Akademik\TahunAkademik;
use App\Models\Keuangan\BillingTemplate;
use Illuminate\Support\Facades\Log;

class GenerateSmartBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-smart 
                            {--template= : Specific template ID to generate}
                            {--semester= : Semester to generate for}
                            {--tahun-akademik= : Academic year ID}
                            {--preview : Preview generation without creating records}
                            {--scheduled : Run scheduled automatic generation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate smart billing based on templates';

    protected $smartBillingService;

    /**
     * Create a new command instance.
     */
    public function __construct(SmartBillingService $smartBillingService)
    {
        parent::__construct();
        $this->smartBillingService = $smartBillingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Smart Billing Generation...');

        try {
            if ($this->option('scheduled')) {
                return $this->handleScheduledGeneration();
            }

            if ($this->option('preview')) {
                return $this->handlePreview();
            }

            if ($this->option('template')) {
                return $this->handleSpecificTemplate();
            }

            return $this->handleSemesterGeneration();

        } catch (\Exception $e) {
            $this->error('Error in smart billing generation: ' . $e->getMessage());
            Log::error('Smart billing command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Handle scheduled automatic generation
     */
    protected function handleScheduledGeneration()
    {
        $this->info('Running scheduled automatic generation...');

        $results = $this->smartBillingService->scheduleAutomaticGeneration();

        if (empty($results)) {
            $this->info('No templates scheduled for generation at this time.');
            return Command::SUCCESS;
        }

        $this->displayResults($results);
        $this->info('Scheduled generation completed successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Handle preview mode
     */
    protected function handlePreview()
    {
        $templateId = $this->option('template');
        
        if (!$templateId) {
            $templateId = $this->askForTemplate();
        }

        $semester = $this->option('semester');
        $criteria = $semester ? ['semester' => $semester] : [];

        $this->info("Previewing billing generation for template ID: {$templateId}");

        $preview = $this->smartBillingService->previewBillingGeneration($templateId, $criteria);

        $this->displayPreview($preview);

        return Command::SUCCESS;
    }

    /**
     * Handle specific template generation
     */
    protected function handleSpecificTemplate()
    {
        $templateId = $this->option('template');
        $semester = $this->option('semester');
        
        $criteria = [];
        if ($semester) {
            $criteria['semester'] = $semester;
        }

        $this->info("Generating billing for template ID: {$templateId}");

        $result = $this->smartBillingService->generateSpecificBilling($templateId, $criteria);

        $this->displayResults([$result]);
        $this->info('Specific template generation completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Handle semester generation
     */
    protected function handleSemesterGeneration()
    {
        $tahunAkademikId = $this->option('tahun-akademik');
        $semester = $this->option('semester');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
            if (!$tahunAkademik) {
                $this->error('No active academic year found. Please specify --tahun-akademik option.');
                return Command::FAILURE;
            }
            $tahunAkademikId = $tahunAkademik->id;
            $this->info("Using active academic year: {$tahunAkademik->tahun}");
        }

        $this->info("Generating semester billing for academic year ID: {$tahunAkademikId}");
        if ($semester) {
            $this->info("Semester: {$semester}");
        }

        $results = $this->smartBillingService->generateSemesterBilling($tahunAkademikId, $semester);

        if (empty($results)) {
            $this->warn('No active templates found for automatic generation.');
            return Command::SUCCESS;
        }

        $this->displayResults($results);
        $this->info('Semester billing generation completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Ask user to select a template
     */
    protected function askForTemplate()
    {
        $templates = BillingTemplate::active()->get();

        if ($templates->isEmpty()) {
            $this->error('No active templates found.');
            return null;
        }

        $choices = [];
        foreach ($templates as $template) {
            $choices[$template->id] = "{$template->name} ({$template->type})";
        }

        return $this->choice('Select a template:', $choices);
    }

    /**
     * Display generation results
     */
    protected function displayResults($results)
    {
        $this->info("\n=== Generation Results ===");

        $totalCreated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($results as $result) {
            $this->info("\nTemplate: {$result['template_name']}");
            $this->info("Group Code: {$result['group_code']}");
            $this->info("Eligible Students: {$result['eligible_students']}");
            
            if ($result['created_count'] > 0) {
                $this->info("✓ Created: {$result['created_count']} tagihan");
            }
            
            if ($result['skipped_count'] > 0) {
                $this->warn("⚠ Skipped: {$result['skipped_count']} (already exists)");
            }
            
            if ($result['errors_count'] > 0) {
                $this->error("✗ Errors: {$result['errors_count']}");
                
                // Show first few errors
                foreach (array_slice($result['errors'], 0, 3) as $error) {
                    $this->error("  - {$error['student_name']}: {$error['error']}");
                }
                
                if (count($result['errors']) > 3) {
                    $this->error("  ... and " . (count($result['errors']) - 3) . " more errors");
                }
            }

            $totalCreated += $result['created_count'];
            $totalSkipped += $result['skipped_count'];
            $totalErrors += $result['errors_count'];
        }

        $this->info("\n=== Summary ===");
        $this->info("Templates Processed: " . count($results));
        $this->info("Total Created: {$totalCreated}");
        $this->info("Total Skipped: {$totalSkipped}");
        $this->info("Total Errors: {$totalErrors}");
    }

    /**
     * Display preview information
     */
    protected function displayPreview($preview)
    {
        $this->info("\n=== Billing Preview ===");
        $this->info("Template: {$preview['template']['name']} ({$preview['template']['type']})");
        $this->info("Status: {$preview['template']['status']}");
        $this->info("Eligible Students: {$preview['eligible_students_count']}");
        $this->info("Estimated Due Date: {$preview['estimated_due_date']}");
        $this->info("Grace Period: {$preview['grace_period_days']} days");
        $this->info("Total Estimated Amount: Rp " . number_format($preview['total_estimated_amount'], 0, ',', '.'));

        if (!empty($preview['sample_calculations'])) {
            $this->info("\n=== Sample Calculations ===");
            
            $this->table(
                ['Student', 'NIM', 'Semester', 'Program', 'Amount'],
                collect($preview['sample_calculations'])->map(function ($calc) {
                    return [
                        $calc['student_name'],
                        $calc['student_nim'],
                        $calc['semester'],
                        $calc['program'],
                        'Rp ' . number_format($calc['calculated_amount'], 0, ',', '.')
                    ];
                })->toArray()
            );
        }

        if ($this->confirm('Do you want to proceed with actual generation?')) {
            $templateId = $preview['template']['id'];
            $result = $this->smartBillingService->generateSpecificBilling($templateId);
            $this->displayResults([$result]);
        }
    }
}
