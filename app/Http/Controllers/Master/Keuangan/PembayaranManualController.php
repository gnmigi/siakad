<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;
use App\Models\Keuangan\RiwayatPembayaran;
use App\Models\Keuangan\Saldo;
use App\Models\Keuangan\PaymentVerification;
use App\Models\Mahasantri;
use App\Services\PaymentService;
use App\Services\ReceiptGenerationService;
use App\Services\PaymentVerificationService;
use Carbon\Carbon;

class PembayaranManualController extends Controller
{
    protected $paymentService;
    protected $receiptService;
    protected $verificationService;

    public function __construct(
        PaymentService $paymentService,
        ReceiptGenerationService $receiptService,
        PaymentVerificationService $verificationService
    ) {
        $this->paymentService = $paymentService;
        $this->receiptService = $receiptService;
        $this->verificationService = $verificationService;
    }

    /**
     * Display single-page payment interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data['webs'] = \App\Models\Pengaturan\WebSetting::first();
        
        // Ensure webs object has required properties
        if (!$data['webs']) {
            $data['webs'] = (object) [
                'school_apps' => 'SIAKAD',
                'school_name' => 'Sistem Informasi Akademik',
                'school_logo_hori' => asset('logo.png'),
            ];
        }
        
        // Use user prefix like other working controllers
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master Keuangan";
        $data['pages'] = "Pembayaran Manual";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // For single-page interface
        $data['paymentMethods'] = PaymentMethod::where('is_active', true)->get();
        $data['bankAccounts'] = BankAccount::where('is_active', true)->get();
        $data['user'] = $user;

        return view('master.keuangan.pembayaran-manual-single-page', $data);
    }

    /**
     * Display verification queue for manual transfer proofs uploaded by students.
     */
    public function verificationQueue(Request $request)
    {
        $verifications = $this->buildVerificationQueueQuery($request)->paginate(15);

        $statuses = [
            PaymentVerification::STATUS_PENDING => 'Menunggu Verifikasi',
            PaymentVerification::STATUS_APPROVED => 'Disetujui',
            PaymentVerification::STATUS_REJECTED => 'Ditolak',
            PaymentVerification::STATUS_NEED_REVIEW => 'Perlu Review',
        ];

        $user = Auth::user();
        $spref = $user && !empty($user->prefix)
            ? $user->prefix
            : (($user && (int) ($user->raw_type ?? 0) === 2) ? 'finance.' : 'root.');

        return view('master.keuangan.pembayaran-manual-verification-queue', compact('verifications', 'statuses', 'spref'));
    }

