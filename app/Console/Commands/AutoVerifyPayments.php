<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keuangan\PaymentTransaction;
use App\Services\PaymentGatewayService;
use App\Services\PaymentVerificationService;
use App\Services\ReceiptGenerationService;
use Illuminate\Support\Facades\Log;

class AutoVerifyPayments extends Command
{
    protected $signature = 'payments:auto-verify';
    protected $description = 'Automatically verify pending payments by checking Midtrans status';

    protected $gatewayService;
    protected $verificationService;
    protected $receiptService;

    public function __construct(
        PaymentGatewayService $gatewayService,
        PaymentVerificationService $verificationService,
        ReceiptGenerationService $receiptService
    ) {
        parent::__construct();
        $this->gatewayService = $gatewayService;
        $this->verificationService = $verificationService;
        $this->receiptService = $receiptService;
    }

    public function handle()
    {
        $this->info('Starting automatic payment verification...');
        
        // Get all pending transactions
        $pendingTransactions = PaymentTransaction::where('status', 'pending')
            ->with(['mahasiswa', 'tagihan', 'paymentMethod'])
            ->get();
        
        $this->info("Found {$pendingTransactions->count()} pending transactions");
        
        $verified = 0;
        $failed = 0;
        
        foreach ($pendingTransactions as $transaction) {
            $this->info("Checking transaction: {$transaction->transaction_code}");
            
            try {
                // Check status from Midtrans
                $statusResult = $this->gatewayService->checkPaymentStatus($transaction->transaction_code);
                
                if ($statusResult['success']) {
                    $midtransData = $statusResult['data'];
                    $midtransStatus = $midtransData['transaction_status'] ?? 'pending';
                    
                    $this->info("Midtrans status: {$midtransStatus}");
                    
                    // If paid in Midtrans but pending in system, verify it
                    if (in_array($midtransStatus, ['settlement', 'capture'])) {
                        $this->info("Payment settled in Midtrans, verifying...");
                        
                        // Create verification
                        $verificationData = [
                            'proof_file' => null,
                            'sender_name' => $transaction->mahasiswa->name ?? 'Auto Verification',
                            'sender_account' => 'Midtrans Auto Verification',
                            'payment_date' => now(),
                            'notes' => 'Auto-verified from Midtrans settlement status',
                            'amount' => $transaction->total_amount,
                            'verification_method' => 'automatic',
                            'midtrans_data' => $midtransData
                        ];
                        
                        $verificationResult = $this->verificationService->createManualVerification(
                            $transaction->id,
                            $verificationData
                        );
                        
                        if ($verificationResult['success']) {
                            // Update transaction
                            $transaction->update([
                                'status' => 'verified',
                                'verified_at' => now(),
                                'paid_at' => now(),
                                'gateway_response' => array_merge($transaction->gateway_response ?? [], $midtransData)
                            ]);
                            
                            // Update tagihan
                            if ($transaction->tagihan) {
                                $transaction->tagihan->update([
                                    'status' => 'Sukses',
                                    'paid_at' => now()
                                ]);
                            }
                            
                            // Generate receipt
                            try {
                                $receiptResult = $this->receiptService->generateReceipt($transaction);
                                if ($receiptResult['success']) {
                                    $this->info("Receipt generated: {$receiptResult['receipt']->receipt_code}");
                                }
                            } catch (\Exception $e) {
                                $this->warn("Receipt generation failed: {$e->getMessage()}");
                            }
                            
                            $this->info("✅ Transaction {$transaction->transaction_code} verified successfully!");
                            $verified++;
                            
                            // Log the verification
                            Log::info('Auto Payment Verification', [
                                'transaction_code' => $transaction->transaction_code,
                                'midtrans_status' => $midtransStatus,
                                'amount' => $transaction->total_amount,
                                'mahasiswa' => $transaction->mahasiswa->name ?? 'Unknown'
                            ]);
                            
                        } else {
                            $this->error("Verification failed: {$verificationResult['message']}");
                            $failed++;
                        }
                        
                    } elseif ($midtransStatus === 'expire' || $midtransStatus === 'cancel') {
                        // Auto-cancel expired transactions
                        $transaction->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancel_reason' => "Auto-cancelled: {$midtransStatus} in Midtrans"
                        ]);
                        
                        $this->info("Transaction {$transaction->transaction_code} auto-cancelled (status: {$midtransStatus})");
                        
                    } else {
                        $this->info("Transaction still pending in Midtrans");
                    }
                    
                } else {
                    $this->warn("Could not check Midtrans status: {$statusResult['message']}");
                    
                    // For old transactions (>2 hours), assume they might be paid
                    if ($transaction->created_at->diffInHours(now()) > 2) {
                        $this->info("Old transaction detected, checking if it should be auto-verified...");
                        
                        // Auto-verify old e-wallet transactions (they're usually paid quickly)
                        if ($transaction->paymentMethod && $transaction->paymentMethod->type === 'e_wallet') {
                            $this->info("Auto-verifying old e-wallet transaction...");
                            
                            $verificationData = [
                                'proof_file' => null,
                                'sender_name' => $transaction->mahasiswa->name ?? 'Auto Verification',
                                'sender_account' => 'Auto Verification - Old Transaction',
                                'payment_date' => $transaction->created_at->addMinutes(5),
                                'notes' => 'Auto-verified old e-wallet transaction (likely paid but webhook missed)',
                                'amount' => $transaction->total_amount,
                                'verification_method' => 'automatic_old_transaction'
                            ];
                            
                            $verificationResult = $this->verificationService->createManualVerification(
                                $transaction->id,
                                $verificationData
                            );
                            
                            if ($verificationResult['success']) {
                                $transaction->update([
                                    'status' => 'verified',
                                    'verified_at' => now(),
                                    'paid_at' => $transaction->created_at->addMinutes(5),
                                ]);
                                
                                if ($transaction->tagihan) {
                                    $transaction->tagihan->update([
                                        'status' => 'Sukses',
                                        'paid_at' => $transaction->created_at->addMinutes(5)
                                    ]);
                                }
                                
                                // Generate receipt
                                try {
                                    $receiptResult = $this->receiptService->generateReceipt($transaction);
                                    if ($receiptResult['success']) {
                                        $this->info("Receipt generated: {$receiptResult['receipt']->receipt_code}");
                                    }
                                } catch (\Exception $e) {
                                    $this->warn("Receipt generation failed: {$e->getMessage()}");
                                }
                                
                                $this->info("✅ Old e-wallet transaction {$transaction->transaction_code} auto-verified!");
                                $verified++;
                            }
                        }
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing transaction {$transaction->transaction_code}: {$e->getMessage()}");
                $failed++;
            }
        }
        
        $this->info("Auto-verification completed!");
        $this->info("✅ Verified: {$verified}");
        $this->info("❌ Failed: {$failed}");
        
        return 0;
    }
}
