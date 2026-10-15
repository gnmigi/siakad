<?php

namespace App\Http\Controllers\Private\Mahasantri;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentReceipt;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Pengaturan\WebSetting;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get common template variables
     */
    private function getTemplateData()
    {
        $webs = WebSetting::first();
        return [
            'webs' => $webs,
            'spref' => 'mahasiswa.',
            'academy' => $webs->school_apps . ' oleh ' . $webs->school_name,
        ];
    }

    /**
     * Display payment dashboard for student
     */
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Get unpaid tagihan - include both Pending and Cicilan (partially paid)
        $unpaidTagihan = TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
            ->with(['group'])
            ->orderBy('due_date')
            ->get();

        // Get payment history
        $paymentHistory = $this->paymentService->getPaymentHistory($mahasiswa->id, 10);

        // Calculate remaining amounts for statistics
        $totalRemainingAmount = 0;
        foreach ($unpaidTagihan as $tagihan) {
            $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
            $totalRemainingAmount += $remainingAmount;
        }

        // Get payment statistics
        $stats = [
            'total_tagihan' => TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)->count(),
            'paid_tagihan' => TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', ['Sukses', 'sukses', 'SUKSES'])->count(),
            'pending_amount' => $totalRemainingAmount,
            'total_paid' => PaymentTransaction::where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'verified')
                ->sum('total_amount'),
        ];

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Dashboard Pembayaran";

        return view('mahasantri.pages.payment-dashboard', $data, compact(
            'mahasiswa', 
            'unpaidTagihan', 
            'paymentHistory', 
            'stats'
        ));
    }

    /**
     * Show payment form for specific tagihan
     */
    public function create($tagihanId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Allow both Pending and Cicilan (partially paid) tagihan
        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
            ->firstOrFail();

        // Check if payment already exists
        // NOTE: exclude verified manual bank transfer so student can create/upload new verification flow if needed
        $existingPaymentQuery = PaymentTransaction::where('tagihan_id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['pending', 'processing', 'paid', 'verified']);

        $existingPayment = $existingPaymentQuery->first();

        if ($existingPayment) {
            $isVerifiedManualBankTransfer = $existingPayment->status === 'verified'
                && (int)($existingPayment->is_manual ?? 0) === 1
                && optional($existingPayment->paymentMethod)->type === 'bank_transfer';

            if (!$isVerifiedManualBankTransfer) {
                return redirect()->route('mahasiswa.payment.show', $existingPayment->id)
                    ->with('info', 'Pembayaran untuk tagihan ini sudah ada');
            }
        }

        // Get available payment methods
        $paymentData = $this->paymentService->getAvailablePaymentMethods($mahasiswa->id);
        
        // Extract payment methods and bank accounts from service response
        $paymentMethods = $paymentData['payment_methods'] ?? [];
        $bankAccounts = $paymentData['bank_accounts'] ?? [];

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Buat Pembayaran";

        return view('mahasantri.pages.payment-create', $data, compact(
            'mahasiswa',
            'tagihan',
            'paymentMethods',
            'bankAccounts'
        ));
    }

    /**
     * Store payment transaction
     */
    public function store(Request $request)
    {
        try {
            // Fix CSRF token for production
            if (app()->environment("production") && !$request->session()->token()) {
                $request->session()->regenerateToken();
            }

            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            // FIXED: Better validation and error handling
            if (!$mahasiswa) {
                Log::error('❌ No authenticated mahasiswa found');
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Authentication required'
                    ], 401);
                }
                return redirect()->route('mahasiswa.login')->with('error', 'Silakan login terlebih dahulu');
            }

            // Log incoming request for debugging
            Log::info('💳 Payment store request received', [
                'mahasiswa_id' => $mahasiswa->id,
                'request_data' => $request->except(['_token']), // Exclude token from logs
                'has_payment_method_type' => $request->has('payment_method_type'),
                'has_selected_bank' => $request->has('selected_bank'),
                'has_payment_method_id' => $request->has('payment_method_id'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ]);

            // FIXED: Better request validation
            $request->validate([
                'tagihan_id' => 'required|exists:tagihan_kuliahs,id',
                'payment_method_id' => 'nullable|exists:payment_methods,id',
                'payment_method_type' => 'nullable|string',
                'selected_bank' => 'nullable|string',
                'payment_mode' => 'nullable|in:full,installment',
                'payment_amount' => 'nullable|numeric|min:1'
            ]);

            // Handle new modern UI form structure - IMPROVED
            if ($request->has('payment_method_type') || $request->has('selected_bank')) {
                $paymentMethodType = $request->payment_method_type;
                $selectedBank = $request->selected_bank;

                Log::info('🔄 Processing modern payment form', [
                    'payment_method_type' => $paymentMethodType,
                    'selected_bank' => $selectedBank
                ]);

                // Define Virtual Account banks that should use Core API
                $vaBanks = [
                    'bca_va', 'mandiri_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va',
                    'danamon_va', 'maybank_va', 'btn_va', 'bsi_va', 'mega_va', 'ocbc_va',
                    'panin_va', 'bukopin_va', 'bjb_va', 'banten_va', 'dki_va', 'jateng_va',
                    'jatim_va', 'sumut_va', 'sumsel_va', 'kalbar_va', 'kaltim_va', 'sulselbar_va'
                ];

                // Handle different payment method types
                if ($paymentMethodType === 'virtual_account' || 
                    in_array($paymentMethodType, ['e_wallet', 'qris', 'credit_card']) ||
                    ($selectedBank && in_array($selectedBank, $vaBanks))) {
                    // Use modern Core API for Virtual Account, E-Wallet, QRIS, Credit Card
                    return $this->handleModernPayment($request, $mahasiswa, $paymentMethodType, $selectedBank);
                } elseif ($paymentMethodType === 'bank_transfer') {
                    // Handle manual bank transfer (only for actual manual transfers)
                    return $this->handleManualBankTransfer($request, $mahasiswa);
                } elseif ($paymentMethodType === 'cash') {
                    // Handle cash payment
                    return $this->handleCashPayment($request, $mahasiswa);
                } else {
                    Log::warning('⚠️ Unknown payment method type', [
                        'payment_method_type' => $paymentMethodType,
                        'selected_bank' => $selectedBank
                    ]);
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Metode pembayaran tidak dikenali'
                        ], 422);
                    }
                    return redirect()->back()->with('error', 'Metode pembayaran tidak dikenali')->withInput();
                }
            } else {
                // Legacy form structure - IMPROVED
                if (!$request->payment_method_id) {
                    Log::warning('⚠️ Missing payment method ID in legacy form');
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Payment method ID is required'
                        ], 422);
                    }
                    return redirect()->back()->with('error', 'Payment method ID is required')->withInput();
                }

                Log::info('🔄 Processing legacy payment form', [
                    'payment_method_id' => $request->payment_method_id,
                    'bank_account_id' => $request->bank_account_id
                ]);

                // FIXED: Better error handling for PaymentService
                $result = $this->paymentService->createPayment(
                    $request->tagihan_id,
                    $mahasiswa->id,
                    $request->payment_method_id,
                    $request->bank_account_id
                );

                if ($result['success']) {
                    $transaction = $result['transaction'];
                    $paymentMethod = $transaction->paymentMethod;
                    
                    Log::info('✅ Payment created successfully', [
                        'transaction_id' => $transaction->id,
                        'transaction_code' => $transaction->transaction_code,
                        'payment_method_type' => $paymentMethod->type ?? 'unknown'
                    ]);
                    
                    // PENTING: Untuk Midtrans Snap, handle both AJAX and regular requests
                    if ($paymentMethod && $paymentMethod->type === 'midtrans_snap') {
                        $gatewayResponse = $transaction->gateway_response ?? [];
                        
                        Log::info('🔍 Checking Midtrans Snap token', [
                            'transaction_code' => $transaction->transaction_code,
                            'gateway_response_exists' => !empty($gatewayResponse),
                            'token_exists' => isset($gatewayResponse['token']),
                            'gateway_response_keys' => array_keys($gatewayResponse),
                            'is_ajax' => $request->ajax()
                        ]);
                        
                        if (isset($gatewayResponse['token']) && !empty($gatewayResponse['token'])) {
                            // Store Snap token in session for popup use
                            session(['snap_token' => $gatewayResponse['token']]);
                            session(['snap_transaction_id' => $transaction->id]);
                            
                            Log::info('✅ Midtrans Snap token stored in session', [
                                'transaction_code' => $transaction->transaction_code,
                                'token_length' => strlen($gatewayResponse['token']),
                                'token_preview' => substr($gatewayResponse['token'], 0, 20) . '...'
                            ]);
                            
                            // Handle AJAX request (for popup)
                            if ($request->ajax()) {
                                return response()->json([
                                    'success' => true,
                                    'snap_token' => $gatewayResponse['token'],
                                    'transaction_id' => $transaction->id,
                                    'transaction_code' => $transaction->transaction_code,
                                    'redirect_url' => route('mahasiswa.payment.show', $transaction->id)
                                ]);
                            }
                            
                            // Handle regular request (redirect)
                            return redirect()->route('mahasiswa.payment.show', $transaction->id)
                                ->with('success', 'Pembayaran berhasil dibuat. Silakan lanjutkan pembayaran melalui Midtrans Snap.');
                        } else {
                            // Token tidak tersedia - ini adalah masalah serius
                            Log::error('❌ Midtrans Snap token not generated', [
                                'transaction_code' => $transaction->transaction_code,
                                'transaction_id' => $transaction->id,
                                'gateway_response' => $gatewayResponse,
                                'payment_method_id' => $request->payment_method_id
                            ]);
                            
                            // Cancel the transaction since token is not available
                            $transaction->update(['status' => 'failed']);
                            
                            // Handle AJAX request
                            if ($request->ajax()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Snap token not received. Terjadi kesalahan saat membuat pembayaran Midtrans.'
                                ], 400);
                            }
                            
                            return redirect()->back()
                                ->with('error', 'Snap token not received. Terjadi kesalahan saat membuat pembayaran Midtrans. Silakan coba lagi atau hubungi admin.')
                                ->withInput();
                        }
                    }
                    
                    // Untuk payment method lain, redirect ke halaman detail
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'transaction_id' => $transaction->id,
                            'redirect_url' => route('mahasiswa.payment.show', $transaction->id)
                        ]);
                    }
                    
                    return redirect()->route('mahasiswa.payment.show', $transaction->id)
                        ->with('success', 'Pembayaran berhasil dibuat');
                } else {
                    Log::error('❌ Payment creation failed', [
                        'tagihan_id' => $request->tagihan_id,
                        'mahasiswa_id' => $mahasiswa->id,
                        'payment_method_id' => $request->payment_method_id,
                        'error_message' => $result['message'] ?? 'Unknown error'
                    ]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $result['message'] ?? 'Gagal membuat pembayaran'
                        ], 400);
                    }

                    return redirect()->back()
                        ->with('error', $result['message'] ?? 'Gagal membuat pembayaran')
                        ->withInput();
                }
            }
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('⚠️ Validation error in payment store', [
                'errors' => $e->errors(),
                'mahasiswa_id' => $mahasiswa->id ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid: ' . implode(', ', Arr::flatten($e->errors()))
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('❌ Payment store system error: ' . $e->getMessage(), [
                'request_data' => $request->except(['_token']),
                'mahasiswa_id' => $mahasiswa->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.',
                    'error_code' => 'SYSTEM_ERROR'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.')
                ->withInput();
        }
    }

    /**
     * Handle modern payment methods (VA, E-Wallet, QRIS, Credit Card)
     */
    private function handleModernPayment(Request $request, $mahasiswa, $paymentMethodType, $selectedBank)
    {
        // Redirect to modern Core API endpoint
        return redirect()->route('mahasiswa.payment.checkout-modern', $request->tagihan_id)
            ->with('selected_method', $paymentMethodType)
            ->with('selected_bank', $selectedBank);
    }

    /**
     * Handle manual bank transfer
     */
    private function handleManualBankTransfer(Request $request, $mahasiswa)
    {
        // Extract bank account ID from selected_bank (format: manual_X)
        $bankAccountId = null;
        if ($request->selected_bank && strpos($request->selected_bank, 'manual_') === 0) {
            $bankAccountId = str_replace('manual_', '', $request->selected_bank);
        }

        if (!$bankAccountId) {
            return redirect()->back()
                ->with('error', 'Silakan pilih rekening bank tujuan')
                ->withInput();
        }

        // Find a manual bank transfer payment method
        $paymentMethod = \App\Models\Keuangan\PaymentMethod::where('type', 'bank_transfer')->first();
        if (!$paymentMethod) {
            return redirect()->back()
                ->with('error', 'Metode pembayaran transfer bank tidak tersedia')
                ->withInput();
        }

        $tagihan = TagihanKuliah::where('id', $request->tagihan_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $remainingAmount = (float) ($tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0)));
        $remainingAmount = max($remainingAmount, 0);

        $paymentMode = $request->input('payment_mode', 'full');
        $requestedAmount = (float) $request->input('payment_amount', 0);

        $effectiveAmount = $paymentMode === 'installment' ? $requestedAmount : $remainingAmount;

        if ($effectiveAmount <= 0) {
            return redirect()->back()
                ->with('error', 'Nominal pembayaran harus lebih dari 0')
                ->withInput();
        }

        if ($effectiveAmount > $remainingAmount) {
            return redirect()->back()
                ->with('error', 'Nominal pembayaran tidak boleh melebihi sisa tagihan')
                ->withInput();
        }

        $result = $this->paymentService->createPayment(
            $request->tagihan_id,
            $mahasiswa->id,
            $paymentMethod->id,
            $bankAccountId
        );

        if ($result['success']) {
            $transaction = $result['transaction'];

            // Force transaction nominal to selected effective amount (full/cicilan)
            $transaction->update([
                'amount' => (int) round($effectiveAmount),
                'total_amount' => (int) round($effectiveAmount),
                'paid_amount' => (int) round($effectiveAmount),
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_mode' => $paymentMode,
                    'requested_payment_amount' => $requestedAmount,
                    'effective_payment_amount' => $effectiveAmount,
                    'remaining_before_payment' => $remainingAmount,
                ])
            ]);
            $transaction->refresh();

            // Handle file upload if provided
            if ($request->hasFile('transfer_proof')) {
                $data = [
                    'proof_file' => $request->file('transfer_proof'),
                    'sender_name' => $mahasiswa->name,
                    'sender_account' => null,
                    'payment_date' => now(),
                    'notes' => $request->notes,
                    'amount' => $effectiveAmount,
                ];

                $verificationResult = app(\App\Services\PaymentVerificationService::class)
                    ->createManualVerification($transaction->id, $data);

                if ($verificationResult['success']) {
                    // force processing while waiting admin verification
                    if ($transaction->status !== PaymentTransaction::STATUS_VERIFIED) {
                        $transaction->update([
                            'status' => PaymentTransaction::STATUS_PROCESSING,
                            'notes' => trim(($transaction->notes ? $transaction->notes . "\n" : '') . 'Bukti transfer diunggah, menunggu verifikasi admin')
                        ]);
                    }

                    return redirect()->route('mahasiswa.payment.show', $transaction->id)
                        ->with('success', 'Pembayaran berhasil dibuat dan bukti transfer telah diunggah. Menunggu verifikasi admin.');
                }

                return redirect()->route('mahasiswa.payment.show', $transaction->id)
                    ->with('error', $verificationResult['message'] ?? 'Upload bukti gagal dibuat untuk verifikasi. Silakan upload ulang.');
            }

            // No proof uploaded yet => keep waiting state for manual transfer
            if ($transaction->status !== PaymentTransaction::STATUS_VERIFIED) {
                $transaction->update([
                    'status' => PaymentTransaction::STATUS_PENDING,
                    'is_manual' => 1,
                    'notes' => trim(($transaction->notes ? $transaction->notes . "\n" : '') . 'Menunggu upload bukti transfer dari mahasantri')
                ]);
            }

            return redirect()->route('mahasiswa.payment.show', $transaction->id)
                ->with('success', 'Pembayaran berhasil dibuat. Silakan upload bukti transfer.');
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Handle cash payment
     */
    private function handleCashPayment(Request $request, $mahasiswa)
    {
        // Find cash payment method
        $paymentMethod = \App\Models\Keuangan\PaymentMethod::where('type', 'cash')->first();
        if (!$paymentMethod) {
            return redirect()->back()
                ->with('error', 'Metode pembayaran tunai tidak tersedia')
                ->withInput();
        }

        $result = $this->paymentService->createPayment(
            $request->tagihan_id,
            $mahasiswa->id,
            $paymentMethod->id,
            null
        );

        if ($result['success']) {
            $transaction = $result['transaction'];
            
            return redirect()->route('mahasiswa.payment.show', $transaction->id)
                ->with('success', 'Pembayaran tunai berhasil dibuat. Silakan datang ke bagian keuangan untuk melakukan pembayaran.');
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Show payment details and instructions
     */
    public function show($transactionId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan', 'paymentMethod', 'bankAccount', 'receipts'])
            ->firstOrFail();

        // AUTO-CHECK: If payment is pending or processing, try to detect settlement automatically
        if (in_array($transaction->status, ['pending', 'processing'])) {
            $this->autoCheckPaymentSettlement($transaction);
            $transaction = $transaction->fresh(); // Reload after potential update
        }

        $paymentInfo = $this->getPaymentInfo($transaction);

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Detail Pembayaran";

        return view('mahasantri.pages.payment-show', $data, compact(
            'mahasiswa',
            'transaction',
            'paymentInfo'
        ));
    }

    /**
     * Auto-check payment settlement when page is refreshed (DISABLED)
     * This method has been disabled to prevent false auto-verifications
     */
    private function autoCheckPaymentSettlement(PaymentTransaction $transaction)
    {
        // DISABLED: Auto-check functionality removed to prevent false verifications
        // Only manual status checks via button clicks are now allowed
        
        Log::info("ℹ️ Auto-check disabled - manual verification required", [
            'transaction_code' => $transaction->transaction_code,
            'current_status' => $transaction->status,
            'message' => 'Auto-verification disabled for security'
        ]);
        
        return;
    }
    
    /**
     * Complete payment verification process
     */
    private function completePaymentVerification(PaymentTransaction $transaction, $notes = 'Auto-verified')
    {
        try {
            // Update transaction to paid status first
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => array_merge($transaction->gateway_response ?? [], [
                    'transaction_status' => 'settlement',
                    'payment_type' => 'bank_transfer',
                    'settlement_time' => now()->toISOString(),
                    'gross_amount' => $transaction->total_amount,
                    'status_code' => '200',
                    'auto_verified' => true
                ])
            ]);
            
            // Create verification record
            $verificationService = app(\App\Services\PaymentVerificationService::class);
            $verificationData = [
                'amount' => $transaction->total_amount,
                'payment_date' => now(),
                'notes' => $notes,
                'verified_by' => 'system_auto'
            ];
            
            $verificationResult = $verificationService->createManualVerification(
                $transaction->id, 
                $verificationData
            );
            
            if ($verificationResult['success']) {
                Log::info("Payment verification completed: " . $transaction->transaction_code);
            }
            
        } catch (\Exception $e) {
            Log::error('Complete payment verification error: ' . $e->getMessage());
        }
    }

    /**
     * Get payment information for display
     */
    private function getPaymentInfo(PaymentTransaction $transaction)
    {
        $paymentInfo = [];
        
        // Get gateway response data
        $gatewayResponse = $transaction->gateway_response ?? [];
        
        switch ($transaction->paymentMethod->type) {
            case 'virtual_account':
                // Handle different VA types
                if (isset($gatewayResponse['bill_key'])) {
                    // Mandiri VA (echannel) uses bill_key
                    $paymentInfo['virtual_account'] = $gatewayResponse['bill_key'];
                    $paymentInfo['biller_code'] = $gatewayResponse['biller_code'] ?? null;
                } elseif (isset($gatewayResponse['va_numbers']) && !empty($gatewayResponse['va_numbers'])) {
                    // Other VA banks use va_numbers
                    $paymentInfo['virtual_account'] = $gatewayResponse['va_numbers'][0]['va_number'];
                } elseif (isset($gatewayResponse['permata_va_number'])) {
                    // Permata VA specifically
                    $paymentInfo['virtual_account'] = $gatewayResponse['permata_va_number'];
                } else {
                    // Fallback to transaction virtual_account field
                    $paymentInfo['virtual_account'] = $transaction->virtual_account;
                }
                break;
                
            case 'e_wallet':
                // Handle different e-wallet types
                if (isset($gatewayResponse['actions'])) {
                    foreach ($gatewayResponse['actions'] as $action) {
                        if ($action['name'] === 'generate-qr-code') {
                            $paymentInfo['qr_code'] = $action['url'];
                        }
                        if ($action['name'] === 'deeplink-redirect') {
                            $paymentInfo['payment_url'] = $action['url'];
                        }
                    }
                }
                
                // For QRIS specifically - use Midtrans QR Code URL
                if (strpos(strtolower($transaction->paymentMethod->name), 'qris') !== false || 
                    (isset($gatewayResponse['payment_type']) && $gatewayResponse['payment_type'] === 'qris')) {
                    
                    // Use direct QR Code URL from Midtrans actions
                    if (isset($gatewayResponse['actions'])) {
                        foreach ($gatewayResponse['actions'] as $action) {
                            if ($action['name'] === 'generate-qr-code') {
                                $paymentInfo['qr_code'] = $action['url'];
                                break;
                            }
                        }
                    }
                    
                    // If no QR code URL found, try to construct it from transaction_id
                    if (empty($paymentInfo['qr_code']) && isset($gatewayResponse['transaction_id'])) {
                        $baseUrl = config('midtrans.is_production') 
                            ? 'https://api.midtrans.com/v2' 
                            : 'https://api.sandbox.midtrans.com/v2';
                        $paymentInfo['qr_code'] = $baseUrl . '/qris/' . $gatewayResponse['transaction_id'] . '/qr-code';
                    }
                    
                    // Add QRIS string for simulator testing
                    if (isset($gatewayResponse['qr_string'])) {
                        $paymentInfo['qris_string'] = $gatewayResponse['qr_string'];
                    }
                    
                    // Add QR Code URL for copying
                    if (!empty($paymentInfo['qr_code'])) {
                        $paymentInfo['qr_code_url'] = $paymentInfo['qr_code'];
                    }
                }
                
                // For OVO specifically
                if (strpos(strtolower($transaction->paymentMethod->name), 'ovo') !== false) {
                    if (isset($gatewayResponse['ovo_push_url'])) {
                        $paymentInfo['payment_url'] = $gatewayResponse['ovo_push_url'];
                    }
                    if (isset($gatewayResponse['qr_code'])) {
                        $paymentInfo['qr_code'] = $gatewayResponse['qr_code'];
                    }
                }
                
                // For GoPay
                if (strpos(strtolower($transaction->paymentMethod->name), 'gopay') !== false) {
                    if (isset($gatewayResponse['gopay_redirect_url'])) {
                        $paymentInfo['payment_url'] = $gatewayResponse['gopay_redirect_url'];
                    }
                    if (isset($gatewayResponse['qr_code'])) {
                        $paymentInfo['qr_code'] = $gatewayResponse['qr_code'];
                    }
                }
                
                // Fallback for simulator - generate dummy QR code (except for QRIS)
                if (empty($paymentInfo['qr_code']) && !config('midtrans.is_production') && 
                    !(strpos(strtolower($transaction->paymentMethod->name), 'qris') !== false)) {
                    $paymentInfo['qr_code'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
                    $paymentInfo['payment_url'] = '#';
                    $paymentInfo['is_simulator'] = true;
                }
                break;
                
            case 'bank_transfer':
                if ($transaction->bankAccount) {
                    $paymentInfo['bank_account'] = $transaction->bankAccount;
                }
                break;
                
            case 'cash':
                $paymentInfo['instructions'] = 'Silakan datang ke bagian keuangan untuk melakukan pembayaran tunai';
                break;
        }
        
        return $paymentInfo;
    }

    /**
     * Upload payment proof for manual verification
     */
    public function uploadProof(Request $request, $transactionId)
    {
        $request->validate([
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sender_name' => 'nullable|string|max:255',
            'sender_account' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        // Allow re-upload proof for manual bank transfer in pending/processing status
        if (
            ($transaction->paymentMethod->type ?? null) !== 'bank_transfer' ||
            !in_array($transaction->status, ['pending', 'processing'])
        ) {
            return redirect()->back()->with('error', 'Pembayaran tidak dapat diproses dalam status saat ini');
        }

        $data = [
            'proof_file' => $request->file('proof_file'),
            'sender_name' => $request->sender_name,
            'sender_account' => $request->sender_account,
            'payment_date' => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now(),
            'notes' => $request->notes,
            'amount' => $transaction->total_amount,
        ];

        $result = app(\App\Services\PaymentVerificationService::class)
            ->createManualVerification($transactionId, $data);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang diverifikasi');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Cancel payment transaction
     */
    public function cancel(Request $request, $transactionId)
    {
        try {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            $transaction = PaymentTransaction::where('id', $transactionId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->firstOrFail();

            // Check if transaction can be cancelled
            if (!in_array($transaction->status, ['pending', 'processing'])) {
                $message = 'Pembayaran tidak dapat dibatalkan dalam status: ' . $transaction->status;
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return redirect()->back()->with('error', $message);
            }

            $result = $this->paymentService->cancelPayment($transactionId, 'Dibatalkan oleh mahasiswa');

            if ($result['success']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran berhasil dibatalkan',
                        'redirect_url' => route('mahasiswa.payment.index')
                    ]);
                }
                
                return redirect()->route('mahasiswa.payment.index')
                    ->with('success', 'Pembayaran berhasil dibatalkan');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal membatalkan pembayaran'
                ], 400);
            }

            return redirect()->back()->with('error', $result['message']);
            
        } catch (\Exception $e) {
            Log::error('Cancel payment error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString()
            ]);
            
            $message = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * View receipt in web browser
     */
    public function viewReceipt($transactionId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'verified')
            ->with(['tagihan', 'paymentMethod', 'bankAccount'])
            ->firstOrFail();

        // Get or create receipt
        $receipt = \App\Models\Keuangan\PaymentReceipt::where('payment_transaction_id', $transaction->id)->first();
        if (!$receipt) {
            $receipt = \App\Models\Keuangan\PaymentReceipt::createFromTransaction($transaction);
        }

        // Get template data required by layout
        $templateData = $this->getTemplateData();
        $templateData['menus'] = "Pembayaran";
        $templateData['pages'] = "Kwitansi Pembayaran";
        $templateData['mahasiswa'] = $mahasiswa;

        // Prepare data for receipt view
        $data = [
            'receipt' => $receipt,
            'transaction' => $transaction,
            'mahasiswa' => $mahasiswa,
            'tagihan' => $transaction->tagihan,
            'institution' => [
                'name' => config('app.institution_name', 'Institusi Pendidikan'),
                'address' => config('app.institution_address', 'Alamat Institusi'),
                'phone' => config('app.institution_phone', '021-12345678'),
                'email' => config('app.institution_email', 'info@institusi.ac.id'),
                'website' => config('app.url'),
                'logo' => asset('images/logo.png')
            ],
            'generated_at' => now(),
        ];

        // Mark as viewed
        $receipt->increment('download_count');
        if (!$receipt->downloaded_at) {
            $receipt->update(['downloaded_at' => now()]);
        }

        return view('mahasantri.pages.payment-receipt', array_merge($templateData, $data));
    }

    /**
     * Download receipt as PDF
     */
    public function downloadReceipt($transactionId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'verified')
            ->with(['tagihan', 'paymentMethod', 'bankAccount', 'receipts'])
            ->firstOrFail();

        // Get or create receipt
        $receipt = $transaction->receipts()->first();
        if (!$receipt) {
            $receipt = \App\Models\Keuangan\PaymentReceipt::createFromTransaction($transaction);
        }

        // Get tagihan data
        $tagihan = $transaction->tagihan;

        // Get template data
        $data = $this->getTemplateData();
        
        // Prepare data for PDF generation
        $pdfData = array_merge($data, [
            'transaction' => $transaction,
            'receipt' => $receipt,
            'mahasiswa' => $mahasiswa,
            'tagihan' => $tagihan,
            'generated_at' => now(),
            'institution' => [
                'name' => $data['webs']->school_name ?? 'Institusi Pendidikan',
                'address' => $data['webs']->school_address ?? 'Alamat Institusi',
                'phone' => $data['webs']->school_phone ?? '021-12345678',
                'email' => $data['webs']->school_email ?? 'info@institusi.ac.id',
                'website' => config('app.url'),
            ]
        ]);

        // Generate PDF using the web receipt view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mahasantri.pages.payment-receipt-pdf', $pdfData);
        $pdf->setPaper('A4', 'portrait');
        
        // Update download count
        $receipt->incrementDownloadCount();
        
        // Return PDF download
        return $pdf->download('kwitansi-' . $receipt->receipt_code . '.pdf');
    }

    /**
     * Check payment status (AJAX)
     */
    public function checkStatus($transactionId)
    {
        try {
            // Validate transaction ID
            if (!is_numeric($transactionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction ID'
                ], 400);
            }

            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $transaction = PaymentTransaction::where('id', $transactionId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['paymentMethod', 'tagihan'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            Log::info("🔍 Checking payment status", [
                'transaction_code' => $transaction->transaction_code,
                'current_status' => $transaction->status,
                'payment_method' => $transaction->paymentMethod->name ?? 'Unknown'
            ]);

            // If already verified, return current status
            if ($transaction->status === 'verified') {
                return response()->json([
                    'success' => true,
                    'status' => $transaction->status,
                    'status_badge' => '<span class="badge bg-success">✅ Terverifikasi</span>',
                    'message' => 'Payment already verified'
                ]);
            }

            // Check status for ALL pending/processing transactions immediately
            if (in_array($transaction->status, ['pending', 'processing'])) {
                
                Log::info("🚀 Attempting status check via button", [
                    'transaction_code' => $transaction->transaction_code,
                    'payment_method' => $transaction->paymentMethod->name ?? 'Unknown',
                    'current_status' => $transaction->status
                ]);
                
                try {
                    // Get REAL status from Midtrans
                    $gatewayService = app(\App\Services\PaymentGatewayService::class);
                    $statusResult = $gatewayService->checkPaymentStatus($transaction->transaction_code);
                    
                    if ($statusResult['success']) {
                        $midtransData = $statusResult['data'];
                        
                        // ONLY verify if Midtrans actually shows settlement/capture
                        if (in_array($midtransData['transaction_status'], ['settlement', 'capture'])) {
                            
                            Log::info("💰 Payment actually settled in Midtrans, processing verification", [
                                'transaction_code' => $transaction->transaction_code,
                                'midtrans_status' => $midtransData['transaction_status']
                            ]);
                            
                            // Process webhook to complete verification
                            $webhookData = array_merge($midtransData, [
                                'signature_key' => 'verified_status_check_' . time()
                            ]);
                            
                            $webhookResult = $gatewayService->handleWebhook($webhookData);
                            
                            if ($webhookResult['success']) {
                                $transaction = $transaction->fresh();
                                
                                Log::info("✅ Auto-verification completed via status check", [
                                    'transaction_code' => $transaction->transaction_code,
                                    'new_status' => $transaction->status
                                ]);
                                
                                return response()->json([
                                    'success' => true,
                                    'status' => $transaction->status,
                                    'status_badge' => '<span class="badge bg-success">✅ Terverifikasi</span>',
                                    'message' => 'Payment verified successfully!',
                                    'verified' => true,
                                    'payment_method' => $transaction->paymentMethod->name ?? 'Unknown'
                                ]);
                            }
                        } else {
                            Log::info("⏳ Payment still pending in Midtrans", [
                                'transaction_code' => $transaction->transaction_code,
                                'midtrans_status' => $midtransData['transaction_status']
                            ]);
                        }
                    } else {
                        Log::info("⚠️ Could not check Midtrans status", [
                            'transaction_code' => $transaction->transaction_code,
                            'error' => $statusResult['message']
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::error("❌ Error during status check: " . $e->getMessage(), [
                        'transaction_code' => $transaction->transaction_code
                    ]);
                    // Continue with normal flow if status check fails
                }
            }

            // Generate status badge
            $statusBadges = [
                'pending' => '<span class="badge bg-warning">Menunggu Pembayaran</span>',
                'processing' => '<span class="badge bg-info">Sedang Diproses</span>',
                'paid' => '<span class="badge bg-primary">Sudah Dibayar</span>',
                'verified' => '<span class="badge bg-success">✅ Terverifikasi</span>',
                'failed' => '<span class="badge bg-danger">Gagal</span>',
                'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
            ];

            $gatewayResponse = $transaction->gateway_response ?? [];
            
            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'status_badge' => $statusBadges[$transaction->status] ?? '<span class="badge bg-light">Unknown</span>',
                'message' => 'Status checked successfully',
                'midtrans_status' => $gatewayResponse['transaction_status'] ?? null,
                'transaction_code' => $transaction->transaction_code,
                'payment_method' => $transaction->paymentMethod ? $transaction->paymentMethod->name : 'Unknown'
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Payment status check error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Get Snap token for transaction (AJAX endpoint) - ENHANCED
     */
    public function getSnapToken($transactionId)
    {
        try {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            Log::info('🔍 Snap token request received', [
                'transaction_id' => $transactionId,
                'mahasiswa_id' => $mahasiswa->id,
                'user_agent' => request()->userAgent()
            ]);
            
            // Validate transaction ownership
            $transaction = PaymentTransaction::where('id', $transactionId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['paymentMethod', 'tagihan'])
                ->first();

            if (!$transaction) {
                Log::warning('❌ Transaction not found or access denied', [
                    'transaction_id' => $transactionId,
                    'mahasiswa_id' => $mahasiswa->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found or access denied',
                    'error_code' => 'TRANSACTION_NOT_FOUND'
                ], 404);
            }

            // Check if this is a Midtrans Snap transaction
            if ($transaction->paymentMethod->type !== 'midtrans_snap') {
                Log::warning('❌ Invalid payment method for Snap token', [
                    'transaction_id' => $transactionId,
                    'payment_method_type' => $transaction->paymentMethod->type
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Not a Midtrans Snap transaction',
                    'error_code' => 'INVALID_PAYMENT_METHOD'
                ], 400);
            }

            // Check transaction status
            if (!in_array($transaction->status, ['pending', 'processing'])) {
                Log::warning('❌ Invalid transaction status for token retrieval', [
                    'transaction_id' => $transactionId,
                    'current_status' => $transaction->status
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is not in a valid state for payment',
                    'error_code' => 'INVALID_TRANSACTION_STATUS'
                ], 400);
            }

            // Get token from session first
            $snapToken = session('snap_token');
            $sessionTransactionId = session('snap_transaction_id');

            // Verify session token belongs to this transaction
            if ($snapToken && $sessionTransactionId == $transactionId) {
                Log::info('✅ Snap token retrieved from session', [
                    'transaction_id' => $transactionId,
                    'token_length' => strlen($snapToken),
                    'transaction_code' => $transaction->transaction_code
                ]);

                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'transaction_id' => $transactionId,
                    'transaction_code' => $transaction->transaction_code,
                    'source' => 'session'
                ]);
            }

            // Fallback: Get token from transaction gateway_response
            $gatewayResponse = $transaction->gateway_response ?? [];
            if (isset($gatewayResponse['token']) && !empty($gatewayResponse['token'])) {
                Log::info('✅ Snap token retrieved from database', [
                    'transaction_id' => $transactionId,
                    'token_length' => strlen($gatewayResponse['token']),
                    'transaction_code' => $transaction->transaction_code
                ]);

                // Store in session for future use
                session(['snap_token' => $gatewayResponse['token']]);
                session(['snap_transaction_id' => $transactionId]);

                return response()->json([
                    'success' => true,
                    'snap_token' => $gatewayResponse['token'],
                    'transaction_id' => $transactionId,
                    'transaction_code' => $transaction->transaction_code,
                    'source' => 'database'
                ]);
            }

            // If no token found, try to generate a new one
            Log::info('🔧 No existing token found, attempting to generate new Snap token', [
                'transaction_id' => $transactionId,
                'transaction_code' => $transaction->transaction_code
            ]);

            try {
                $gatewayService = app(\App\Services\PaymentGatewayService::class);
                $result = $gatewayService->createPayment($transaction, $transaction->paymentMethod);

                if ($result['success'] && isset($result['data']['token'])) {
                    $newToken = $result['data']['token'];
                    
                    // Store new token in session and database
                    session(['snap_token' => $newToken]);
                    session(['snap_transaction_id' => $transactionId]);
                    
                    Log::info('✅ New Snap token generated successfully', [
                        'transaction_id' => $transactionId,
                        'token_length' => strlen($newToken),
                        'transaction_code' => $transaction->transaction_code
                    ]);

                    return response()->json([
                        'success' => true,
                        'snap_token' => $newToken,
                        'transaction_id' => $transactionId,
                        'transaction_code' => $transaction->transaction_code,
                        'source' => 'generated'
                    ]);
                } else {
                    Log::error('❌ Failed to generate new Snap token', [
                        'transaction_id' => $transactionId,
                        'error_message' => $result['message'] ?? 'Unknown error'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Exception during token generation', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage()
                ]);
            }

            // If we reach here, no token could be found or generated
            Log::error('❌ No Snap token available', [
                'transaction_id' => $transactionId,
                'session_token_exists' => !empty($snapToken),
                'session_transaction_id' => $sessionTransactionId,
                'gateway_response_exists' => !empty($gatewayResponse),
                'gateway_token_exists' => isset($gatewayResponse['token']),
                'transaction_code' => $transaction->transaction_code
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Snap token not available. Please try creating the payment again.',
                'error_code' => 'TOKEN_NOT_FOUND',
                'debug_info' => [
                    'transaction_code' => $transaction->transaction_code,
                    'payment_method' => $transaction->paymentMethod->name,
                    'status' => $transaction->status
                ]
            ], 404);

        } catch (\Exception $e) {
            Log::error('❌ Get Snap token system error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'mahasiswa_id' => $mahasiswa->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'System error occurred while retrieving Snap token',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Payment history
     */
    public function history()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $payments = PaymentTransaction::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan', 'paymentMethod', 'bankAccount', 'receipts'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Riwayat Pembayaran";
        $data['pages'] = "Riwayat Pembayaran";

        return view('mahasantri.pages.payment-history', $data, compact('mahasiswa', 'payments'));
    }

    /**
     * Modern checkout page with Core API
     */
    public function checkoutModern($tagihanId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Allow both Pending and Cicilan (partially paid) tagihan
        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
            ->firstOrFail();

        // Check if payment already exists
        $existingPayment = PaymentTransaction::where('tagihan_id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['pending', 'processing', 'paid', 'verified'])
            ->first();

        if ($existingPayment) {
            return redirect()->route('mahasiswa.payment.status-modern', $existingPayment->id)
                ->with('info', 'Pembayaran untuk tagihan ini sudah ada');
        }

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Checkout Pembayaran";

        return view('mahasantri.pages.payment-checkout-modern', $data, compact(
            'mahasiswa',
            'tagihan'
        ));
    }

    /**
     * Create transaction via Core API (AJAX)
     */
    public function createTransaction(Request $request)
    {
        try {
            $request->validate([
                'tagihan_id' => 'required|exists:tagihan_kuliahs,id',
                'payment_method_type' => 'nullable|string|in:virtual_account,e_wallet,qris,credit_card',
                'selected_bank' => 'nullable|string',
                'payment_method' => 'nullable|string|in:bca_va,mandiri_va,bni_va,bri_va,permata_va,cimb_va,danamon_va,bsi_va,btn_va,maybank_va,bjb_va,mega_va,bukopin_va,panin_va,ocbc_va,other_va,gopay,ovo,dana,shopeepay,linkaja,qris,credit_card',
                'bank_code' => 'nullable|string'
            ]);

            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            // Get tagihan - allow both Pending and Cicilan (partially paid) tagihan
            $tagihan = TagihanKuliah::where('id', $request->tagihan_id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
                ->firstOrFail();

            // Check if payment already exists
            $existingPayment = PaymentTransaction::where('tagihan_id', $request->tagihan_id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', ['pending', 'processing', 'paid', 'verified'])
                ->first();

            if ($existingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran untuk tagihan ini sudah ada',
                    'redirect_url' => route('mahasiswa.payment.status-modern', $existingPayment->id)
                ], 400);
            }

            // Get default payment method (first available) - cached for performance
            $defaultPaymentMethod = cache()->remember('default_payment_method', 3600, function () {
                return \App\Models\Keuangan\PaymentMethod::where('is_active', true)->first();
            });
            
            // Create payment transaction
            $transaction = PaymentTransaction::create([
                'transaction_code' => 'TRX-' . time() . '-' . $mahasiswa->id,
                'mahasiswa_id' => $mahasiswa->id,
                'tagihan_id' => $tagihan->id,
                'amount' => $tagihan->amount,
                'fee_amount' => 0, // No fee for now
                'total_amount' => $tagihan->amount,
                'status' => 'pending',
                'payment_method_id' => $defaultPaymentMethod ? $defaultPaymentMethod->id : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Process payment via Core API
            $gatewayService = app(\App\Services\PaymentGatewayService::class);
            // Determine payment method from new or old parameter structure
            $paymentMethod = $request->selected_bank ?? $request->payment_method;
            
            Log::info('🔧 Processing payment with:', [
                'payment_method_type' => $request->payment_method_type,
                'selected_bank' => $request->selected_bank,
                'payment_method' => $request->payment_method,
                'final_method' => $paymentMethod
            ]);
            
            $result = $gatewayService->createCoreApiPayment($transaction, $paymentMethod, $request->bank_code);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'payment_method' => $request->payment_method,
                    'payment_data' => $result['data'],
                    'redirect_url' => route('mahasiswa.payment.status-modern', $transaction->id)
                ]);
            } else {
                // Delete failed transaction
                $transaction->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal membuat transaksi pembayaran'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Create transaction error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Modern payment status page
     */
    public function statusModern($transactionId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan', 'paymentMethod', 'bankAccount'])
            ->firstOrFail();

        // Get payment info
        $paymentInfo = $this->getPaymentInfo($transaction);

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Status Pembayaran";

        return view('mahasantri.pages.payment-status-modern', $data, compact(
            'mahasiswa',
            'transaction',
            'paymentInfo'
        ));
    }

    /**
     * Check payment status via AJAX (for polling)
     */
    public function checkStatusModern($transactionId)
    {
        try {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            $transaction = PaymentTransaction::where('id', $transactionId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['paymentMethod', 'tagihan'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // If already verified, return current status
            if ($transaction->status === 'verified') {
                return response()->json([
                    'success' => true,
                    'status' => 'verified',
                    'status_text' => 'Pembayaran Berhasil',
                    'message' => 'Pembayaran telah terverifikasi',
                    'can_download_receipt' => true
                ]);
            }

            // Check status from Midtrans for pending/processing transactions
            if (in_array($transaction->status, ['pending', 'processing'])) {
                try {
                    $gatewayService = app(\App\Services\PaymentGatewayService::class);
                    $statusResult = $gatewayService->checkPaymentStatus($transaction->transaction_code);
                    
                    if ($statusResult['success']) {
                        $midtransData = $statusResult['data'];
                        
                        // If payment is settled in Midtrans, process webhook
                        if (in_array($midtransData['transaction_status'], ['settlement', 'capture'])) {
                            $webhookData = array_merge($midtransData, [
                                'signature_key' => 'status_check_' . time()
                            ]);
                            
                            $webhookResult = $gatewayService->handleWebhook($webhookData);
                            
                            if ($webhookResult['success']) {
                                $transaction = $transaction->fresh();
                                
                                return response()->json([
                                    'success' => true,
                                    'status' => $transaction->status,
                                    'status_text' => $this->getStatusText($transaction->status),
                                    'message' => 'Status pembayaran diperbarui',
                                    'can_download_receipt' => $transaction->status === 'verified'
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Status check error: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'status_text' => $this->getStatusText($transaction->status),
                'message' => 'Status checked successfully',
                'can_download_receipt' => $transaction->status === 'verified'
            ]);

        } catch (\Exception $e) {
            Log::error('Check status modern error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Get status text for display
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'paid' => 'Sudah Dibayar',
            'verified' => 'Pembayaran Berhasil',
            'failed' => 'Pembayaran Gagal',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa'
        ];

        return $statusTexts[$status] ?? 'Status Tidak Dikenal';
    }

    /**
     * Handle successful payment callback from Midtrans
     */
    public function success(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if ($transactionId) {
            // Redirect to status page for specific transaction
            return redirect()->route('mahasiswa.payment.status-modern', $transactionId)
                ->with('success', 'Pembayaran berhasil! Terima kasih atas pembayaran Anda.');
        }
        
        // Fallback to payment dashboard
        return redirect()->route('mahasiswa.payment.index')
            ->with('success', 'Pembayaran berhasil! Terima kasih atas pembayaran Anda.');
    }

    /**
     * Handle pending payment callback from Midtrans
     */
    public function pending(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if ($transactionId) {
            // Redirect to status page for specific transaction
            return redirect()->route('mahasiswa.payment.status-modern', $transactionId)
                ->with('info', 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.');
        }
        
        // Fallback to payment dashboard
        return redirect()->route('mahasiswa.payment.index')
            ->with('info', 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.');
    }

    /**
     * Handle failed payment callback from Midtrans
     */
    public function failed(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if ($transactionId) {
            // Redirect to status page for specific transaction
            return redirect()->route('mahasiswa.payment.status-modern', $transactionId)
                ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
        }
        
        // Fallback to payment dashboard
        return redirect()->route('mahasiswa.payment.index')
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }

    /**
     * View accumulated installment receipt (per tagihan)
     */
    public function viewAccumulatedReceipt($tagihanId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $receipt = PaymentReceipt::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('receipt_data')
            ->get()
            ->first(function ($item) {
                return (bool) data_get($item->receipt_data, 'is_accumulated_installment', false);
            });

        if (!$receipt) {
            return redirect()->back()->with('error', 'Kwitansi akumulasi cicilan belum tersedia.');
        }

        $transactions = PaymentTransaction::query()
            ->whereIn('id', data_get($receipt->receipt_data, 'installment_transaction_ids', []))
            ->orderBy('created_at')
            ->get();

        $templateData = $this->getTemplateData();
        $templateData['menus'] = "Pembayaran";
        $templateData['pages'] = "Kwitansi Akumulasi Cicilan";
        $templateData['mahasiswa'] = $mahasiswa;

        $data = [
            'receipt' => $receipt,
            'transaction' => $transactions->last(),
            'transactions' => $transactions,
            'mahasiswa' => $mahasiswa,
            'tagihan' => $tagihan,
            'institution' => [
                'name' => config('app.institution_name', 'Institusi Pendidikan'),
                'address' => config('app.institution_address', 'Alamat Institusi'),
                'phone' => config('app.institution_phone', '021-12345678'),
                'email' => config('app.institution_email', 'info@institusi.ac.id'),
                'website' => config('app.url'),
                'logo' => asset('images/logo.png')
            ],
            'generated_at' => now(),
            'is_accumulated_installment' => true,
        ];

        $receipt->incrementDownloadCount();

        return view('mahasantri.pages.payment-receipt', array_merge($templateData, $data));
    }

    /**
     * Download accumulated installment receipt PDF (per tagihan)
     */
    public function downloadAccumulatedReceipt($tagihanId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $receipt = PaymentReceipt::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('receipt_data')
            ->get()
            ->first(function ($item) {
                return (bool) data_get($item->receipt_data, 'is_accumulated_installment', false);
            });

        if (!$receipt) {
            return redirect()->back()->with('error', 'Kwitansi akumulasi cicilan belum tersedia.');
        }

        $transactions = PaymentTransaction::query()
            ->whereIn('id', data_get($receipt->receipt_data, 'installment_transaction_ids', []))
            ->orderBy('created_at')
            ->get();

        $data = $this->getTemplateData();

        $pdfData = array_merge($data, [
            'transaction' => $transactions->last(),
            'transactions' => $transactions,
            'receipt' => $receipt,
            'mahasiswa' => $mahasiswa,
            'tagihan' => $tagihan,
            'generated_at' => now(),
            'is_accumulated_installment' => true,
            'institution' => [
                'name' => $data['webs']->school_name ?? 'Institusi Pendidikan',
                'address' => $data['webs']->school_address ?? 'Alamat Institusi',
                'phone' => $data['webs']->school_phone ?? '021-12345678',
                'email' => $data['webs']->school_email ?? 'info@institusi.ac.id',
                'website' => config('app.url'),
            ]
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mahasantri.pages.payment-receipt-pdf', $pdfData);
        $pdf->setPaper('A4', 'portrait');

        $receipt->incrementDownloadCount();

        return $pdf->download('kwitansi-akumulasi-' . $receipt->receipt_code . '.pdf');
    }
}