    /**
     * AJAX JSON data for verification queue table (no page refresh).
     */
    public function verificationQueueData(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        if ($perPage < 5) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $verifications = $this->buildVerificationQueueQuery($request)->paginate($perPage);

        $items = $verifications->getCollection()->map(function ($verification) {
            $trx = $verification->paymentTransaction;
            $mahasiswa = $trx?->mahasiswa;

            return [
                'id' => $verification->id,
                'verification_code' => $verification->verification_code,
                'status' => $verification->status,
                'status_label' => $this->mapVerificationStatusLabel($verification->status),
                'sender_name' => $verification->sender_name,
                'sender_account' => $verification->sender_account,
                'amount' => (float) ($verification->verified_amount ?? $trx?->total_amount ?? 0),
                'amount_formatted' => 'Rp ' . number_format((float) ($verification->verified_amount ?? $trx?->total_amount ?? 0), 0, ',', '.'),
                'created_at' => optional($verification->created_at)->format('d/m/Y H:i'),
                'proof_url' => $verification->proof_file ? asset('storage/' . ltrim($verification->proof_file, '/')) : null,
                'transaction_id' => $trx?->id,
                'transaction_code' => $trx?->transaction_code,
                'mahasiswa_name' => $mahasiswa?->name,
                'mahasiswa_nim' => $mahasiswa?->numb_nim ?? $mahasiswa?->nim,
                'tagihan_name' => $trx?->tagihan?->group?->name ?? $trx?->tagihan?->desc ?? 'Tagihan Kuliah',
            ];

        })->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $verifications->currentPage(),
                'last_page' => $verifications->lastPage(),
                'per_page' => $verifications->perPage(),
                'total' => $verifications->total(),
                'from' => $verifications->firstItem(),
                'to' => $verifications->lastItem(),
            ],
        ]);
    }

    private function buildVerificationQueueQuery(Request $request)
    {
        $query = PaymentVerification::with([
                'paymentTransaction.mahasiswa',
                'paymentTransaction.tagihan',
                'paymentTransaction.paymentMethod',
                'paymentTransaction.bankAccount',
                'verifiedBy',
            ])
            ->where('verification_type', PaymentVerification::TYPE_MANUAL)
            ->whereHas('paymentTransaction', function ($q) {
                $q->where('is_manual', true);
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('verification_code', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('sender_account', 'like', "%{$search}%")
                  ->orWhereHas('paymentTransaction', function ($tq) use ($search) {
                      $tq->where('transaction_code', 'like', "%{$search}%")
                         ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                             $mq->where('name', 'like', "%{$search}%")
                                ->orWhere('numb_nim', 'like', "%{$search}%");
                         });
                  });
            });
        }

        return $query;
    }

    private function mapVerificationStatusLabel(?string $status): string
    {
        return match ($status) {
            PaymentVerification::STATUS_PENDING => 'Menunggu Verifikasi',
            PaymentVerification::STATUS_APPROVED => 'Disetujui',
            PaymentVerification::STATUS_REJECTED => 'Ditolak',
            PaymentVerification::STATUS_NEED_REVIEW => 'Perlu Review',
            default => ucfirst((string) $status),
        };
    }

    /**
     * Approve manual transfer proof verification.
     */
    public function approveVerification(Request $request, $verificationId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->verificationService->approveVerification(
            $verificationId,
            Auth::id(),
            $request->notes
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Gagal menyetujui verifikasi: ' . $result['message']);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi (ACC).');
    }

    /**
     * Reject manual transfer proof verification.
     */
    public function rejectVerification(Request $request, $verificationId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $result = $this->verificationService->rejectVerification(
            $verificationId,
            $request->reason,
            Auth::id()
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Gagal menolak verifikasi: ' . $result['message']);
        }

        return redirect()->back()->with('success', 'Verifikasi ditolak. Status pembayaran menjadi gagal.');
    }

    /**
     * Bulk approve pending verifications.
     */
    public function approveVerificationBulk(Request $request)
    {
        $request->validate([
            'verification_ids' => 'required|array|min:1',
            'verification_ids.*' => 'integer|exists:payment_verifications,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->verification_ids as $verificationId) {
            $verification = PaymentVerification::find($verificationId);

            if (!$verification || $verification->status !== PaymentVerification::STATUS_PENDING) {
                $failCount++;
                continue;
            }

            $result = $this->verificationService->approveVerification(
                $verificationId,
                Auth::id(),
                $request->notes
            );

            if (!empty($result['success'])) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount === 0) {
            return redirect()->back()->with('error', 'Tidak ada verifikasi yang berhasil di-ACC. Pastikan item yang dipilih masih pending.');
        }

        $message = "Berhasil ACC {$successCount} verifikasi.";
        if ($failCount > 0) {
            $message .= " {$failCount} item gagal/dilewati.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Bulk reject pending verifications.
     */
    public function rejectVerificationBulk(Request $request)
    {
        $request->validate([
            'verification_ids' => 'required|array|min:1',
            'verification_ids.*' => 'integer|exists:payment_verifications,id',
            'reason' => 'required|string|max:1000',
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->verification_ids as $verificationId) {
            $verification = PaymentVerification::find($verificationId);

            if (!$verification || $verification->status !== PaymentVerification::STATUS_PENDING) {
                $failCount++;
                continue;
            }

            $result = $this->verificationService->rejectVerification(
                $verificationId,
                $request->reason,
                Auth::id()
            );

            if (!empty($result['success'])) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount === 0) {
            return redirect()->back()->with('error', 'Tidak ada verifikasi yang berhasil ditolak. Pastikan item yang dipilih masih pending.');
        }

        $message = "Berhasil tolak {$successCount} verifikasi.";
        if ($failCount > 0) {
            $message .= " {$failCount} item gagal/dilewati.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display payment history
     */
    public function history(Request $request)
    {
        $query = PaymentTransaction::with(['mahasiswa', 'tagihan', 'paymentMethod', 'bankAccount', 'createdBy'])
            ->where('is_manual', true)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method_id', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15);

        // Get filter options
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $statuses = [
            PaymentTransaction::STATUS_PENDING => 'Menunggu',
            PaymentTransaction::STATUS_PAID => 'Dibayar',
            PaymentTransaction::STATUS_VERIFIED => 'Terverifikasi',
            PaymentTransaction::STATUS_FAILED => 'Gagal',
            PaymentTransaction::STATUS_CANCELLED => 'Dibatalkan'
        ];

        return view('master.keuangan.pembayaran-manual-index', compact(
            'payments', 'paymentMethods', 'statuses'
        ));
    }

    /**
     * Show the form for creating a new manual payment
     */
    public function create(Request $request)
    {
        $mahasiswa = null;
        $tagihans = collect();

        // If mahasiswa_id is provided, load student and their bills
        if ($request->filled('mahasiswa_id')) {
            $mahasiswa = Mahasantri::with(['programStudi', 'tahunAkademik'])->find($request->mahasiswa_id);
            
            if ($mahasiswa) {
                $tagihans = TagihanKuliah::with(['group', 'tahunAkademik'])
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('status', TagihanKuliah::PAYMENT_PENDING)
                    ->orderBy('due_date', 'asc')
                    ->get();
            }
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('master.keuangan.pembayaran-manual-create', compact(
            'mahasiswa', 'tagihans', 'paymentMethods', 'bankAccounts'
        ));
    }

    /**
     * Store a newly created manual payment - FIXED VERSION
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'mahasiswa_id' => 'required|exists:mahasiswas,id',
                'tagihan_ids' => 'required|array|min:1',
                'tagihan_ids.*' => 'exists:tagihan_kuliahs,id',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'bank_account_id' => 'nullable|exists:bank_accounts,id',
                'paid_amount' => 'required|string|min:1',
                'paid_amount_raw' => 'nullable|numeric|min:1',
                'payment_date' => 'required|date',
                'payment_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            throw $e;
        }

        // Use database transaction with proper locking
        DB::beginTransaction();
        
        try {
            $mahasiswa = Mahasantri::findOrFail($request->mahasiswa_id);
            
            // Use raw amount if available, otherwise parse the formatted amount
            $paidAmount = $request->filled('paid_amount_raw') ? 
                $request->paid_amount_raw : 
                (float) preg_replace('/[^\d]/', '', $request->paid_amount);

            // FIXED: Check for duplicate payments first
            if ($this->checkForDuplicatePayment($mahasiswa->id, $request->tagihan_ids, $paidAmount)) {
                throw new \Exception('Pembayaran untuk tagihan ini sedang diproses. Silakan tunggu beberapa saat.');
            }
            
            // FIXED: Lock tagihans to prevent race conditions
            $tagihans = TagihanKuliah::whereIn('id', $request->tagihan_ids)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', [
                    TagihanKuliah::PAYMENT_PENDING, 
                    'Pending', 'pending', 'PENDING', 
                    'Belum Bayar', 'belum_bayar',
                    'Cicilan', 'cicilan', 'CICILAN'
                ])
                ->lockForUpdate() // CRITICAL: Prevent concurrent modifications
                ->get();

            if ($tagihans->isEmpty()) {
                throw new \Exception('Tidak ada tagihan yang valid untuk dibayar. Pastikan tagihan belum lunas.');
            }

            // Parse date and set time to current server time to avoid 00:00:00 timestamp
            $paymentDateTime = Carbon::parse($request->payment_date)->setTimeFrom(now());

            // Handle payment proof upload
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'manual_payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $paymentProofPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // FIXED: Process payments more efficiently - one transaction per tagihan
            $transactions = [];
            $remainingAmount = $paidAmount;

            foreach ($tagihans as $tagihan) {
                if ($remainingAmount <= 0) break;

                // Calculate remaining amount for this tagihan
                $tagihanRemainingAmount = $tagihan->remaining_amount ?? $tagihan->amount;
                $amountForThisTagihan = min($remainingAmount, $tagihanRemainingAmount);
                
                // FIXED: Create single transaction per tagihan
                $transaction = PaymentTransaction::create([
                    'transaction_code' => PaymentTransaction::generateTransactionCode(),
                    'invoice_number' => PaymentTransaction::generateInvoiceNumber(),
                    'mahasiswa_id' => $mahasiswa->id,
                    'tagihan_id' => $tagihan->id,
                    'payment_method_id' => $request->payment_method_id,
                    'bank_account_id' => $request->bank_account_id,
                    'amount' => $tagihan->amount,
                    'fee_amount' => 0,
                    'total_amount' => $tagihan->amount,
                    'paid_amount' => $amountForThisTagihan,
                    'status' => $amountForThisTagihan >= $tagihanRemainingAmount ? 'verified' : 'paid',
                    'gateway_reference' => $request->payment_reference,
                    'payment_proof' => $paymentProofPath,
                    'notes' => $request->notes,
                    'paid_at' => $paymentDateTime,
                    'verified_at' => $amountForThisTagihan >= $tagihanRemainingAmount ? now() : null,
                    'verified_by' => $amountForThisTagihan >= $tagihanRemainingAmount ? Auth::id() : null,
                    'created_by' => Auth::id(),
                    'is_manual' => true,
                    'metadata' => [
                        'manual_entry' => true,
                        'created_by_admin' => Auth::user()->name,
                        'payment_date' => $request->payment_date,
                        'original_amount' => $paidAmount,
                        'payment_reference' => $request->payment_reference,
                        'gateway' => 'manual',
                        'is_installment' => $amountForThisTagihan < $tagihanRemainingAmount
                    ]
                ]);

                // FIXED: Update tagihan properly
                try {
                    if ($amountForThisTagihan >= $tagihanRemainingAmount) {
                        // Full payment - mark as completed
                        $tagihan->update([
                            'status' => TagihanKuliah::PAYMENT_SUKSES,
                            'paid_amount' => $tagihan->amount,
                            'remaining_amount' => 0,
                            'paid_at' => $paymentDateTime,
                            'is_installment' => false
                        ]);
                    } else {
                        // Partial payment - use installment method
                        $tagihan->addInstallmentPayment(
                            $amountForThisTagihan, 
                            $paymentDateTime,
                            'Pembayaran manual oleh admin: ' . Auth::user()->name
                        );
                    }
                    
                    Log::info('Tagihan updated successfully', [
                        'tagihan_id' => $tagihan->id,
                        'payment_amount' => $amountForThisTagihan,
                        'new_status' => $tagihan->fresh()->status,
                        'remaining_amount' => $tagihan->fresh()->remaining_amount
                    ]);
                    
                } catch (\Exception $updateError) {
                    Log::error('Failed to update tagihan', [
                        'tagihan_id' => $tagihan->id,
                        'error' => $updateError->getMessage()
                    ]);
                    throw $updateError; // Re-throw to trigger rollback
                }

                // FIXED: Create single payment history record
                RiwayatPembayaran::create([
                    'code' => 'RWY-' . time() . '-' . $transaction->id,
                    'payment_transaction_id' => $transaction->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'tagihan_id' => $tagihan->id,
                    'amount' => $amountForThisTagihan,
                    'payment_method' => $transaction->paymentMethod->name ?? 'Manual',
                    'paid_at' => $paymentDateTime,
                    'status' => $transaction->status,
                    'reference_number' => $transaction->transaction_code,
                    'notes' => $amountForThisTagihan >= $tagihanRemainingAmount ? 
                        'Pembayaran lunas oleh admin: ' . Auth::user()->name :
                        'Pembayaran cicilan oleh admin: ' . Auth::user()->name,
                    'created_by' => Auth::id()
                ]);

                // FIXED: Create single saldo entry
                Saldo::create([
                    'code' => 'SAL-' . \Illuminate\Support\Str::random(8),
                    'type' => 'Pemasukan',
                    'status' => 'Sukses',
                    'amount' => $amountForThisTagihan,
                    'transaction_code' => $transaction->transaction_code,
                    'source' => 'Pembayaran Mahasiswa - ' . $mahasiswa->name . ' (' . ($mahasiswa->nim ?? $mahasiswa->numb_nim) . ')',
                    'desc' => 'Pembayaran ' . ($amountForThisTagihan >= $tagihanRemainingAmount ? 'lunas' : 'cicilan') . 
                             ' untuk tagihan: ' . ($tagihan->desc ?? 'Tagihan Kuliah') . 
                             ' - Metode: ' . ($transaction->paymentMethod->name ?? 'Manual'),
                    'tagihan_id' => $tagihan->id,
                    'created_by' => Auth::id(),
                    'created_at' => $paymentDateTime,
                    'updated_at' => $paymentDateTime
                ]);

                $transactions[] = $transaction;
                $remainingAmount -= $amountForThisTagihan;
            }

            // FIXED: Commit transaction only after all operations succeed
            DB::commit();

            // Generate receipts for verified transactions (outside transaction)
            foreach ($transactions as $transaction) {
                if ($transaction->status === 'verified') {
                    try {
                        $this->receiptService->generateReceipt($transaction);
                    } catch (\Exception $e) {
                        Log::error('Failed to generate receipt: ' . $e->getMessage());
                        // Don't fail the entire process for receipt generation
                    }
                }
            }

            Log::info('Manual payment processed successfully', [
                'mahasiswa_id' => $mahasiswa->id,
                'transactions_created' => count($transactions),
                'total_amount' => $paidAmount
            ]);

            // Return success response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran manual berhasil diproses untuk ' . count($transactions) . ' tagihan.',
                    'transactions' => count($transactions),
                    'receipt_url' => $transactions[0]->receipts->isNotEmpty() ? 
                        route('keuangan.pembayaran-manual-download-receipt', $transactions[0]->id) : null
                ]);
            }

            return redirect()->route('keuangan.pembayaran-manual-index')
                ->with('success', 'Pembayaran manual berhasil diproses untuk ' . count($transactions) . ' tagihan.');

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Manual payment failed', [
                'mahasiswa_id' => $request->mahasiswa_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified manual payment
     */
    public function show($id)
    {
        $payment = PaymentTransaction::with([
            'mahasiswa.programStudi', 
            'tagihan.group', 
            'paymentMethod', 
            'bankAccount',
            'createdBy',
            'verifiedBy',
            'receipts',
            'riwayatPembayaran'
        ])->where('is_manual', true)->findOrFail($id);

        return view('master.keuangan.pembayaran-manual-show', compact('payment'));
    }

    /**
     * Show the form for editing manual payment
     */
    public function edit($id)
    {
        $payment = PaymentTransaction::with(['mahasiswa', 'tagihan', 'paymentMethod', 'bankAccount'])
            ->where('is_manual', true)
            ->findOrFail($id);

        // Only allow editing of pending or paid payments
        if (!in_array($payment->status, [PaymentTransaction::STATUS_PENDING, PaymentTransaction::STATUS_PAID])) {
            return redirect()->route('keuangan.pembayaran-manual-show', $payment->id)
                ->with('error', 'Pembayaran dengan status ' . $payment->status . ' tidak dapat diedit.');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('master.keuangan.pembayaran-manual-edit', compact(
            'payment', 'paymentMethods', 'bankAccounts'
        ));
    }

    /**
     * Update the specified manual payment
     */
    public function update(Request $request, $id)
    {
        $payment = PaymentTransaction::where('is_manual', true)->findOrFail($id);

        // Only allow editing of pending or paid payments
        if (!in_array($payment->status, [PaymentTransaction::STATUS_PENDING, PaymentTransaction::STATUS_PAID])) {
            return redirect()->route('keuangan.pembayaran-manual-show', $payment->id)
                ->with('error', 'Pembayaran dengan status ' . $payment->status . ' tidak dapat diedit.');
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'paid_amount' => 'required|numeric|min:1|max:' . $payment->total_amount,
            'payment_date' => 'required|date',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        DB::beginTransaction();

        try {
            // Handle payment proof upload
            $paymentProofPath = $payment->payment_proof;
            if ($request->hasFile('payment_proof')) {
                // Delete old file if exists
                if ($paymentProofPath && Storage::disk('public')->exists($paymentProofPath)) {
                    Storage::disk('public')->delete($paymentProofPath);
                }

                $file = $request->file('payment_proof');
                $filename = 'manual_payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $paymentProofPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // Update payment transaction
            $payment->update([
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'paid_amount' => $request->paid_amount,
                'gateway_reference' => $request->payment_reference,
                'payment_proof' => $paymentProofPath,
                'notes' => $request->notes,
                'paid_at' => Carbon::parse($request->payment_date),
                'status' => $request->paid_amount >= $payment->total_amount ? 
                    'verified' : 'paid', // Use string values instead of constants
                'verified_at' => $request->paid_amount >= $payment->total_amount ? now() : null,
                'verified_by' => $request->paid_amount >= $payment->total_amount ? Auth::id() : null,
            ]);

            // Update tagihan status
            if ($payment->tagihan) {
                $payment->tagihan->update([
                    'status' => $request->paid_amount >= $payment->total_amount ? 
                        TagihanKuliah::PAYMENT_SUKSES : TagihanKuliah::PAYMENT_PENDING,
                    'paid_at' => $request->paid_amount >= $payment->total_amount ? 
                        Carbon::parse($request->payment_date) : null
                ]);
            }

            // Update payment history
            if ($payment->riwayatPembayaran) {
                $payment->riwayatPembayaran->update([
                    'amount' => $request->paid_amount,
                    'payment_method' => $payment->paymentMethod->name ?? 'Manual',
                    'paid_at' => Carbon::parse($request->payment_date), // Use paid_at instead of payment_date
                    'status' => $payment->status,
                    'notes' => 'Pembayaran manual diupdate oleh admin: ' . Auth::user()->name,
                    'updated_by' => Auth::id()
                ]);
            }

            DB::commit();

            return redirect()->route('keuangan.pembayaran-manual-show', $payment->id)
                ->with('success', 'Pembayaran manual berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal mengupdate pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified manual payment
     */
    public function destroy($id)
    {
        $payment = PaymentTransaction::where('is_manual', true)->findOrFail($id);

        // Only allow deletion of pending payments
        if ($payment->status !== PaymentTransaction::STATUS_PENDING) {
            return redirect()->route('keuangan.pembayaran-manual-index')
                ->with('error', 'Hanya pembayaran dengan status pending yang dapat dihapus.');
        }

        DB::beginTransaction();

        try {
            // Delete payment proof file if exists
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }

            // Reset tagihan status
            if ($payment->tagihan) {
                $payment->tagihan->update([
                    'status' => TagihanKuliah::PAYMENT_PENDING,
                    'paid_at' => null
                ]);
            }

            // Delete related records
            $payment->riwayatPembayaran()->delete();
            $payment->receipts()->delete();
            
            // Soft delete the payment
            $payment->delete();

            DB::commit();

            return redirect()->route('keuangan.pembayaran-manual-index')
                ->with('success', 'Pembayaran manual berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('keuangan.pembayaran-manual-index')
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Search for students (AJAX) - FIXED VERSION
     */
    public function searchMahasiswa(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            // Build query for active students
            $query = Mahasantri::with(['prodi', 'programStudi'])
                ->where('type', 1); // Only active students
            
            // Apply search filters if search term provided
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('numb_nim', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }
            
            $mahasiswa = $query->limit(20)->get();

            return response()->json([
                'success' => true,
                'results' => $mahasiswa->map(function ($m) {
                    // Use prodi relationship, fallback to programStudi if needed
                    $prodiName = $m->prodi->name ?? $m->programStudi->name ?? 'N/A';
                    
                    return [
                        'id' => $m->id,
                        'text' => ($m->numb_nim ?? $m->id) . ' - ' . $m->name,
                        'nim' => $m->numb_nim ?? $m->id, // Map numb_nim to nim for frontend
                        'name' => $m->name,
                        'program_studi' => $prodiName
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in searchMahasiswa: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'results' => [],
                'message' => 'Terjadi kesalahan saat mencari mahasiswa'
            ], 500);
        }
    }

    /**
     * Get student bills (AJAX)
     */
    public function getMahasiswaTagihan(Request $request, $mahasiswaId)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'status'); // default sort by status
        $sortOrder = $request->get('sort_order', 'asc');

        // Get ALL tagihans for this student (both paid and unpaid) with pagination
        $query = TagihanKuliah::with(['tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswaId);

        // Apply sorting - prioritize unpaid bills first
        if ($sortBy === 'status') {
            // Custom sorting: unpaid first, then paid
            $query->orderByRaw("CASE 
                WHEN status IN ('Sukses', 'sukses', 'SUKSES') THEN 1 
                ELSE 0 
            END ASC")
            ->orderBy('due_date', 'asc'); // Then by due date
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tagihans = $query->paginate($perPage, ['*'], 'page', $page);
            
        // Log all tagihan statuses for debugging
        Log::info('Paginated tagihans for mahasiswa', [
            'mahasiswa_id' => $mahasiswaId,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $tagihans->total(),
            'current_page' => $tagihans->currentPage(),
            'last_page' => $tagihans->lastPage()
        ]);

        return response()->json([
            'tagihans' => $tagihans->map(function ($tagihan) {
                $isPaid = in_array($tagihan->status, ['Sukses', 'sukses', 'SUKSES']);
                $isPending = in_array($tagihan->status, ['Pending', 'pending', 'PENDING']);
                $isInstallment = $tagihan->status === 'Cicilan' || $tagihan->isPartiallyPaid();
                
                // Calculate remaining amount for display
                $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                
                return [
                    'id' => $tagihan->id,
                    'name' => $tagihan->desc ?? 'Tagihan',
                    'description' => $tagihan->desc ?? '',
                    'amount' => $tagihan->amount,
                    'paid_amount' => $tagihan->paid_amount ?? 0,
                    'remaining_amount' => $remainingAmount,
                    'formatted_amount' => 'Rp ' . number_format($tagihan->amount, 0, ',', '.'),
                    'formatted_paid_amount' => 'Rp ' . number_format($tagihan->paid_amount ?? 0, 0, ',', '.'),
                    'formatted_remaining_amount' => 'Rp ' . number_format($remainingAmount, 0, ',', '.'),
                    'due_date' => $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('d/m/Y') : '-',
                    'paid_date' => $tagihan->paid_at ? \Carbon\Carbon::parse($tagihan->paid_at)->format('d/m/Y') : null,
                    'is_overdue' => $tagihan->due_date && !$isPaid ? \Carbon\Carbon::parse($tagihan->due_date)->isPast() : false,
                    'is_paid' => $isPaid,
                    'is_pending' => $isPending,
                    'is_installment' => $isInstallment,
                    'installment_count' => $tagihan->installment_payments_count ?? 0,
                    'payment_percentage' => $tagihan->amount > 0 ? round((($tagihan->paid_amount ?? 0) / $tagihan->amount) * 100, 1) : 0,
                    'status' => $tagihan->status,
                    'category' => 'SPP',
                    'bill_number' => $tagihan->code ?? '-'
                ];
            }),
            'pagination' => [
                'current_page' => $tagihans->currentPage(),
                'last_page' => $tagihans->lastPage(),
                'per_page' => $tagihans->perPage(),
                'total' => $tagihans->total(),
                'from' => $tagihans->firstItem(),
                'to' => $tagihans->lastItem(),
                'has_more_pages' => $tagihans->hasMorePages(),
                'prev_page_url' => $tagihans->previousPageUrl(),
                'next_page_url' => $tagihans->nextPageUrl()
            ]
        ]);
    }

    /**
     * Download payment receipt
     */
    public function downloadReceipt($id)
    {
        $payment = PaymentTransaction::with(['mahasiswa', 'tagihan', 'receipts'])
            ->where('is_manual', true)
            ->findOrFail($id);

        if ($payment->receipts->isEmpty()) {
            // Generate receipt if not exists
            try {
                $this->receiptService->generateReceipt($payment);
                $payment->refresh(); // Reload to get the new receipt
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menggenerate receipt: ' . $e->getMessage());
            }
        }

        if ($payment->receipts->isNotEmpty()) {
            return $this->receiptService->downloadReceipt($payment->receipts->first());
        }

        return redirect()->back()->with('error', 'Receipt tidak tersedia.');
    }

    /**
     * FIXED: Check for duplicate payment processing to prevent race conditions
     */
    private function checkForDuplicatePayment($mahasiswaId, $tagihanIds, $amount)
    {
        return PaymentTransaction::where('mahasiswa_id', $mahasiswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->where('paid_amount', $amount)
            ->where('created_at', '>', now()->subMinutes(5)) // Check for recent transactions
            ->whereIn('status', ['pending', 'paid', 'verified']) // Active statuses
            ->exists();
    }
}
