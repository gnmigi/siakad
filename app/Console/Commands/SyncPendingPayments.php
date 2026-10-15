<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keuangan\PaymentTransaction;
use App\Services\PaymentGatewayService;
use Carbon\Carbon;

class SyncPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:sync-pending 
                            {--limit=10 : Maximum number of transactions to process}
                            {--hours=24 : Check transactions from last N hours}
                            {--dry-run : Show what would be synced without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Sync pending payment transactions with Midtrans status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $hours = $this->option('hours');
        $dryRun = $this->option('dry-run');
        
        $this->info("🔄 Syncing Pending Payments");
        $this->info("Limit: {$limit} transactions");
        $this->info("Time range: Last {$hours} hours");
        $this->info("Mode: " . ($dryRun ? 'DRY RUN (no changes)' : 'LIVE SYNC'));
        $this->line('');
        
        // Find pending transactions from last N hours
        $transactions = PaymentTransaction::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        if ($transactions->isEmpty()) {
            $this->info("✅ No pending transactions found in the last {$hours} hours");
            return 0;
        }
        
        $this->info("Found {$transactions->count()} pending transactions to check");
        $this->line('');
        
        $gatewayService = app(PaymentGatewayService::class);
        $syncedCount = 0;
        $errorCount = 0;
        
        foreach ($transactions as $transaction) {
            $this->info("Checking: {$transaction->transaction_code}");
            
            try {
                // Check status in Midtrans
                $statusResult = $gatewayService->checkPaymentStatus($transaction->transaction_code);
                
                if (!$statusResult['success']) {
                    $this->warn("  ❌ Failed to get Midtrans status: {$statusResult['message']}");
                    $errorCount++;
                    continue;
                }
                
                $midtransData = $statusResult['data'];
                $midtransStatus = $midtransData['transaction_status'];
                
                $this->line("  Database: {$transaction->status} | Midtrans: {$midtransStatus}");
                
                // Check if sync is needed
                if (in_array($midtransStatus, ['settlement', 'capture'])) {
                    $this->info("  🔄 Sync needed: Midtrans shows '{$midtransStatus}'");
                    
                    if (!$dryRun) {
                        // Perform sync via webhook simulation
                        $webhookData = array_merge($midtransData, [
                            'signature_key' => 'auto_sync_' . time(),
                            'auto_sync' => true,
                            'sync_reason' => 'Automatic sync for settled transaction'
                        ]);
                        
                        $webhookResult = $gatewayService->handleWebhook($webhookData);
                        
                        if ($webhookResult['success']) {
                            $this->info("  ✅ Synced successfully");
                            $syncedCount++;
                        } else {
                            $this->error("  ❌ Sync failed: {$webhookResult['message']}");
                            $errorCount++;
                        }
                    } else {
                        $this->comment("  📝 Would sync (dry run mode)");
                        $syncedCount++;
                    }
                } else {
                    $this->comment("  ⏳ No sync needed: Status is '{$midtransStatus}'");
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                $errorCount++;
            }
            
            $this->line('');
        }
        
        // Summary
        $this->line('===========================================');
        $this->info("📊 SYNC SUMMARY:");
        $this->info("Transactions checked: {$transactions->count()}");
        $this->info("Successfully synced: {$syncedCount}");
        $this->info("Errors: {$errorCount}");
        
        if ($dryRun) {
            $this->comment("Note: This was a dry run. No actual changes were made.");
            $this->comment("Run without --dry-run to perform actual sync.");
        }
        
        return 0;
    }
}
