<?php

namespace App\Observers;

use App\Models\Keuangan\PaymentTransaction;
use App\Services\TerminalService;
use Illuminate\Support\Facades\Log;

class PaymentTransactionObserver
{
    protected $terminalService;

    public function __construct(TerminalService $terminalService)
    {
        $this->terminalService = $terminalService;
    }

    /**
     * Handle the PaymentTransaction "created" event.
     */
    public function created(PaymentTransaction $paymentTransaction): void
    {
        // Auto-check terminal status when new payment is created
        $this->checkTerminalStatus($paymentTransaction, 'created');
    }

    /**
     * Handle the PaymentTransaction "updated" event.
     */
    public function updated(PaymentTransaction $paymentTransaction): void
    {
        // Check if status changed to verified/paid
        if ($paymentTransaction->wasChanged('status')) {
            $oldStatus = $paymentTransaction->getOriginal('status');
            $newStatus = $paymentTransaction->status;
            
            // If payment status changed to verified/paid, check for auto-reactivation
            if (in_array($newStatus, ['verified', 'paid', 'success']) && 
                !in_array($oldStatus, ['verified', 'paid', 'success'])) {
                
                Log::info('Payment status changed to verified/paid, checking auto-reactivation', [
                    'payment_id' => $paymentTransaction->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'mahasiswa_id' => $paymentTransaction->mahasiswa_id
                ]);
                
                $this->checkTerminalStatus($paymentTransaction, 'payment_verified');
            }
        }
    }

    /**
     * Check terminal status for the mahasiswa related to this payment
     */
    protected function checkTerminalStatus(PaymentTransaction $paymentTransaction, string $trigger): void
    {
        try {
            // Get mahasiswa from payment transaction
            $mahasiswa = $paymentTransaction->mahasiswa;
            
            if (!$mahasiswa) {
                Log::warning('PaymentTransaction has no associated mahasiswa', [
                    'payment_id' => $paymentTransaction->id,
                    'mahasiswa_id' => $paymentTransaction->mahasiswa_id
                ]);
                return;
            }

            Log::info('Auto-checking terminal status after payment event', [
                'trigger' => $trigger,
                'payment_id' => $paymentTransaction->id,
                'mahasiswa_id' => $mahasiswa->id,
                'mahasiswa_name' => $mahasiswa->name,
                'payment_status' => $paymentTransaction->status,
                'payment_amount' => $paymentTransaction->amount
            ]);

            // Check if mahasiswa was terminal before
            $wasTerminal = $mahasiswa->is_terminated;
            
            // Run auto-terminal check (this will also check for auto-reactivation)
            $result = $this->terminalService->checkAutoTerminal($mahasiswa);
            
            // Reload mahasiswa to get updated status
            $mahasiswa->refresh();
            
            // Log the result
            if ($wasTerminal && !$mahasiswa->is_terminated) {
                Log::info('Mahasiswa auto-reactivated after payment', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'mahasiswa_name' => $mahasiswa->name,
                    'payment_id' => $paymentTransaction->id,
                    'trigger' => $trigger
                ]);
            } elseif (!$wasTerminal && $mahasiswa->is_terminated) {
                Log::info('Mahasiswa auto-terminated after payment check', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'mahasiswa_name' => $mahasiswa->name,
                    'payment_id' => $paymentTransaction->id,
                    'trigger' => $trigger,
                    'reason' => $mahasiswa->termination_reason
                ]);
            } else {
                Log::info('No terminal status change after payment check', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'mahasiswa_name' => $mahasiswa->name,
                    'payment_id' => $paymentTransaction->id,
                    'trigger' => $trigger,
                    'is_terminal' => $mahasiswa->is_terminated
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in PaymentTransactionObserver', [
                'payment_id' => $paymentTransaction->id,
                'mahasiswa_id' => $paymentTransaction->mahasiswa_id,
                'trigger' => $trigger,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
