<?php

namespace App\Services;

use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;
use App\Models\Keuangan\PaymentVerification;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentGatewayService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    protected $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction 
            ? config('midtrans.production_base_url', 'https://api.midtrans.com/v2')
            : config('midtrans.sandbox_base_url', 'https://api.sandbox.midtrans.com/v2');
            
        // Debug log untuk memastikan konfigurasi terbaca
        Log::info('🔧 PaymentGatewayService initialized', [
            'server_key_exists' => !empty($this->serverKey),
            'client_key_exists' => !empty($this->clientKey),
            'is_production' => $this->isProduction,
            'base_url' => $this->baseUrl
        ]);
    }

    /**
     * Create payment transaction via Midtrans
     */
    public function createPayment(PaymentTransaction $transaction)
    {
        try {
            $paymentMethod = $transaction->paymentMethod;
            
            Log::info('🚀 Creating Midtrans payment', [
                'transaction_code' => $transaction->transaction_code,
                'payment_method' => $paymentMethod->name,
                'payment_type' => $paymentMethod->type,
                'amount' => $transaction->total_amount
            ]);
            
            // PENTING: Untuk Midtrans Snap, gunakan Snap API
            if ($paymentMethod->type === 'midtrans_snap') {
                return $this->createSnapPayment($transaction);
            }
            
            // Untuk payment method lain, gunakan Charge API
            $paymentData = $this->preparePaymentData($transaction);
            $response = $this->sendToMidtrans('/charge', $paymentData);
            
            if ($response['status_code'] === '201' || $response['status_code'] === '200') {
                // Update transaction with gateway response
                $transaction->update([
                    'gateway_transaction_id' => $response['transaction_id'],
                    'gateway_reference' => $response['order_id'],
                    'gateway_response' => $response,
                    'status' => $this->mapMidtransStatus($response['transaction_status']),
                    'virtual_account' => $this->extractVirtualAccount($response),
                ]);

                Log::info('✅ Midtrans payment created successfully', [
                    'transaction_code' => $transaction->transaction_code,
                    'midtrans_status' => $response['transaction_status'],
                    'system_status' => $transaction->status
                ]);

                return [
                    'success' => true,
                    'data' => $response,
                    'transaction' => $transaction->fresh()
                ];
            }

            Log::warning('❌ Midtrans payment creation failed', [
                'transaction_code' => $transaction->transaction_code,
                'response' => $response
            ]);

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Payment creation failed',
                'data' => $response
            ];

        } catch (Exception $e) {
            Log::error('❌ Payment Gateway Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code ?? 'unknown',
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create Midtrans Snap payment
     */
    private function createSnapPayment(PaymentTransaction $transaction)
    {
        try {
            $snapData = $this->prepareSnapData($transaction);
            
            Log::info('🌟 Creating Midtrans Snap payment', [
                'transaction_code' => $transaction->transaction_code,
                'amount' => $transaction->total_amount,
                'snap_data' => $snapData
            ]);
            
            // Use Snap API endpoint
            $snapBaseUrl = $this->isProduction 
                ? 'https://app.midtrans.com/snap/v1'
                : 'https://app.sandbox.midtrans.com/snap/v1';
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
            ])->timeout(10)->post($snapBaseUrl . '/transactions', $snapData);
            
            if (!$response->successful()) {
                throw new Exception('Midtrans Snap API Error: ' . $response->body());
            }
            
            $snapResponse = $response->json();
            
            // Update transaction with Snap response
            $transaction->update([
                'gateway_transaction_id' => $snapResponse['token'],
                'gateway_reference' => $transaction->transaction_code,
                'gateway_response' => array_merge($snapResponse, [
                    'token' => $snapResponse['token'], // Ensure token is stored for frontend access
                    'redirect_url' => $snapResponse['redirect_url']
                ]),
                'status' => 'pending'
            ]);
            
            Log::info('✅ Midtrans Snap payment created successfully', [
                'transaction_code' => $transaction->transaction_code,
                'snap_token' => $snapResponse['token'],
                'redirect_url' => $snapResponse['redirect_url']
            ]);
            
            return [
                'success' => true,
                'data' => $snapResponse,
                'transaction' => $transaction->fresh()
            ];
            
        } catch (Exception $e) {
            Log::error('❌ Midtrans Snap Error: ' . $e->getMessage(), [
                'transaction_code' => $transaction->transaction_code,
                'error' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Midtrans Snap error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare Snap payment data
     */
    private function prepareSnapData(PaymentTransaction $transaction)
    {
        $mahasiswa = $transaction->mahasiswa;
        $tagihan = $transaction->tagihan;

        // Enhanced logging for debugging
        Log::info('🔍 Preparing Snap data for transaction', [
            'transaction_code' => $transaction->transaction_code,
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'original_email' => $mahasiswa->email,
            'original_phone' => $mahasiswa->phone
        ]);

        // Validate and fix email format with Midtrans-specific validation
        $originalEmail = $mahasiswa->email ?? 'student@example.com';
        $email = $originalEmail;
        
        // First check basic PHP validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If email is invalid, create a valid one
            $email = 'student' . $mahasiswa->id . '@example.com';
            Log::info('📧 Email fixed (invalid format)', [
                'original' => $originalEmail,
                'fixed' => $email,
                'mahasiswa_id' => $mahasiswa->id,
                'reason' => 'Failed PHP filter_var validation'
            ]);
        } else {
            // Additional Midtrans-specific validation
            $emailParts = explode('@', $email);
            if (count($emailParts) === 2) {
                $domain = $emailParts[1];
                $domainParts = explode('.', $domain);
                
                // Check if TLD (last part) is at least 2 characters
                if (count($domainParts) < 2 || strlen(end($domainParts)) < 2) {
                    $email = 'student' . $mahasiswa->id . '@example.com';
                    Log::info('📧 Email fixed (Midtrans incompatible)', [
                        'original' => $originalEmail,
                        'fixed' => $email,
                        'mahasiswa_id' => $mahasiswa->id,
                        'reason' => 'TLD too short for Midtrans (minimum 2 characters required)',
                        'original_domain' => $domain,
                        'original_tld' => end($domainParts)
                    ]);
                } else {
                    Log::info('📧 Email is valid for Midtrans', [
                        'email' => $email,
                        'mahasiswa_id' => $mahasiswa->id,
                        'domain' => $domain,
                        'tld' => end($domainParts)
                    ]);
                }
            } else {
                // Fallback if email structure is unexpected
                $email = 'student' . $mahasiswa->id . '@example.com';
                Log::info('📧 Email fixed (unexpected structure)', [
                    'original' => $originalEmail,
                    'fixed' => $email,
                    'mahasiswa_id' => $mahasiswa->id,
                    'reason' => 'Unexpected email structure'
                ]);
            }
        }

        // Validate and fix phone format
        $originalPhone = $mahasiswa->phone ?? '08123456789';
        $phone = preg_replace('/[^0-9+]/', '', $originalPhone);
        
        if (empty($phone) || strlen($phone) < 10) {
            $phone = '08123456789';
            Log::info('📱 Phone fixed', [
                'original' => $originalPhone,
                'fixed' => $phone,
                'mahasiswa_id' => $mahasiswa->id
            ]);
        } else {
            Log::info('📱 Phone is valid', [
                'phone' => $phone,
                'mahasiswa_id' => $mahasiswa->id
            ]);
        }

        // Validate and fix name
        $originalName = $mahasiswa->name ?? 'Student';
        $name = $originalName;
        
        if (empty($name) || strlen($name) < 2) {
            $name = 'Student ' . $mahasiswa->id;
            Log::info('👤 Name fixed', [
                'original' => $originalName,
                'fixed' => $name,
                'mahasiswa_id' => $mahasiswa->id
            ]);
        } else {
            Log::info('👤 Name is valid', [
                'name' => $name,
                'mahasiswa_id' => $mahasiswa->id
            ]);
        }

        // Get institution branding
        $webs = \App\Models\Pengaturan\WebSetting::first();
        $institutionName = $webs->school_name ?? config('app.name', 'SIAKAD');
        $institutionLogo = asset('logo.png'); // Adjust path as needed

        // Prepare item details with proper validation
        $itemDetails = [];
        
        // Main payment item
        $itemName = $tagihan->desc ?? 'Pembayaran Akademik';
        // Midtrans has a 50 character limit for item names
        if (strlen($itemName) > 50) {
            $itemName = substr($itemName, 0, 47) . '...';
        }
        
        $itemDetails[] = [
            'id' => $tagihan->code ?? 'TAGIHAN-' . $transaction->id,
            'price' => (int) $transaction->amount,
            'quantity' => 1,
            'name' => $itemName
        ];
        
        // Add fee if exists
        if ($transaction->fee_amount > 0) {
            $itemDetails[] = [
                'id' => 'FEE',
                'price' => (int) $transaction->fee_amount,
                'quantity' => 1,
                'name' => 'Biaya Admin'
            ];
        }
        
        // Calculate total from items to ensure it matches gross_amount
        $calculatedTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $itemDetails));
        
        // Ensure gross_amount matches the sum of item_details
        $grossAmount = (int) $transaction->total_amount;
        if ($calculatedTotal !== $grossAmount) {
            // Adjust the main item price to match the total
            $itemDetails[0]['price'] = $grossAmount - ($transaction->fee_amount > 0 ? (int) $transaction->fee_amount : 0);
        }

        $snapData = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => $grossAmount
            ],
            'customer_details' => [
                'first_name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
            'item_details' => $itemDetails,
            'enabled_payments' => [
                // Virtual Account
                'bca_va', 'bni_va', 'bri_va', 'echannel', 'permata_va', 'other_va',
                // E-Wallet
                'gopay', 'shopeepay', 'dana', 'ovo', 'linkaja', 'jenius',
                // Credit Card
                'credit_card',
                // Bank Transfer
                'bca_klikbca', 'bca_klikpay', 'bri_epay', 'cimb_clicks',
                // Retail
                'indomaret', 'alfamart',
                // Others
                'akulaku'
            ],
            'callbacks' => [
                'finish' => route('mahasiswa.payment.success') . '?transaction_id=' . $transaction->id,
                'unfinish' => route('mahasiswa.payment.pending') . '?transaction_id=' . $transaction->id,
                'error' => route('mahasiswa.payment.failed') . '?transaction_id=' . $transaction->id
            ],
            // Custom branding and styling
            'custom_expiry' => [
                'order_time' => now()->toISOString(),
                'expiry_duration' => 60,
                'unit' => 'minute'
            ],
            'page_expiry' => [
                'duration' => 60,
                'unit' => 'minute'
            ],
            // Custom fields for branding
            'custom_field1' => $institutionName,
            'custom_field2' => 'Pembayaran Akademik Online',
            'custom_field3' => 'Sistem Informasi Akademik'
        ];

        // Log the complete Snap data for debugging
        Log::info('📦 Complete Snap data prepared', [
            'transaction_code' => $transaction->transaction_code,
            'snap_data' => $snapData,
            'calculated_total' => $calculatedTotal,
            'gross_amount' => $grossAmount
        ]);

        return $snapData;
    }

    /**
     * Handle webhook notification from Midtrans
     */
    public function handleWebhook($notification)
    {
        try {
            // Check if this is a simulator or auto-check webhook
            $isSimulator = isset($notification['signature_key']) && 
                          (strpos($notification['signature_key'], 'simulated') !== false ||
                           strpos($notification['signature_key'], 'auto_check') !== false ||
                           strpos($notification['signature_key'], 'status_check') !== false ||
                           strpos($notification['signature_key'], 'manual_sync') !== false ||
                           strpos($notification['signature_key'], 'instant_verification') !== false ||
                           strpos($notification['signature_key'], 'dev_simulation') !== false ||
                           strpos($notification['signature_key'], 'test_webhook') !== false ||
                           isset($notification['simulated']) ||
                           isset($notification['reason']));
            
            // Verify notification authenticity (skip for simulator/auto-check)
            if (!$isSimulator && !$this->verifyNotification($notification)) {
                Log::warning('Invalid webhook signature', $notification);
                return ['success' => false, 'message' => 'Invalid notification signature'];
            }

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            Log::info('🔄 Processing webhook', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'is_simulator' => $isSimulator,
                'fraud_status' => $fraudStatus
            ]);

            // Find transaction by gateway reference or transaction code
            $transaction = PaymentTransaction::where('gateway_reference', $orderId)
                ->orWhere('transaction_code', $orderId)
                ->with(['tagihan', 'paymentMethod'])
                ->first();
            
            if (!$transaction) {
                Log::warning('❌ Transaction not found for webhook', ['order_id' => $orderId]);
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            // Skip if already verified
            if ($transaction->status === 'verified') {
                Log::info('✅ Transaction already verified', ['order_id' => $orderId]);
                return [
                    'success' => true,
                    'message' => 'Transaction already verified',
                    'transaction' => $transaction
                ];
            }

            // Update transaction status based on Midtrans status
            $newStatus = $this->mapMidtransStatus($transactionStatus, $fraudStatus);
            
            // For ALL payment methods, if status is settlement/capture, immediately verify
            if (in_array($transactionStatus, ['settlement', 'capture']) || $isSimulator) {
                $newStatus = 'verified'; // Skip 'paid' status, go directly to verified
                
                Log::info('🚀 Auto-verifying payment', [
                    'order_id' => $orderId,
                    'payment_method' => $transaction->paymentMethod->name ?? 'Unknown',
                    'transaction_status' => $transactionStatus,
                    'is_simulator' => $isSimulator
                ]);
            }
            
            $transaction->update([
                'status' => $newStatus,
                'gateway_response' => array_merge($transaction->gateway_response ?? [], $notification),
                'paid_at' => in_array($newStatus, ['paid', 'verified']) ? now() : $transaction->paid_at,
                'verified_at' => $newStatus === 'verified' ? now() : $transaction->verified_at,
            ]);

            Log::info('📊 Transaction status updated', [
                'order_id' => $orderId,
                'old_status' => $transaction->getOriginal('status'),
                'new_status' => $newStatus
            ]);

            // Create automatic verification for successful payments
            if ($newStatus === 'verified') {
                // Create verification record
                $verification = PaymentVerification::createAutoVerification($transaction->id, array_merge($notification, [
                    'verified_by' => $isSimulator ? 'simulator_webhook' : 'midtrans_webhook',
                    'verification_method' => 'automatic',
                    'notes' => $isSimulator ? 'Auto-verified via simulator webhook' : 'Auto-verified via Midtrans webhook',
                    'verified_amount' => $transaction->total_amount,
                    'payment_date' => now()
                ]));
                
                Log::info('📝 Verification record created', [
                    'verification_id' => $verification->id,
                    'transaction_code' => $orderId
                ]);
                
                // Update tagihan status to paid
                if ($transaction->tagihan) {
                    $transaction->tagihan->update([
                        'status' => 'Sukses',
                        'paid_at' => now()
                    ]);
                    
                    Log::info('💰 Tagihan updated to Sukses', [
                        'tagihan_id' => $transaction->tagihan->id,
                        'tagihan_code' => $transaction->tagihan->code ?? 'N/A',
                        'transaction_code' => $orderId
                    ]);
                }
                
                // 🚀 AUTO-SYNC TO SALDO TABLE
                try {
                    $this->syncPaymentToSaldo($transaction, $isSimulator);
                } catch (Exception $e) {
                    Log::error('❌ Auto-sync to Saldo failed: ' . $e->getMessage(), [
                        'transaction_code' => $orderId,
                        'error' => $e->getTraceAsString()
                    ]);
                }
                
                // Generate receipt automatically
                try {
                    $receiptService = app(ReceiptGenerationService::class);
                    $receiptService->generateReceipt($transaction);
                    Log::info('🧾 Receipt generated automatically', ['transaction_code' => $orderId]);
                } catch (Exception $e) {
                    Log::warning('⚠️ Auto receipt generation failed: ' . $e->getMessage(), [
                        'transaction_code' => $orderId
                    ]);
                }
                
                Log::info('✅ Payment verification completed successfully', [
                    'transaction_code' => $orderId,
                    'final_status' => 'verified',
                    'payment_method' => $transaction->paymentMethod->name ?? 'Unknown'
                ]);
            }

            return [
                'success' => true,
                'message' => 'Webhook processed successfully',
                'transaction' => $transaction->fresh()
            ];

        } catch (Exception $e) {
            Log::error('❌ Webhook Processing Error: ' . $e->getMessage(), [
                'notification' => $notification,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status from Midtrans
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            Log::info('🔍 Checking payment status from Midtrans', [
                'transaction_id' => $transactionId
            ]);
            
            $response = $this->sendToMidtrans("/{$transactionId}/status", null, 'GET');
            
            // FIXED: Handle 404 responses properly
            if (isset($response['status_code']) && $response['status_code'] === '404') {
                Log::info('ℹ️ Transaction not found in Midtrans (404)', [
                    'transaction_id' => $transactionId,
                    'response' => $response
                ]);
                
                return [
                    'success' => true,
                    'data' => [
                        'transaction_status' => 'not_found',
                        'status_code' => '404',
                        'status_message' => 'Transaction not found in Midtrans'
                    ],
                    'message' => 'Transaction not found in Midtrans'
                ];
            }
            
            Log::info('✅ Midtrans status check successful', [
                'transaction_id' => $transactionId,
                'status' => $response['transaction_status'] ?? 'unknown',
                'status_code' => $response['status_code'] ?? 'unknown'
            ]);
            
            return [
                'success' => true,
                'data' => $response,
                'message' => 'Payment status retrieved successfully'
            ];

        } catch (Exception $e) {
            Log::error('❌ Midtrans status check error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'error_details' => $e->getTraceAsString()
            ]);
            
            // FIXED: Return proper structure even on error
            return [
                'success' => false,
                'data' => [
                    'transaction_status' => 'error',
                    'status_code' => '500',
                    'status_message' => $e->getMessage()
                ],
                'message' => 'Midtrans API Error: ' . $e->getMessage(),
                'error_code' => 'MIDTRANS_API_ERROR'
            ];
        }
    }

    /**
     * Cancel payment transaction
     */
    public function cancelPayment($transactionId)
    {
        try {
            $response = $this->sendToMidtrans("/{$transactionId}/cancel", [], 'POST');
            
            return [
                'success' => true,
                'data' => $response
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare payment data for Midtrans
     */
    private function preparePaymentData(PaymentTransaction $transaction)
    {
        $mahasiswa = $transaction->mahasiswa;
        $tagihan = $transaction->tagihan;
        $paymentMethod = $transaction->paymentMethod;

        $baseData = [
            'payment_type' => $this->getPaymentType($paymentMethod->type),
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => $transaction->total_amount
            ],
            'customer_details' => [
                'first_name' => $mahasiswa->name ?? 'Student',
                'email' => $mahasiswa->email ?? 'student@example.com',
                'phone' => $mahasiswa->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => $tagihan->code ?? 'TAGIHAN',
                    'price' => $transaction->amount,
                    'quantity' => 1,
                    'name' => $tagihan->name ?? 'Pembayaran Akademik'
                ]
            ]
        ];

        // Add fee if exists
        if ($transaction->fee_amount > 0) {
            $baseData['item_details'][] = [
                'id' => 'FEE',
                'price' => $transaction->fee_amount,
                'quantity' => 1,
                'name' => 'Biaya Admin'
            ];
        }

        // Add payment method specific data
        switch ($paymentMethod->type) {
            case 'virtual_account':
                $baseData['bank_transfer'] = [
                    'bank' => $this->getBankCode($transaction->bankAccount)
                ];
                break;
                
            case 'e_wallet':
                // Determine e-wallet type based on payment method name
                $walletType = strtolower($paymentMethod->name);
                
                if (strpos($walletType, 'gopay') !== false) {
                    $baseData['gopay'] = [
                        'enable_callback' => true,
                        'callback_url' => route('payment.callback')
                    ];
                } elseif (strpos($walletType, 'ovo') !== false) {
                    $baseData['payment_type'] = 'ovo';
                    $baseData['ovo'] = [
                        'enable_callback' => true,
                        'callback_url' => route('payment.callback')
                    ];
                } elseif (strpos($walletType, 'dana') !== false) {
                    $baseData['payment_type'] = 'dana';
                    $baseData['dana'] = [
                        'enable_callback' => true,
                        'callback_url' => route('payment.callback')
                    ];
                } elseif (strpos($walletType, 'shopeepay') !== false) {
                    $baseData['payment_type'] = 'shopeepay';
                    $baseData['shopeepay'] = [
                        'enable_callback' => true,
                        'callback_url' => route('payment.callback')
                    ];
                } else {
                    // Default to GoPay if not specified
                    $baseData['gopay'] = [
                        'enable_callback' => true,
                        'callback_url' => route('payment.callback')
                    ];
                }
                break;
                
            case 'credit_card':
                $baseData['credit_card'] = [
                    'secure' => true
                ];
                break;
        }

        return $baseData;
    }

    /**
     * Send request to Midtrans API
     */
    private function sendToMidtrans($endpoint, $data = null, $method = 'POST')
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
        ];

        // Set timeout to 5 seconds to avoid long delays
        $response = Http::withHeaders($headers)->timeout(5);

        if ($method === 'GET') {
            $response = $response->get($url);
        } else {
            $response = $response->post($url, $data);
        }

        if (!$response->successful()) {
            throw new Exception('Midtrans API Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Verify webhook notification
     */
    private function verifyNotification($notification)
    {
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        
        return $signatureKey === $notification['signature_key'];
    }

    /**
     * Map Midtrans status to our system status
     */
    private function mapMidtransStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return $fraudStatus === 'challenge' ? 'processing' : 'paid';
            case 'settlement':
                return 'paid';
            case 'pending':
                return 'pending';
            case 'deny':
            case 'cancel':
            case 'expire':
                return 'failed';
            default:
                return 'pending';
        }
    }

    /**
     * Get payment type for Midtrans
     */
    private function getPaymentType($methodType)
    {
        $mapping = [
            'virtual_account' => 'bank_transfer',
            'e_wallet' => 'gopay',
            'credit_card' => 'credit_card',
            'bank_transfer' => 'bank_transfer'
        ];

        return $mapping[$methodType] ?? 'bank_transfer';
    }

    /**
     * Get bank code for virtual account
     */
    private function getBankCode($bankAccount)
    {
        if (!$bankAccount) return 'bca';

        // Convert to uppercase for consistent mapping
        $bankCode = strtoupper($bankAccount->bank_code);
        
        $mapping = [
            'BCA' => 'bca',
            'MANDIRI' => 'echannel',
            'BNI' => 'bni',
            'BRI' => 'bri',
            'PERMATA' => 'permata',
            'CIMB' => 'cimb'
        ];

        // Also check lowercase version
        $lowercaseMapping = [
            'bca' => 'bca',
            'mandiri' => 'echannel',
            'bni' => 'bni',
            'bri' => 'bri',
            'permata' => 'permata',
            'cimb' => 'cimb'
        ];

        return $mapping[$bankCode] ?? $lowercaseMapping[$bankAccount->bank_code] ?? $bankAccount->bank_code;
    }

    /**
     * Extract virtual account number from response
     */
    private function extractVirtualAccount($response)
    {
        // For Mandiri VA (echannel), use bill_key
        if (isset($response['bill_key'])) {
            return $response['bill_key'];
        }

        // For other VA banks, use va_numbers
        if (isset($response['va_numbers']) && !empty($response['va_numbers'])) {
            return $response['va_numbers'][0]['va_number'];
        }

        // For Permata VA specifically
        if (isset($response['permata_va_number'])) {
            return $response['permata_va_number'];
        }

        return null;
    }

    /**
     * Create payment via Midtrans Core API
     */
    public function createCoreApiPayment(PaymentTransaction $transaction, $paymentMethod, $bankCode = null)
    {
        try {
            Log::info('🚀 Creating Core API payment', [
                'transaction_code' => $transaction->transaction_code,
                'payment_method' => $paymentMethod,
                'bank_code' => $bankCode,
                'amount' => $transaction->total_amount
            ]);

            // Prepare payment data based on method
            $paymentData = $this->prepareCoreApiData($transaction, $paymentMethod, $bankCode);
            
            // Send to Midtrans Core API
            $response = $this->sendToMidtrans('/charge', $paymentData);
            
            if (in_array($response['status_code'], ['200', '201'])) {
                // Update transaction with gateway response
                $transaction->update([
                    'gateway_transaction_id' => $response['transaction_id'],
                    'gateway_reference' => $response['order_id'],
                    'gateway_response' => $response,
                    'status' => $this->mapMidtransStatus($response['transaction_status']),
                    'virtual_account' => $this->extractVirtualAccount($response),
                ]);

                Log::info('✅ Core API payment created successfully', [
                    'transaction_code' => $transaction->transaction_code,
                    'midtrans_status' => $response['transaction_status'],
                    'system_status' => $transaction->status
                ]);

                return [
                    'success' => true,
                    'data' => $response,
                    'transaction' => $transaction->fresh()
                ];
            }

            Log::warning('❌ Core API payment creation failed', [
                'transaction_code' => $transaction->transaction_code,
                'response' => $response
            ]);

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Payment creation failed',
                'data' => $response
            ];

        } catch (Exception $e) {
            Log::error('❌ Core API Payment Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'payment_method' => $paymentMethod,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare Core API payment data
     */
    private function prepareCoreApiData(PaymentTransaction $transaction, $paymentMethod, $bankCode = null)
    {
        $mahasiswa = $transaction->mahasiswa;
        $tagihan = $transaction->tagihan;

        // Validate and fix customer data
        $email = $mahasiswa->email ?? 'student@example.com';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'student' . $mahasiswa->id . '@example.com';
        }

        $phone = $mahasiswa->phone ?? '08123456789';
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (empty($phone) || strlen($phone) < 10) {
            $phone = '08123456789';
        }

        $name = $mahasiswa->name ?? 'Student';
        if (empty($name) || strlen($name) < 2) {
            $name = 'Student ' . $mahasiswa->id;
        }

        // Base payment data
        $baseData = [
            'payment_type' => $this->getCoreApiPaymentType($paymentMethod),
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total_amount
            ],
            'customer_details' => [
                'first_name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
            'item_details' => [
                [
                    'id' => $tagihan->code ?? 'TAGIHAN-' . $transaction->id,
                    'price' => (int) $transaction->amount,
                    'quantity' => 1,
                    'name' => $tagihan->desc ?? 'Pembayaran Akademik'
                ]
            ]
        ];

        // Add fee if exists
        if ($transaction->fee_amount > 0) {
            $baseData['item_details'][] = [
                'id' => 'FEE',
                'price' => (int) $transaction->fee_amount,
                'quantity' => 1,
                'name' => 'Biaya Admin'
            ];
        }

        // Add payment method specific data
        switch ($paymentMethod) {
            case 'bca_va':
            case 'bni_va':
            case 'bri_va':
            case 'permata_va':
            case 'cimb_va':
            case 'danamon_va':
            case 'bsi_va':
            case 'btn_va':
            case 'maybank_va':
            case 'bjb_va':
            case 'mega_va':
            case 'bukopin_va':
            case 'panin_va':
            case 'ocbc_va':
            case 'other_va':
                $baseData['bank_transfer'] = [
                    'bank' => $this->getBankCodeFromMethod($paymentMethod)
                ];
                break;

            case 'mandiri_va':
                // PENTING: Mandiri VA menggunakan echannel, bukan bank_transfer
                $baseData['payment_type'] = 'echannel';
                $baseData['echannel'] = [
                    'bill_info1' => 'Payment for: ' . ($tagihan->desc ?? 'Academic Payment'),
                    'bill_info2' => 'Student: ' . $name
                ];
                break;

            case 'gopay':
                $baseData['payment_type'] = 'gopay';
                $baseData['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('payment.callback')
                ];
                break;

            case 'ovo':
                // OVO is not supported in Midtrans Core API, redirect to Snap
                $baseData['payment_type'] = 'gopay';
                $baseData['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('payment.callback')
                ];
                
                Log::info('⚠️ OVO not supported in Core API, using GoPay as fallback', [
                    'original_method' => 'ovo',
                    'fallback_method' => 'gopay'
                ]);
                break;

            case 'dana':
                // DANA is not supported in Midtrans Core API, redirect to Snap
                $baseData['payment_type'] = 'gopay';
                $baseData['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('payment.callback')
                ];
                
                Log::info('⚠️ DANA not supported in Core API, using GoPay as fallback', [
                    'original_method' => 'dana',
                    'fallback_method' => 'gopay'
                ]);
                break;

            case 'shopeepay':
                // ShopeePay is not supported in Midtrans Core API, redirect to Snap
                $baseData['payment_type'] = 'gopay';
                $baseData['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('payment.callback')
                ];
                
                Log::info('⚠️ ShopeePay not supported in Core API, using GoPay as fallback', [
                    'original_method' => 'shopeepay',
                    'fallback_method' => 'gopay'
                ]);
                break;

            case 'linkaja':
                // LinkAja is not supported in Midtrans Core API, redirect to Snap
                $baseData['payment_type'] = 'gopay';
                $baseData['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('payment.callback')
                ];
                
                Log::info('⚠️ LinkAja not supported in Core API, using GoPay as fallback', [
                    'original_method' => 'linkaja',
                    'fallback_method' => 'gopay'
                ]);
                break;

            case 'qris':
                $baseData['payment_type'] = 'qris';
                $baseData['qris'] = [
                    'acquirer' => 'gopay'
                ];
                
                // For sandbox/simulator, add additional QRIS configuration
                if (!$this->isProduction) {
                    $baseData['qris']['merchant_id'] = config('midtrans.merchant_id');
                }
                break;

            case 'credit_card':
                $baseData['payment_type'] = 'credit_card';
                $baseData['credit_card'] = [
                    'secure' => true,
                    'save_card' => false
                ];
                break;
        }

        return $baseData;
    }

    /**
     * Get Core API payment type
     */
    private function getCoreApiPaymentType($paymentMethod)
    {
        $mapping = [
            // Major Banks (Fixed Mapping)
            'bca_va' => 'bank_transfer',
            'mandiri_va' => 'echannel',  // PENTING: Mandiri harus 'echannel', bukan 'bank_transfer'
            'bni_va' => 'bank_transfer',
            'bri_va' => 'bank_transfer',
            'permata_va' => 'bank_transfer',
            'cimb_va' => 'bank_transfer',
            
            // Additional Banks (New Support)
            'danamon_va' => 'bank_transfer',
            'bsi_va' => 'bank_transfer',
            'btn_va' => 'bank_transfer',
            'maybank_va' => 'bank_transfer',
            'bjb_va' => 'bank_transfer',
            'mega_va' => 'bank_transfer',
            'bukopin_va' => 'bank_transfer',
            'panin_va' => 'bank_transfer',
            'ocbc_va' => 'bank_transfer',
            'other_va' => 'bank_transfer',
            
            // E-Wallets - FIXED: Use correct Midtrans payment_type values
            'gopay' => 'gopay',
            'ovo' => 'ovo',
            'dana' => 'dana',
            'shopeepay' => 'shopeepay',
            'linkaja' => 'linkaja',
            
            // Other Methods
            'qris' => 'qris',
            'credit_card' => 'credit_card'
        ];

        $paymentType = $mapping[$paymentMethod] ?? 'bank_transfer';
        
        Log::info('🔧 Payment type mapping', [
            'payment_method' => $paymentMethod,
            'mapped_payment_type' => $paymentType
        ]);

        return $paymentType;
    }

    /**
     * Get bank code from payment method
     */
    private function getBankCodeFromMethod($paymentMethod)
    {
        // Banks that are confirmed working with Midtrans
        $supportedBanks = [
            'bca_va' => 'bca',
            'mandiri_va' => 'echannel',
            'bni_va' => 'bni',
            'bri_va' => 'bri',
            'permata_va' => 'permata',
            'cimb_va' => 'cimb'
        ];
        
        // Banks that may not be supported - fallback to 'other'
        $fallbackBanks = [
            'danamon_va' => 'other',
            'bsi_va' => 'other',
            'btn_va' => 'other',
            'maybank_va' => 'other',
            'bjb_va' => 'other',
            'mega_va' => 'other',
            'bukopin_va' => 'other',
            'panin_va' => 'other',
            'ocbc_va' => 'other',
            'other_va' => 'other'
        ];

        // Log the bank selection for debugging
        if (isset($fallbackBanks[$paymentMethod])) {
            Log::info('🔄 Using fallback bank for unsupported VA', [
                'requested_bank' => $paymentMethod,
                'fallback_to' => 'other',
                'reason' => 'Bank may not be supported by Midtrans'
            ]);
        }

        return $supportedBanks[$paymentMethod] ?? $fallbackBanks[$paymentMethod] ?? 'bca';
    }

    /**
     * 🚀 AUTO-SYNC PAYMENT TO SALDO TABLE
     * This method automatically creates Saldo entry when payment is verified
     */
    private function syncPaymentToSaldo(PaymentTransaction $transaction, $isSimulator = false)
    {
        try {
            // Check if already synced to avoid duplicates
            $existingSaldo = \App\Models\Keuangan\Saldo::where('source', 'like', "%{$transaction->transaction_code}%")
                ->where('amount', $transaction->total_amount)
                ->first();
            
            if ($existingSaldo) {
                Log::info('⏭️ Payment already synced to Saldo', [
                    'transaction_code' => $transaction->transaction_code,
                    'existing_saldo_id' => $existingSaldo->id
                ]);
                return;
            }
            
            // Get mahasiswa info
            $mahasiswa = $transaction->mahasiswa;
            if (!$mahasiswa) {
                throw new Exception("Mahasiswa not found for transaction {$transaction->transaction_code}");
            }
            
            // Get tagihan info
            $tagihan = $transaction->tagihan;
            if (!$tagihan) {
                throw new Exception("Tagihan not found for transaction {$transaction->transaction_code}");
            }
            
            // Generate unique codes for Saldo
            $code = 'AUTO-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            $transactionCode = 'TRX-AUTO-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            
            // Determine source based on payment method
            $paymentMethodName = $transaction->paymentMethod->name ?? 'Unknown Payment';
            if ($transaction->paymentMethod && $transaction->paymentMethod->type === 'midtrans_snap') {
                $source = "Midtrans - {$mahasiswa->name} ({$mahasiswa->numb_nim})";
            } else {
                $source = "{$paymentMethodName} - {$mahasiswa->name} ({$mahasiswa->numb_nim})";
            }
            
            // Create Saldo entry
            $saldo = \App\Models\Keuangan\Saldo::create([
                'tagihan_id' => $tagihan->id,
                'code' => $code,
                'type' => 'Pemasukan',
                'status' => 'Sukses',
                'amount' => $transaction->total_amount,
                'transaction_code' => $transactionCode,
                'source' => $source,
                'created_at' => $transaction->verified_at ?? now(),
                'updated_at' => now()
            ]);
            
            Log::info('✅ Payment auto-synced to Saldo successfully', [
                'transaction_code' => $transaction->transaction_code,
                'saldo_id' => $saldo->id,
                'mahasiswa' => $mahasiswa->name,
                'nim' => $mahasiswa->numb_nim,
                'amount' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                'source' => $source,
                'is_simulator' => $isSimulator
            ]);
            
        } catch (Exception $e) {
            Log::error('❌ Auto-sync to Saldo failed', [
                'transaction_code' => $transaction->transaction_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to be caught by calling method
            throw $e;
        }
    }

    /**
     * Schedule auto-verification for simulator payments
     */
    private function scheduleAutoVerification(PaymentTransaction $transaction)
    {
        // For simulator, we'll create a simple delayed verification
        // In production, this would be handled by actual webhook from payment gateway
        
        try {
            // Simulate successful payment after a short delay
            $simulatedWebhookData = [
                'order_id' => $transaction->transaction_code,
                'transaction_status' => 'settlement',
                'status_code' => '200',
                'gross_amount' => $transaction->total_amount,
                'payment_type' => 'bank_transfer',
                'transaction_time' => now()->toISOString(),
                'signature_key' => 'simulated_signature',
                'fraud_status' => 'accept'
            ];
            
            // Process the simulated webhook immediately for testing
            // In real scenario, this would come from external webhook
            $this->handleWebhook($simulatedWebhookData);
            
            Log::info('Auto-verification scheduled for simulator payment', [
                'transaction_code' => $transaction->transaction_code
            ]);
            
        } catch (Exception $e) {
            Log::error('Auto-verification scheduling failed: ' . $e->getMessage());
        }
    }
}
