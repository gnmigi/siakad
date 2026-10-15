<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync pending payments every 2 minutes (fast response)
        $schedule->command('payments:sync-pending --limit=10 --hours=2')
                 ->everyTwoMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/payment-sync.log'));
        
        // Sync older pending payments every 30 minutes
        $schedule->command('payments:sync-pending --limit=30 --hours=24')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/payment-sync-extended.log'));
        
        // Daily cleanup - sync very old pending payments
        $schedule->command('payments:sync-pending --limit=50 --hours=168')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/payment-sync-daily.log'));

        // Auto-terminal check - run daily at 3 AM (after payment sync)
        $schedule->command('terminal:check-auto')
                 ->dailyAt('03:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/auto-terminal.log'));

        // Also run every 6 hours during business hours for more frequent checks
        $schedule->command('terminal:check-auto')
                 ->cron('0 9,15,21 * * *') // 9 AM, 3 PM, 9 PM
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/auto-terminal.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
