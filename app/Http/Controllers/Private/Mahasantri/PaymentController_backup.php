<?php

namespace App\Http\Controllers\Private\Mahasantri;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Pengaturan\WebSetting;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        
        // Get unpaid tagihan
        $unpaidTagihan = TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->with(['group'])
            ->orderBy('due_date')
            ->get();

        // Get payment history
        $paymentHistory = $this->paymentService->getPaymentHistory($mahasiswa->id, 10);

        // Get payment statistics
        $stats = [
            'total_tagihan' => TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)->count(),
            'paid_tagihan' => TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)->where('status', 'Sukses')->count(),
            'pending_amount' => $unpaidTagihan->sum('amount'),
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
        
        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        // Check if payment already exists
        $existingPayment = PaymentTransaction::where('tagihan_id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['pending', 'processing', 'paid', 'verified'])
            ->first();

        if ($existingPayment) {
            return redirect()->route('mahasiswa.payment.show', $existingPayment->id)
                ->with('info', 'Pembayaran untuk tagihan ini sudah ada');
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
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan_kuliahs,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();

        $result = $this->paymentService->createPayment(
            $request->tagihan_id,
            $mahasiswa->id,
            $request->payment_method_id,
            $request->bank_account_id
        );

        if ($result['success']) {
            $transaction = $result['transaction'];
            $paymentMethod = $transaction->paymentMethod;
            
            // PENTING: Untuk Midtrans Snap, store token in session and redirect to show page
            if ($paymentMethod->type === 'midtrans_snap') {
                $gatewayResponse = $transaction->gateway_response ?? [];
                
                Log::info('🔍 Checking Midtrans Snap token', [
                    'transaction_code' => $transaction->transaction_code,
                    'gateway_response_exists' => !empty($gatewayResponse),
                    'token_exists' => isset($gatewayResponse['token']),
                    'gateway_response_keys' => array_keys($gatewayResponse)
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
                    
                    return redirect()->back()
                        ->with('error', 'Snap token not received. Terjadi kesalahan saat membuat pembayaran Midtrans. Silakan coba lagi atau hubungi admin.')
                        ->withInput();
                }
            }
            
            // Untuk payment method lain, redirect ke halaman detail
            return redirect()->route('mahasiswa.payment.show', $transaction->id)
                ->with('success', 'Pembayaran berhasil dibuat');
        }

        Log::error('❌ Payment creation failed', [
            'tagihan_id' => $request->tagihan_id,
            'mahasiswa_id' => $mahasiswa->id,
            'payment_method_id' => $request->payment_method_id,
            'error_message' => $result['message']
        ]);

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
                $paymentInfo['virtual_account'] = $transaction->virtual_account 
                    ?? $gatewayResponse['va_numbers'][0]['va_number'] ?? null;
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
                
                // Fallback for simulator - generate dummy QR code
                if (empty($paymentInfo['qr_code']) && !config('midtrans.is_production')) {
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

        if (!$transaction->canBePaid()) {
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
    public function cancel($transactionId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $transaction = PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $result = $this->paymentService->cancelPayment($transactionId, 'Dibatalkan oleh mahasiswa');

        if ($result['success']) {
            return redirect()->route('mahasiswa.payment.index')
                ->with('success', 'Pembayaran berhasil dibatalkan');
        }

        return redirect()->back()->with('error', $result['message']);
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
     * Show custom UI for payment (game-style interface)
     */
    public function customUI($tagihanId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $tagihan = TagihanKuliah::where('id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        // Check if payment already exists
        $existingPayment = PaymentTransaction::where('tagihan_id', $tagihanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['pending', 'processing', 'paid', 'verified'])
            ->first();

        if ($existingPayment) {
            return redirect()->route('mahasiswa.payment.show', $existingPayment->id)
                ->with('info', 'Pembayaran untuk tagihan ini sudah ada');
        }

        // Get available payment methods
        $paymentData = $this->paymentService->getAvailablePaymentMethods($mahasiswa->id);
        
        // Extract payment methods and bank accounts from service response
        $paymentMethods = $paymentData['payment_methods'] ?? [];
        $bankAccounts = $paymentData['bank_accounts'] ?? [];

        // Get template data
        $data = $this->getTemplateData();
        $data['menus'] = "Pembayaran";
        $data['pages'] = "Custom Payment UI";

        return view('mahasantri.pages.payment-custom-ui', $data, compact(
            'mahasiswa',
            'tagihan',
            'paymentMethods',
            'bankAccounts'
        ));
    }

    /**
     * Create payment via AJAX for custom UI
     */
    public function createCustomPayment(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan_kuliahs,id',
            'payment_method' => 'required|string',
            'bank_code' => 'nullable|string',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();
        $tagihan = TagihanKuliah::findOrFail($request->tagihan_id);

        try {
            // Determine payment method ID based on method type
            $paymentMethodId = $this->getPaymentMethodId($request->payment_method);
            $bankAccountId = $this->getBankAccountId($request->bank_code);

            $result = $this->paymentService->createPayment(
                $request->tagihan_id,
                $mahasiswa->id,
                $paymentMethodId,
                $bankAccountId
            );

            if ($result['success']) {
                $transaction = $result['transaction'];
                $gatewayResponse = $transaction->gateway_response ?? [];

                // Prepare response data based on payment method
                $responseData = [
                    'success' => true,
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'payment_type' => $request->payment_method,
                    'amount' => $transaction->total_amount,
                    'status' => $transaction->status,
                ];

                // Add method-specific data
                switch ($request->payment_method) {
                    case 'bca_va':
                    case 'mandiri_va':
                    case 'bni_va':
                    case 'bri_va':
                    case 'permata_va':
                        $responseData['va_number'] = $transaction->virtual_account ?? 
                            ($gatewayResponse['va_numbers'][0]['va_number'] ?? null);
                        $responseData['bank'] = strtoupper(str_replace('_va', '', $request->payment_method));
                        break;

                    case 'gopay':
                        if (isset($gatewayResponse['actions'])) {
                            foreach ($gatewayResponse['actions'] as $action) {
                                if ($action['name'] === 'generate-qr-code') {
                                    $responseData['qr_code'] = $action['url'];
                                }
                                if ($action['name'] === 'deeplink-redirect') {
                                    $responseData['deeplink_url'] = $action['url'];
                                }
                            }
                        }
                        break;

                    case 'shopeepay':
                    case 'ovo':
                    case 'dana':
                        // Similar handling for other e-wallets
                        if (isset($gatewayResponse['actions'])) {
                            foreach ($gatewayResponse['actions'] as $action) {
                                if ($action['name'] === 'generate-qr-code') {
                                    $responseData['qr_code'] = $action['url'];
                                }
                            }
                        }
                        break;
                }

                return response()->json($responseData);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment method ID based on method string
     */
    private function getPaymentMethodId($methodString)
    {
        // Map method strings to payment method IDs
        $methodMap = [
            'bca_va' => 'virtual_account',
            'mandiri_va' => 'virtual_account', 
            'bni_va' => 'virtual_account',
            'bri_va' => 'virtual_account',
            'permata_va' => 'virtual_account',
            'gopay' => 'e_wallet',
            'shopeepay' => 'e_wallet',
            'ovo' => 'e_wallet',
            'dana' => 'e_wallet',
            'linkaja' => 'e_wallet',
        ];

        $methodType = $methodMap[$methodString] ?? 'virtual_account';
        
        // Find payment method by type
        $paymentMethod = \App\Models\Keuangan\PaymentMethod::where('type', $methodType)
            ->where('is_active', true)
            ->first();

        return $paymentMethod ? $paymentMethod->id : 1; // Default to first method
    }

    /**
     * Get bank account ID based on bank code
     */
    private function getBankAccountId($bankCode)
    {
        if (!$bankCode) return null;

        $bankAccount = \App\Models\Keuangan\BankAccount::where('bank_code', strtoupper($bankCode))
            ->where('is_active', true)
            ->first();

        return $bankAccount ? $bankAccount->id : null;
    }

    /**
     * Get Snap token for transaction (AJAX endpoint)
     */
    public function getSnapToken($transactionId)
    {
        try {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            // Validate transaction ownership
            $transaction = PaymentTransaction::where('id', $transactionId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['paymentMethod'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Check if this is a Midtrans Snap transaction
            if ($transaction->paymentMethod->type !== 'midtrans_snap') {
                return response()->json([
                    'success' => false,
                    'message' => 'Not a Midtrans Snap transaction'
                ], 400);
            }

            // Get token from session first
            $snapToken = session('snap_token');
            $sessionTransactionId = session('snap_transaction_id');

            // Verify session token belongs to this transaction
            if ($snapToken && $sessionTransactionId == $transactionId) {
                Log::info('✅ Snap token retrieved from session', [
                    'transaction_id' => $transactionId,
                    'token_length' => strlen($snapToken)
                ]);

                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'transaction_id' => $transactionId,
                    'source' => 'session'
                ]);
            }

            // Fallback: Get token from transaction gateway_response
            $gatewayResponse = $transaction->gateway_response ?? [];
            if (isset($gatewayResponse['token']) && !empty($gatewayResponse['token'])) {
                Log::info('✅ Snap token retrieved from database', [
                    'transaction_id' => $transactionId,
                    'token_length' => strlen($gatewayResponse['token'])
                ]);

                return response()->json([
                    'success' => true,
                    'snap_token' => $gatewayResponse['token'],
                    'transaction_id' => $transactionId,
                    'source' => 'database'
                ]);
            }

            // If no token found, log error and return failure
            Log::error('❌ No Snap token found', [
                'transaction_id' => $transactionId,
                'session_token_exists' => !empty($snapToken),
                'session_transaction_id' => $sessionTransactionId,
                'gateway_response_exists' => !empty($gatewayResponse),
                'gateway_token_exists' => isset($gatewayResponse['token'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Snap token not found. Please try creating the payment again.',
                'error_code' => 'TOKEN_NOT_FOUND'
            ], 404);

        } catch (\Exception $e) {
            Log::error('❌ Get Snap token error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Snap token',
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
}
