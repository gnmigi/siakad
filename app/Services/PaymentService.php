<?php

namespace App\Services;

use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Mahasantri;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gatewayService;
    protected $verificationService;
    protected $receiptService;
    protected $notificationService;

    public function __construct(
        PaymentGatewayService $gatewayService,
        PaymentVerificationService $verificationService,
        ReceiptGenerationService $receiptService,
        NotificationService $notificationService
    ) {
        $this->gatewayService = $gatewayService;
        $this->verificationService = $verificationService;
        $this->receiptService = $receiptService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create payment transaction
     */
    public function createPayment($tagihanId, $mahasiswaId, $paymentMethodId, $bankAccountId = null)
    {
        try {
            DB::beginTransaction();

            // Validate inputs
            $tagihan = TagihanKuliah::findOrFail($tagihanId);
            $mahasiswa = Mahasantri::findOrFail($mahasiswaId);
            $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);
            $bankAccount = $bankAccountId ? BankAccount::findOrFail($bankAccountId) : null;

            // Existing payments handling:
            // - For installment (tagihan status Cicilan), NEVER reuse paid/verified transactions
            // - Only allow reuse of in-progress transaction (pending/processing)
            // - paid/verified means previous installment cycle already closed
            $normalizedTagihanStatus = strtolower((string) ($tagihan->status ?? ''));
            $isInstallmentTagihan = $normalizedTagihanStatus === 'cicilan';

            $existingPayment = PaymentTransaction::where('tagihan_id', $tagihanId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereIn('status', ['pending', 'processing', 'paid', 'verified'])
                ->orderByDesc('id')
                ->first();

            if ($existingPayment) {
                Log::info('🔄 Existing payment found, evaluating reuse policy', [
                    'existing_payment_id' => $existingPayment->id,
                    'existing_status' => $existingPayment->status,
                    'transaction_code' => $existingPayment->transaction_code,
                    'tagihan_status' => $tagihan->status,
                    'is_installment_tagihan' => $isInstallmentTagihan
                ]);

                // Pending too old => cancel and create new
                if (
                    $existingPayment->status === PaymentTransaction::STATUS_PENDING &&
                    $existingPayment->created_at &&
                    $existingPayment->created_at->diffInHours(now()) > 1
                ) {
                    Log::info('🔄 Cancelling expired pending payment', [
                        'old_payment_id' => $existingPayment->id,
                        'created_hours_ago' => $existingPayment->created_at->diffInHours(now())
                    ]);

                    $existingPayment->update([
                        'status' => PaymentTransaction::STATUS_CANCELLED,
                        'notes' => 'Auto-cancelled due to timeout - new payment created'
                    ]);
                } else {
                    $canReuse = in_array($existingPayment->status, [
                        PaymentTransaction::STATUS_PENDING,
                        PaymentTransaction::STATUS_PROCESSING
                    ]);

                    // For installment flow, block reuse of closed states explicitly
                    if ($isInstallmentTagihan && in_array($existingPayment->status, [
                        PaymentTransaction::STATUS_PAID,
                        PaymentTransaction::STATUS_VERIFIED
                    ])) {
                        $canReuse = false;
                    }

                    if ($canReuse) {
                        Log::info('✅ Returning existing in-progress payment', [
                            'payment_id' => $existingPayment->id,
                            'status' => $existingPayment->status
                        ]);

                        return [
                            'success' => true,
                            'message' => 'Using existing payment transaction',
                            'transaction' => $existingPayment,
                            'payment_info' => $this->getPaymentInfo($existingPayment)
                        ];
                    }

                    Log::info('🆕 Existing payment not reusable, creating new transaction', [
                        'existing_payment_id' => $existingPayment->id,
                        'existing_status' => $existingPayment->status,
                        'is_installment_tagihan' => $isInstallmentTagihan
                    ]);
                }
            }

            // Calculate amounts
            $amount = $tagihan->amount;
            $feeAmount = $paymentMethod->calculateFee($amount);
            $totalAmount = $amount + $feeAmount;

            // Create payment transaction
            $isManualBankTransfer = $paymentMethod->type === 'bank_transfer';
            $transaction = PaymentTransaction::create([
                'tagihan_id' => $tagihanId,
                'mahasiswa_id' => $mahasiswaId,
                'payment_method_id' => $paymentMethodId,
                'bank_account_id' => $bankAccountId,
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'total_amount' => $totalAmount,
                'due_date' => $tagihan->due_date,
                'expired_at' => now()->addHours(24), // 24 hours to complete payment
                'status' => PaymentTransaction::STATUS_PENDING,
                'is_manual' => $isManualBankTransfer ? 1 : 0,
            ]);

            // Process payment based on method type
            if ($paymentMethod->isDigital()) {
                // Process through payment gateway
                Log::info('🔄 Processing digital payment', [
                    'transaction_code' => $transaction->transaction_code,
                    'payment_method_type' => $paymentMethod->type,
                    'payment_method_name' => $paymentMethod->name
                ]);
                
                $gatewayResult = $this->gatewayService->createPayment($transaction, $paymentMethod);
                
                if (!$gatewayResult['success']) {
                    Log::error('❌ Payment gateway error', [
                        'transaction_code' => $transaction->transaction_code,
                        'error_message' => $gatewayResult['message'],
                        'payment_method_type' => $paymentMethod->type
                    ]);
                    throw new Exception('Payment gateway error: ' . $gatewayResult['message']);
                }
                
                Log::info('✅ Digital payment processed successfully', [
                    'transaction_code' => $transaction->transaction_code,
                    'gateway_response_keys' => array_keys($gatewayResult['data'] ?? [])
                ]);
            } else {
                // Manual payment (cash)
                Log::info('🔄 Processing manual payment', [
                    'transaction_code' => $transaction->transaction_code,
                    'payment_method_type' => $paymentMethod->type
                ]);
                
                $transaction->update([
                    'notes' => 'Manual payment - requires verification by finance officer'
                ]);
            }

            DB::commit();

            // Send notification
            $this->notificationService->notifyPaymentCreated($transaction);

            return [
                'success' => true,
                'message' => 'Payment created successfully',
                'transaction' => $transaction->fresh(),
                'payment_info' => $this->getPaymentInfo($transaction)
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Payment Creation Error: ' . $e->getMessage(), [
                'tagihan_id' => $tagihanId,
                'mahasiswa_id' => $mahasiswaId,
                'payment_method_id' => $paymentMethodId,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Process manual cash payment
     */
    public function processCashPayment($transactionId, $data, $userId)
    {
        try {
            DB::beginTransaction();

            $transaction = PaymentTransaction::findOrFail($transactionId);

            if (!$transaction->paymentMethod->isManual()) {
                throw new Exception('This is not a manual payment method');
            }

            // Update transaction as paid
            $transaction->update([
                'status' => PaymentTransaction::STATUS_PAID,
                'paid_amount' => $data['amount'] ?? $transaction->total_amount,
                'paid_at' => $data['paid_at'] ?? now(),
                'notes' => $data['notes'] ?? $transaction->notes,
                'updated_by' => $userId,
            ]);

            // Create manual verification
            $verificationResult = $this->verificationService->createManualVerification(
                $transactionId,
                $data,
                $userId
            );

            if (!$verificationResult['success']) {
                throw new Exception('Failed to create verification: ' . $verificationResult['message']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Cash payment processed successfully',
                'transaction' => $transaction->fresh(),
                'verification' => $verificationResult['verification']
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Cash Payment Processing Error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
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
     * Cancel payment transaction
     */
    public function cancelPayment($transactionId, $reason = null, $userId = null)
    {
        try {
            DB::beginTransaction();

            $transaction = PaymentTransaction::findOrFail($transactionId);

            if (!$transaction->canBeCancelled()) {
                throw new Exception('Payment cannot be cancelled in current status: ' . $transaction->status);
            }

            Log::info('🚫 Starting payment cancellation', [
                'transaction_id' => $transactionId,
                'transaction_code' => $transaction->transaction_code,
                'current_status' => $transaction->status,
                'payment_method' => $transaction->paymentMethod->name ?? 'Unknown',
                'gateway_transaction_id' => $transaction->gateway_transaction_id,
                'reason' => $reason
            ]);

            // Cancel in payment gateway if digital payment and has gateway transaction ID
            $midtransCancelSuccess = false;
            $midtransWarning = null;
            
            if ($transaction->paymentMethod && $transaction->paymentMethod->isDigital()) {
                // Try to cancel using gateway_transaction_id first, then fallback to transaction_code
                $gatewayTransactionId = $transaction->gateway_transaction_id ?? $transaction->transaction_code;
                
                if ($gatewayTransactionId) {
                    Log::info('🔄 Attempting to cancel payment in Midtrans', [
                        'gateway_transaction_id' => $gatewayTransactionId,
                        'transaction_code' => $transaction->transaction_code
                    ]);
                    
                    $cancelResult = $this->gatewayService->cancelPayment($gatewayTransactionId);
                    
                    if ($cancelResult['success']) {
                        Log::info('✅ Payment successfully cancelled in Midtrans', [
                            'gateway_transaction_id' => $gatewayTransactionId,
                            'midtrans_response' => $cancelResult['data'] ?? 'No data'
                        ]);
                        $midtransCancelSuccess = true;
                    } else {
                        Log::warning('⚠️ Failed to cancel payment in Midtrans, proceeding with local cancellation', [
                            'gateway_transaction_id' => $gatewayTransactionId,
                            'error' => $cancelResult['message'] ?? 'Unknown error'
                        ]);
                        
                        // Check if this is Virtual Account limitation
                        if (strpos($cancelResult['message'], 'bank_transfer') !== false || 
                            strpos($cancelResult['message'], 'va_number') !== false ||
                            $transaction->virtual_account) {
                            $midtransWarning = 'MIDTRANS_VA_LIMITATION';
                        }
                        
                        // Don't throw exception here - still proceed with local cancellation
                        // This prevents issues if Midtrans API is down or transaction doesn't exist there
                    }
                } else {
                    Log::info('ℹ️ No gateway transaction ID found, skipping Midtrans cancellation', [
                        'transaction_code' => $transaction->transaction_code
                    ]);
                }
            } else {
                Log::info('ℹ️ Non-digital payment method, skipping gateway cancellation', [
                    'payment_method_type' => $transaction->paymentMethod->type ?? 'Unknown'
                ]);
            }

            // Update transaction status locally
            $transaction->update([
                'status' => PaymentTransaction::STATUS_CANCELLED,
                'notes' => ($transaction->notes ? $transaction->notes . "\n" : '') . 
                          "Cancelled: " . ($reason ?: 'No reason provided') . 
                          " at " . now()->format('Y-m-d H:i:s'),
                'updated_by' => $userId,
            ]);

            Log::info('✅ Payment cancelled successfully', [
                'transaction_id' => $transactionId,
                'transaction_code' => $transaction->transaction_code,
                'final_status' => 'cancelled',
                'reason' => $reason
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Payment cancelled successfully',
                'transaction' => $transaction->fresh()
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Payment Cancellation Error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'reason' => $reason,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to cancel payment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment information for display
     */
    public function getPaymentInfo(PaymentTransaction $transaction)
    {
        $paymentMethod = $transaction->paymentMethod;
        $bankAccount = $transaction->bankAccount;

        $info = [
            'transaction_code' => $transaction->transaction_code,
            'invoice_number' => $transaction->invoice_number,
            'amount' => $transaction->amount,
            'fee_amount' => $transaction->fee_amount,
            'total_amount' => $transaction->total_amount,
            'due_date' => $transaction->due_date,
            'expired_at' => $transaction->expired_at,
            'status' => $transaction->status,
            'payment_method' => [
                'name' => $paymentMethod->name,
                'type' => $paymentMethod->type,
                'icon' => $paymentMethod->icon,
            ]
        ];

        // Add method-specific information
        switch ($paymentMethod->type) {
            case 'virtual_account':
                $info['virtual_account'] = $transaction->virtual_account;
                $info['bank_info'] = [
                    'name' => $bankAccount->bank_name ?? 'Bank',
                    'code' => $bankAccount->bank_code ?? '',
                ];
                break;

            case 'bank_transfer':
                $info['bank_info'] = [
                    'name' => $bankAccount->bank_name ?? 'Bank',
                    'account_number' => $bankAccount->account_number ?? '',
                    'account_name' => $bankAccount->account_name ?? '',
                ];
                break;

            case 'e_wallet':
                $info['payment_url'] = $transaction->gateway_response['actions'][0]['url'] ?? null;
                $info['qr_code'] = $transaction->gateway_response['actions'][1]['url'] ?? null;
                break;

            case 'cash':
                $info['instructions'] = 'Silakan datang ke bagian keuangan untuk melakukan pembayaran tunai';
                break;
        }

        return $info;
    }

    /**
     * Get payment methods available for student
     * Mahasiswa akan melihat banyak pilihan pembayaran menggunakan Midtrans Snap
     * Semua dana masuk ke 1 settlement account yang dikonfigurasi di Midtrans
     */
    public function getAvailablePaymentMethods($mahasiswaId = null)
    {
        // Get all active payment methods (akan menggunakan Midtrans Snap)
        $methods = PaymentMethod::active()->ordered()->get();
        
        // Jika tidak ada payment methods, buat default untuk Midtrans Snap
        if ($methods->isEmpty()) {
            $this->createMidtransSnapPaymentMethods();
            $methods = PaymentMethod::active()->ordered()->get();
        }
        
        // PENTING: Tetap return bank accounts untuk Transfer Manual dan Virtual Account
        // Meskipun Midtrans Snap handle semua bank, kita tetap perlu bank accounts untuk:
        // 1. Transfer Manual (bank_transfer) - mahasiswa pilih bank tujuan transfer
        // 2. Virtual Account (virtual_account) - mahasiswa pilih bank VA
        $bankAccounts = BankAccount::active()->get();
        
        // Jika tidak ada bank accounts, buat default
        if ($bankAccounts->isEmpty()) {
            $this->createDefaultBankAccounts();
            $bankAccounts = BankAccount::active()->get();
        }

        return [
            'payment_methods' => $methods,
            'bank_accounts' => $bankAccounts,
            'info' => [
                'total_payment_options' => 'Semua bank di Indonesia + E-wallet + Kartu Kredit',
                'midtrans_snap' => true,
                'available_banks' => 'BCA, Mandiri, BNI, BRI, CIMB, BSI, Muamalat, dan 50+ bank lainnya',
                'available_ewallet' => 'GoPay, OVO, DANA, ShopeePay, LinkAja, Jenius Pay',
                'available_cards' => 'Visa, MasterCard, JCB, Amex',
                'settlement_info' => 'Semua dana akan masuk ke 1 rekening settlement yang dikonfigurasi di Midtrans',
                'bank_accounts_count' => $bankAccounts->count(),
                'bank_accounts_purpose' => 'Untuk Transfer Manual dan Virtual Account'
            ]
        ];
    }

    /**
     * Create Midtrans Snap payment methods
     * Snap akan otomatis menyediakan SEMUA opsi pembayaran
     */
    private function createMidtransSnapPaymentMethods()
    {
        $snapMethods = [
            [
                'code' => 'MIDTRANS_SNAP',
                'name' => 'Pembayaran Online',
                'type' => 'midtrans_snap',
                'description' => 'Bayar dengan semua bank, e-wallet, dan kartu kredit melalui Midtrans Snap',
                'icon' => 'credit-card',
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 4000,
                'config' => [
                    'enabled_payments' => [
                        'bank_transfer',
                        'echannel', 
                        'permata_va',
                        'bca_va',
                        'bni_va',
                        'bri_va',
                        'other_va',
                        'gopay',
                        'shopeepay',
                        'dana',
                        'ovo',
                        'linkaja',
                        'jenius',
                        'credit_card',
                        'cimb_clicks',
                        'bca_klikbca',
                        'bca_klikpay',
                        'bri_epay',
                        'xl_tunai',
                        'indosat_dompetku',
                        'kioson',
                        'indomaret',
                        'alfamart'
                    ]
                ]
            ],
            [
                'code' => 'TRANSFER_MANUAL',
                'name' => 'Transfer Manual',
                'type' => 'bank_transfer',
                'provider' => 'manual',
                'description' => 'Transfer manual ke rekening bank - Upload bukti pembayaran',
                'icon' => 'university',
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
            ],
            [
                'code' => 'CASH',
                'name' => 'Bayar Tunai',
                'type' => 'cash',
                'description' => 'Pembayaran tunai di kampus',
                'icon' => 'money-bill',
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
            ]
        ];

        foreach ($snapMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code']],
                array_merge($method, ['created_by' => 1])
            );
        }

        Log::info('✅ Midtrans Snap payment methods created - Mahasiswa akan punya akses ke SEMUA bank di Indonesia');
    }

    /**
     * Create default bank accounts for manual transfers and virtual accounts
     */
    private function createDefaultBankAccounts()
    {
        $defaultBanks = [
            [
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_code' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'YAYASAN MAAL AMIN',
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'bank_code' => 'MANDIRI',
                'account_number' => '1234567891',
                'account_name' => 'YAYASAN MAAL AMIN',
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'bank_name' => 'Bank Negara Indonesia (BNI)',
                'bank_code' => 'BNI',
                'account_number' => '1234567892',
                'account_name' => 'YAYASAN MAAL AMIN',
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                'bank_code' => 'BRI',
                'account_number' => '1234567893',
                'account_name' => 'YAYASAN MAAL AMIN',
                'is_active' => true,
                'created_by' => 1
            ]
        ];

        foreach ($defaultBanks as $bank) {
            BankAccount::firstOrCreate(
                [
                    'bank_code' => $bank['bank_code'],
                    'account_number' => $bank['account_number']
                ],
                $bank
            );
        }

        Log::info('✅ Default bank accounts created for manual transfers and virtual accounts');
    }

    /**
     * Get payment history for student
     */
    public function getPaymentHistory($mahasiswaId, $limit = 10)
    {
        return PaymentTransaction::byMahasiswa($mahasiswaId)
            ->with(['tagihan', 'paymentMethod', 'bankAccount', 'receipts'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats($dateFrom = null, $dateTo = null)
    {
        $query = PaymentTransaction::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $totalAmount = $query->sum('total_amount');
        $totalFee = $query->sum('fee_amount');

        return [
            'total_transactions' => $query->count(),
            'total_amount' => $totalAmount,
            'total_fee' => $totalFee,
            'net_amount' => $totalAmount - $totalFee,
            'by_status' => [
                'pending' => $query->where('status', 'pending')->count(),
                'processing' => $query->where('status', 'processing')->count(),
                'paid' => $query->where('status', 'paid')->count(),
                'verified' => $query->where('status', 'verified')->count(),
                'failed' => $query->where('status', 'failed')->count(),
                'cancelled' => $query->where('status', 'cancelled')->count(),
            ],
            'by_method' => PaymentTransaction::join('payment_methods', 'payment_transactions.payment_method_id', '=', 'payment_methods.id')
                ->selectRaw('payment_methods.name, COUNT(*) as count, SUM(payment_transactions.total_amount) as total')
                ->groupBy('payment_methods.id', 'payment_methods.name')
                ->get()
        ];
    }

    /**
     * Process webhook from payment gateway
     */
    public function processWebhook($notification)
    {
        return $this->gatewayService->handleWebhook($notification);
    }

    /**
     * Check payment status from gateway
     */
    public function checkPaymentStatus($transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);
        
        if ($transaction->gateway_transaction_id) {
            return $this->gatewayService->checkPaymentStatus($transaction->gateway_transaction_id);
        }

        return [
            'success' => true,
            'data' => [
                'transaction_status' => $transaction->status,
                'local_only' => true
            ]
        ];
    }

    /**
     * Generate receipt for transaction
     */
    public function generateReceipt($transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);
        
        if (!$transaction->isVerified()) {
            throw new Exception('Receipt can only be generated for verified payments');
        }

        return $this->receiptService->generateReceipt($transaction);
    }

    /**
     * Send payment reminders
     */
    public function sendPaymentReminders($days = 3)
    {
        return $this->notificationService->sendBulkPaymentReminders($days);
    }
}
