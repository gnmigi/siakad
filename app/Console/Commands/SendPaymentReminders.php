<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentReminderService;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-reminders 
                            {--type=all : Type of reminders to send (all, before-due, grace-period, overdue)}
                            {--dry-run : Run without actually sending reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to students based on due dates and overdue status';

    protected $reminderService;

    /**
     * Create a new command instance.
     */
    public function __construct(PaymentReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info('Starting payment reminder process...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No reminders will actually be sent');
        }

        try {
            // Get statistics before processing
            $statsBefore = $this->reminderService->getReminderStatistics();
            $this->displayStatistics('Before Processing', $statsBefore);

            // Process reminders based on type
            switch ($type) {
                case 'before-due':
                    $this->info('Sending before due date reminders...');
                    if (!$dryRun) {
                        $this->reminderService->sendBeforeDueReminders();
                    }
                    break;

                case 'grace-period':
                    $this->info('Sending grace period reminders...');
                    if (!$dryRun) {
                        $this->reminderService->sendGracePeriodReminders();
                    }
                    break;

                case 'overdue':
                    $this->info('Sending overdue reminders...');
                    if (!$dryRun) {
                        $this->reminderService->sendOverdueReminders();
                    }
                    break;

                case 'all':
                default:
                    $this->info('Sending all types of reminders...');
                    if (!$dryRun) {
                        $this->reminderService->sendDailyReminders();
                    }
                    break;
            }

            // Get statistics after processing
            $statsAfter = $this->reminderService->getReminderStatistics();
            $this->displayStatistics('After Processing', $statsAfter);

            $this->info('Payment reminder process completed successfully!');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error in payment reminder process: ' . $e->getMessage());
            Log::error('Payment reminder command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Display reminder statistics
     */
    protected function displayStatistics(string $title, array $stats)
    {
        $this->info("\n=== {$title} ===");
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Pending Payments', $stats['total_pending']],
                ['On Time', $stats['on_time']],
                ['Grace Period', $stats['grace_period']],
                ['Overdue', $stats['overdue']],
                ['Academic Hold Applied', $stats['academic_hold']],
                ['Needs Reminder', $stats['needs_reminder']],
            ]
        );
    }
}
