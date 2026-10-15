<?php

namespace App\Services;

use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentVerification;
use App\Models\Keuangan\RiwayatPembayaran;
use App\Models\Keuangan\PaymentReceipt;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentVerificationService
{
    protected $notificationService;
    protected $receiptService;

    public function __construct(
        NotificationService $notificationService,
        ReceiptGenerationService $receiptService
    ) {
        $this->notificationService = $notificationService;
        $this->receiptService = $receiptService;
    }

    /**
     * Create manual verification request
     */
    public function createManualVerification($transactionId, $data, $userId = null)
    {
        try {
            DB::beginTransaction();

            $transaction = PaymentTransaction::findOrFail($transactionId);

            // Validate transaction can be verified (manual proof upload allows pending/paid)
            if (!in_array($transaction->status, [
                PaymentTransaction::STATUS_PENDING,
                PaymentTransaction::STATUS_PROCESSING,
                PaymentTransaction::STATUS_PAID
            ])) {
                throw new Exception('Transaction cannot be verified in current status');
            }

            // Check existing open verification so re-upload replaces previous proof (no duplicate queue rows)
            $existingVerification = PaymentVerification::where('payment_transaction_id', $transactionId)
                ->whereIn('status', [
                    PaymentVerification::STATUS_PENDING,
                    PaymentVerification::STATUS_NEED_REVIEW
                ])
                ->orderByDesc('id')
                ->first();

            $oldProofFile = $existingVerification->proof_file ?? null;

            // Handle file upload if exists
            $proofFile = $oldProofFile;
            if (isset($data['proof_file'])) {
                if (is_string($data['proof_file'])) {
                    $proofFile = $data['proof_file'];
                } else {
                    $proofFile = $this->handleProofFileUpload($data['proof_file'], $transaction);
                }

                // delete old proof file when replacing
                if ($oldProofFile && $oldProofFile !== $proofFile && Storage::disk('public')->exists($oldProofFile)) {
                    Storage::disk('public')->delete($oldProofFile);
                }
            }


            // Update existing verification if present, otherwise create new
            if ($existingVerification) {
                $existingVerification->update([
                    'verified_amount' => $data['amount'] ?? $transaction->total_amount,
                    'payment_date' => $data['payment_date'] ?? now(),
                    'sender_name' => $data['sender_name'] ?? null,
                    'sender_account' => $data['sender_account'] ?? null,
                    'bank_reference' => $data['bank_reference'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'proof_file' => $proofFile,
                ]);
                $verification = $existingVerification->fresh();
            } else {
                $verification = PaymentVerification::createManualVerification($transactionId, [
                    'amount' => $data['amount'] ?? $transaction->total_amount,
                    'payment_date' => $data['payment_date'] ?? now(),
                    'sender_name' => $data['sender_name'] ?? null,
                    'sender_account' => $data['sender_account'] ?? null,
                    'bank_reference' => $data['bank_reference'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'proof_file' => $proofFile,
                ]);
            }

            // Update transaction status to processing
            $transaction->update([
                'status' => PaymentTransaction::STATUS_PROCESSING,
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

            DB::commit();

            // Send notification to admin
            $this->notificationService->notifyAdminNewVerificationRequest($verification);

            return [
                'success' => true,
                'message' => 'Verification request created successfully',
                'verification' => $verification
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Manual Verification Creation Error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'data' => $data,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Approve verification
     */
    public function approveVerification($verificationId, $userId, $notes = null)
    {
        try {
            DB::beginTransaction();

            $verification = PaymentVerification::findOrFail($verificationId);

            if (!$verification->canBeApproved()) {
                throw new Exception('Verification cannot be approved in current status');
            }

            // Approve verification
            $verification->approve($userId, $notes);

            $transaction = $verification->paymentTransaction;

            // Create or update payment history
            $this->createPaymentHistory($transaction);

            // Generate receipt
            $receipt = $this->receiptService->generateReceipt($transaction);

            DB::commit();

            // Send notifications
            $this->notificationService->notifyPaymentVerified($transaction);

            return [
                'success' => true,
                'message' => 'Payment verified successfully',
                'verification' => $verification,
                'transaction' => $transaction->fresh(),
                'receipt' => $receipt
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Verification Approval Error: ' . $e->getMessage(), [
                'verification_id' => $verificationId,
                'user_id' => $userId,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Reject verification
     */
    public function rejectVerification($verificationId, $reason, $userId)
    {
        try {
            DB::beginTransaction();

            $verification = PaymentVerification::findOrFail($verificationId);

            if (!$verification->canBeRejected()) {
                throw new Exception('Verification cannot be rejected in current status');
            }

            // Reject verification
            $verification->reject($reason, $userId);

            DB::commit();

            // Send notification to student
            $this->notificationService->notifyPaymentRejected($verification->paymentTransaction, $reason);

            return [
                'success' => true,
                'message' => 'Verification rejected successfully',
                'verification' => $verification
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Verification Rejection Error: ' . $e->getMessage(), [
                'verification_id' => $verificationId,
                'reason' => $reason,
                'user_id' => $userId,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Mark verification for review
     */
    public function markForReview($verificationId, $notes = null)
    {
        try {
            $verification = PaymentVerification::findOrFail($verificationId);
            $verification->markForReview($notes);

            // Send notification to senior admin
            $this->notificationService->notifyVerificationNeedsReview($verification);

            return [
                'success' => true,
                'message' => 'Verification marked for review',
                'verification' => $verification
            ];

        } catch (Exception $e) {
            Log::error('Mark for Review Error: ' . $e->getMessage(), [
                'verification_id' => $verificationId,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Bulk approve verifications
     */
    public function bulkApprove($verificationIds, $userId, $notes = null)
    {
        $results = [
            'success' => [],
            'failed' => [],
            'total' => count($verificationIds)
        ];

        foreach ($verificationIds as $verificationId) {
            $result = $this->approveVerification($verificationId, $userId, $notes);
            
            if ($result['success']) {
                $results['success'][] = $verificationId;
            } else {
                $results['failed'][] = [
                    'id' => $verificationId,
                    'message' => $result['message']
                ];
            }
        }

        return $results;
    }

    /**
     * Auto verify payment from bank statement
     */
    public function autoVerifyFromBankStatement($bankStatementData)
    {
        try {
            $verifiedCount = 0;
            $failedCount = 0;

            foreach ($bankStatementData as $statement) {
                // Try to match with pending transactions
                $transaction = $this->matchTransactionFromBankStatement($statement);
                
                if ($transaction) {
                    // Create automatic verification
                    $verification = PaymentVerification::create([
                        'verification_code' => PaymentVerification::generateVerificationCode(),
                        'payment_transaction_id' => $transaction->id,
                        'verification_type' => PaymentVerification::TYPE_BANK_STATEMENT,
                        'status' => PaymentVerification::STATUS_APPROVED,
                        'verified_amount' => $statement['amount'],
                        'payment_date' => $statement['date'],
                        'sender_name' => $statement['sender_name'],
                        'sender_account' => $statement['sender_account'],
                        'bank_reference' => $statement['reference'],
                        'verification_data' => $statement,
                        'verified_at' => now(),
                        'auto_verified_at' => now(),
                    ]);

                    // Update transaction
                    $transaction->update([
                        'status' => PaymentTransaction::STATUS_VERIFIED,
                        'paid_at' => $statement['date'],
                        'verified_at' => now(),
                    ]);

                    // Create payment history
                    $this->createPaymentHistory($transaction);

                    $verifiedCount++;
                } else {
                    $failedCount++;
                }
            }

            return [
                'success' => true,
                'verified_count' => $verifiedCount,
                'failed_count' => $failedCount,
                'message' => "Auto verification completed. {$verifiedCount} verified, {$failedCount} failed."
            ];

        } catch (Exception $e) {
            Log::error('Auto Verification Error: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStats($dateFrom = null, $dateTo = null)
    {
        $query = PaymentVerification::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->where('status', PaymentVerification::STATUS_PENDING)->count(),
            'approved' => $query->where('status', PaymentVerification::STATUS_APPROVED)->count(),
            'rejected' => $query->where('status', PaymentVerification::STATUS_REJECTED)->count(),
            'need_review' => $query->where('status', PaymentVerification::STATUS_NEED_REVIEW)->count(),
            'automatic' => $query->where('verification_type', PaymentVerification::TYPE_AUTOMATIC)->count(),
            'manual' => $query->where('verification_type', PaymentVerification::TYPE_MANUAL)->count(),
        ];
    }

    /**
     * Handle proof file upload
     */
    private function handleProofFileUpload($file, $transaction)
    {
        $fileName = 'payment_proof_' . $transaction->transaction_code . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment_proofs', $fileName, 'public');
        
        return $path;
    }

    /**
     * Create payment history record
     */
    private function createPaymentHistory($transaction)
    {
        // Check if history already exists
        $existingHistory = RiwayatPembayaran::where('payment_transaction_id', $transaction->id)->first();
        
        if (!$existingHistory) {
            RiwayatPembayaran::createFromTransaction($transaction);
        } else {
            // Update existing history
            $existingHistory->update([
                'status' => RiwayatPembayaran::STATUS_VERIFIED,
                'verified_at' => now(),
            ]);
        }
    }

    /**
     * Match transaction from bank statement
     */
    private function matchTransactionFromBankStatement($statement)
    {
        // Try to match by amount and date range
        $dateFrom = $statement['date']->subDays(1);
        $dateTo = $statement['date']->addDays(1);

        return PaymentTransaction::where('total_amount', $statement['amount'])
            ->where('status', PaymentTransaction::STATUS_PENDING)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->first();
    }
}
