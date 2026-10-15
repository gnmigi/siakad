@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Detail Pembayaran - {{ config('app.name') }}
@endsection

@section('custom-css')
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        /* Modern Container */
        .modern-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            background: var(--gray-50);
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }

        /* Dark mode support */
        [data-bs-theme="dark"] .modern-container {
            background: var(--gray-900);
        }

        [data-bs-theme="dark"] .modern-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: #f1f5f9;
        }

        /* Header Section */
        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }

        .payment-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        [data-bs-theme="dark"] .payment-title {
            color: #f1f5f9;
        }

        .payment-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        [data-bs-theme="dark"] .payment-subtitle {
            color: var(--gray-400);
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            margin: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-pending {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #92400e;
        }

        .status-processing {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
        }

        .status-verified {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
        }

        .status-failed {
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            color: white;
        }

        /* Detail Cards */
        .detail-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        [data-bs-theme="dark"] .detail-card {
            background: rgba(51, 65, 85, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .detail-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .detail-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: var(--gray-800);
        }

        [data-bs-theme="dark"] .detail-title {
            color: #f1f5f9;
        }

        /* Detail Rows */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        [data-bs-theme="dark"] .detail-label {
            color: var(--gray-400);
        }

        .detail-value {
            font-weight: 600;
            color: var(--gray-800);
        }

        [data-bs-theme="dark"] .detail-value {
            color: #f1f5f9;
        }

        .detail-value.amount {
            color: var(--danger-color);
            font-size: 1.1rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-modern {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            min-width: 140px;
            justify-content: center;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-danger-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
            color: white;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-secondary-modern {
            background: rgba(255, 255, 255, 0.9);
            color: var(--gray-700);
            border: 2px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary-modern:hover {
            transform: translateY(-2px);
            background: white;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        [data-bs-theme="dark"] .btn-secondary-modern {
            background: rgba(51, 65, 85, 0.9);
            color: #f1f5f9;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Payment Instructions */
        .payment-instructions {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }

        [data-bs-theme="dark"] .payment-instructions {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
        }

        /* Copy Button */
        .copy-button {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .copy-button:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-1px);
        }

        .copy-button.copied {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: var(--success-color);
        }

        /* Account Display */
        .account-display {
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin: 0.5rem 0;
            position: relative;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
            word-break: break-all;
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modern-container {
                padding: 1rem;
            }

            .modern-card {
                padding: 1.5rem;
                border-radius: 16px;
            }

            .payment-title {
                font-size: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-modern {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-up {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Success Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #10b981;
            stroke-miterlimit: 10;
            margin: 1rem auto;
            box-shadow: inset 0px 0px 0px #10b981;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }

        .success-checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #10b981;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .success-checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scale {
            0%, 100% {
                transform: none;
            }
            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }

        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #10b981;
            }
        }
    </style>
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="modern-container">
    <div class="modern-card fade-in">
        <!-- Header -->
        <div class="payment-header">
            <h1 class="payment-title">Detail Pembayaran</h1>
            <p class="payment-subtitle">Informasi lengkap transaksi pembayaran Anda</p>
            
            <!-- Status Badge -->
            <div class="text-center">
                @if($transaction->status === 'pending')
                    <div class="status-badge status-pending">
                        <i class="fas fa-clock"></i>
                        Menunggu Pembayaran
                    </div>
                @elseif($transaction->status === 'processing')
                    <div class="status-badge status-processing">
                        <i class="fas fa-spinner fa-spin"></i>
                        Sedang Diproses
                    </div>
                @elseif($transaction->status === 'verified')
                    <div class="status-badge status-verified">
                        <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="success-checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="success-checkmark__check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                        Pembayaran Berhasil
                    </div>
                @elseif($transaction->status === 'failed')
                    <div class="status-badge status-failed">
                        <i class="fas fa-times"></i>
                        Pembayaran Gagal
                    </div>
                @elseif($transaction->status === 'cancelled')
                    <div class="status-badge status-cancelled">
                        <i class="fas fa-ban"></i>
                        Dibatalkan
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Transaction Details -->
            <div class="col-lg-6">
                <div class="detail-card slide-up">
                    <div class="detail-header">
                        <div class="detail-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h3 class="detail-title">Informasi Tagihan</h3>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Kode Transaksi:</span>
                        <span class="detail-value">{{ $transaction->transaction_code ?? $transaction->id }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Tagihan:</span>
                        <span class="detail-value">{{ $transaction->tagihan->name ?? 'Pembayaran Akademik' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Jumlah:</span>
                        <span class="detail-value amount">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Metode Pembayaran:</span>
                        <span class="detail-value">{{ $transaction->paymentMethod->name ?? 'Transfer Manual' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Waktu Dibuat:</span>
                        <span class="detail-value">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="col-lg-6">
                <div class="detail-card slide-up">
                    <div class="detail-header">
                        <div class="detail-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3 class="detail-title">Instruksi Pembayaran</h3>
                    </div>

                    @if($transaction->paymentMethod->type === 'virtual_account')
                        @php
                            $vaNumber = $paymentInfo['virtual_account'] ?? $transaction->virtual_account ?? 'Generating...';
                            $billerCode = $paymentInfo['biller_code'] ?? null;
                            
                            // Enhanced bank name detection logic - consistent with payment-status-modern.blade.php
                            $bankName = 'Bank Virtual Account';
                            $gatewayResponse = $transaction->gateway_response ?? [];
                            
                            // Priority 1: Detect from gateway response va_numbers (most accurate)
                            if (isset($gatewayResponse['va_numbers']) && !empty($gatewayResponse['va_numbers'])) {
                                $vaBank = $gatewayResponse['va_numbers'][0]['bank'] ?? '';
                                switch (strtolower($vaBank)) {
                                    case 'bca':
                                        $bankName = 'Bank Central Asia (BCA)';
                                        break;
                                    case 'bni':
                                        $bankName = 'Bank Negara Indonesia (BNI)';
                                        break;
                                    case 'bri':
                                        $bankName = 'Bank Rakyat Indonesia (BRI)';
                                        break;
                                    case 'cimb':
                                        $bankName = 'CIMB Niaga';
                                        break;
                                    case 'bsi':
                                        $bankName = 'Bank Syariah Indonesia (BSI)';
                                        break;
                                    case 'bjb':
                                        $bankName = 'Bank Jabar Banten (BJB)';
                                        break;
                                }
                            }
                            // Priority 2: Mandiri VA (echannel) detection
                            elseif (isset($gatewayResponse['payment_type']) && $gatewayResponse['payment_type'] === 'echannel' || isset($gatewayResponse['bill_key'])) {
                                $bankName = 'Bank Mandiri';
                            }
                            // Priority 3: Bank account relationship
                            elseif ($transaction->bankAccount) {
                                $bankName = $transaction->bankAccount->bank_name;
                            }
                            // Priority 4: Fallback to payment method name parsing (enhanced)
                            else {
                                $paymentMethodName = strtolower($transaction->paymentMethod->name ?? '');
                                
                                if (strpos($paymentMethodName, 'bca') !== false) {
                                    $bankName = 'Bank Central Asia (BCA)';
                                } elseif (strpos($paymentMethodName, 'mandiri') !== false) {
                                    $bankName = 'Bank Mandiri';
                                } elseif (strpos($paymentMethodName, 'bni') !== false) {
                                    $bankName = 'Bank Negara Indonesia (BNI)';
                                } elseif (strpos($paymentMethodName, 'bri') !== false) {
                                    $bankName = 'Bank Rakyat Indonesia (BRI)';
                                } elseif (strpos($paymentMethodName, 'cimb') !== false) {
                                    $bankName = 'CIMB Niaga';
                                } elseif (strpos($paymentMethodName, 'bsi') !== false || strpos($paymentMethodName, 'syariah') !== false) {
                                    $bankName = 'Bank Syariah Indonesia (BSI)';
                                } elseif (strpos($paymentMethodName, 'bjb') !== false || strpos($paymentMethodName, 'jabar') !== false || strpos($paymentMethodName, 'banten') !== false) {
                                    $bankName = 'Bank Jabar Banten (BJB)';
                                }
                            }
                        @endphp
                        
                        <div class="payment-instructions">
                            <h6><i class="fas fa-university me-2"></i>{{ $bankName }}</h6>
                            
                            @if($vaNumber && $vaNumber !== 'Generating...')
                                <div class="account-display">
                                    <p class="account-number" id="va-number">{{ $vaNumber }}</p>
                                    <button class="copy-button" onclick="copyToClipboard('va-number', this)">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                                
                                @if($billerCode)
                                    <div class="mt-2">
                                        <small class="text-muted"><strong>Biller Code:</strong> {{ $billerCode }}</small>
                                    </div>
                                @endif
                                
                                <small class="text-muted">
                                    @if($bankName === 'Bank Mandiri')
                                        Transfer ke nomor Virtual Account di atas melalui ATM Mandiri, Mandiri Online, atau Livin' by Mandiri
                                    @else
                                        Transfer ke nomor Virtual Account di atas melalui ATM, Mobile Banking, atau Internet Banking
                                    @endif
                                </small>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Nomor Virtual Account sedang diproses. Silakan refresh halaman dalam beberapa saat.
                                    <button type="button" class="btn btn-sm btn-primary ms-2" onclick="window.location.reload()">
                                        <i class="fas fa-refresh"></i> Refresh
                                    </button>
                                </div>
                            @endif
                        </div>

                    @elseif($transaction->paymentMethod->type === 'bank_transfer' && $transaction->bankAccount)
                        <div class="payment-instructions">
                            <h6><i class="fas fa-university me-2"></i>{{ $transaction->bankAccount->bank_name }}</h6>
                            <div class="account-display">
                                <p class="account-number" id="bank-account">{{ $transaction->bankAccount->account_number }}</p>
                                <button class="copy-button" onclick="copyToClipboard('bank-account', this)">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            <p><strong>Atas Nama:</strong> {{ $transaction->bankAccount->account_name }}</p>
                            <small class="text-muted">
                                Transfer manual ke rekening di atas, lalu upload bukti transfer
                            </small>
                        </div>

                    @elseif($transaction->paymentMethod->type === 'e_wallet')
                        <div class="payment-instructions">
                            <h6><i class="fas fa-mobile-alt me-2"></i>{{ $transaction->paymentMethod->name }}</h6>
                            @if(isset($paymentInfo['qr_code']))
                                <div class="text-center">
                                    <img src="{{ $paymentInfo['qr_code'] }}" alt="QR Code" style="max-width: 200px; border-radius: 12px;">
                                    <p class="mt-2">Scan QR Code dengan aplikasi {{ $transaction->paymentMethod->name }}</p>
                                </div>
                            @endif
                            @if(isset($paymentInfo['payment_url']) && $paymentInfo['payment_url'] !== '#')
                                <div class="text-center mt-3">
                                    <a href="{{ $paymentInfo['payment_url'] }}" target="_blank" class="btn-primary-modern">
                                        <i class="fas fa-mobile-alt"></i> Buka {{ $transaction->paymentMethod->name }}
                                    </a>
                                </div>
                            @endif
                        </div>

                    @elseif($transaction->paymentMethod->type === 'cash')
                        <div class="payment-instructions">
                            <h6><i class="fas fa-money-bill me-2"></i>Pembayaran Tunai</h6>
                            <p>Silakan datang ke bagian keuangan untuk melakukan pembayaran tunai dengan membawa:</p>
                            <ul>
                                <li>Kode transaksi: <strong>{{ $transaction->transaction_code }}</strong></li>
                                <li>Kartu identitas mahasiswa</li>
                                <li>Uang tunai sebesar <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                            </ul>
                        </div>

                    @else
                        <div class="payment-instructions">
                            <h6><i class="fas fa-credit-card me-2"></i>Pembayaran Online</h6>
                            <p>Silakan lanjutkan pembayaran sesuai dengan metode yang dipilih.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            @if($transaction->status === 'verified')
                <a href="{{ route('mahasiswa.payment.view-receipt', $transaction->id) }}" class="btn-modern btn-success-modern">
                    <i class="fas fa-eye"></i>
                    Lihat Kwitansi
                </a>
                <a href="{{ route('mahasiswa.payment.receipt', $transaction->id) }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-download"></i>
                    Download PDF
                </a>
            @elseif(in_array($transaction->status, ['pending', 'processing']))
                
                @if($transaction->status === 'pending')
                    <!-- Tombol Lanjutkan Pembayaran untuk status pending -->
                    @if($transaction->paymentMethod->type === 'midtrans_snap')
                        <button type="button" class="btn-modern btn-primary-modern" onclick="openMidtransSnap()">
                            <i class="fas fa-credit-card"></i>
                            Lanjutkan Pembayaran
                        </button>
                    @elseif(in_array($transaction->paymentMethod->type, ['virtual_account', 'e_wallet', 'qris']))
                        <button type="button" class="btn-modern btn-primary-modern" onclick="window.location.reload()">
                            <i class="fas fa-refresh"></i>
                            Refresh Instruksi
                        </button>
                    @elseif($transaction->paymentMethod->type === 'cash')
                        <a href="{{ route('mahasiswa.payment.index') }}" class="btn-modern btn-primary-modern">
                            <i class="fas fa-money-bill"></i>
                            Ke Bagian Keuangan
                        </a>
                    @endif
                @endif
                
                <button type="button" class="btn-modern btn-success-modern" onclick="checkPaymentStatusModern()">
                    <i class="fas fa-sync"></i>
                    Cek Status
                </button>
                
                @if($transaction->paymentMethod->type === 'bank_transfer')
                    <button type="button" class="btn-modern btn-primary-modern" onclick="showUploadModal()">
                        <i class="fas fa-upload"></i>
                        Upload Bukti
                    </button>
                @endif
                
                @if($transaction->status === 'pending')
                    <button type="button" class="btn-modern btn-danger-modern" onclick="cancelPayment()">
                        <i class="fas fa-times"></i>
                        Batalkan
                    </button>
                @endif
            @endif
            
            <!-- Tombol untuk akses ke halaman status pembayaran -->
            <a href="{{ route('mahasiswa.payment.status-modern', $transaction->id) }}" class="btn-modern btn-primary-modern">
                <i class="fas fa-chart-line"></i>
                Status Pembayaran
            </a>
            
            <a href="{{ route('mahasiswa.payment.index') }}" class="btn-modern btn-secondary-modern">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- Upload Modal -->
@if($transaction->paymentMethod->type === 'bank_transfer' && in_array($transaction->status, ['pending', 'processing']))
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mahasiswa.payment.upload-proof', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran *</label>
                        <input type="file" class="form-control" name="proof_file" accept="image/*,application/pdf" required>
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pengirim</label>
                        <input type="text" class="form-control" name="sender_name" value="{{ $mahasiswa->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Rekening Pengirim</label>
                        <input type="text" class="form-control" name="sender_account">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload Bukti</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('custom-js')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <script>
        // Global variables
        window.transactionId = {{ $transaction->id ?? 0 }};
        window.transactionStatus = '{{ $transaction->status ?? "unknown" }}';

        // Copy to clipboard function
        function copyToClipboard(elementId, button) {
            var element = document.getElementById(elementId);
            var text = element.textContent || element.innerText;
            
            // Remove any extra whitespace
            text = text.trim();
            
            // Create temporary textarea
            var textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                
                // Update button appearance
                var originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.classList.add('copied');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Teks berhasil disalin: ' + text,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    button.innerHTML = originalHTML;
                    button.classList.remove('copied');
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyalin teks',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            
            document.body.removeChild(textarea);
        }

        // Check payment status function
        function checkPaymentStatusModern() {
            Swal.fire({
                title: '🔄 Mengecek Status Pembayaran',
                text: 'Sedang menghubungi server...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    fetch(`/mahasiswa/payment/${window.transactionId}/status`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.status === 'verified') {
                                Swal.fire({
                                    title: '🎉 Pembayaran Berhasil!',
                                    html: 'Pembayaran Anda telah berhasil diverifikasi!<br><br>Halaman akan dimuat ulang...',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'ℹ️ Status Pembayaran',
                                    html: `Status: ${data.status}<br>Pesan: ${data.message}`,
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                });
                            }
                        } else {
                            Swal.fire({
                                title: '❌ Gagal Mengecek Status',
                                text: data.message || 'Terjadi kesalahan saat mengecek status pembayaran',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '❌ Kesalahan Jaringan',
                            text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }

        // Cancel payment function
        function cancelPayment() {
            Swal.fire({
                title: 'Batalkan Pembayaran?',
                text: 'Apakah Anda yakin ingin membatalkan pembayaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Membatalkan Pembayaran...',
                        text: 'Sedang memproses pembatalan',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit cancel form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/mahasiswa/payment/${window.transactionId}/cancel`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Show upload modal function
        function showUploadModal() {
            const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
            modal.show();
        }

        // Open Midtrans Snap for pending payments
        function openMidtransSnap() {
            // Get Snap token from server
            fetch(`/mahasiswa/payment/${window.transactionId}/snap-token`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.snap_token) {
                    // Open Midtrans Snap popup
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            Swal.fire({
                                title: '🎉 Pembayaran Berhasil!',
                                text: 'Pembayaran Anda telah berhasil diproses',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            Swal.fire({
                                title: 'Pembayaran Sedang Diproses',
                                text: 'Pembayaran Anda sedang diproses, silakan tunggu konfirmasi',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            Swal.fire({
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan saat memproses pembayaran',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Token Tidak Tersedia',
                        text: data.message || 'Snap token tidak ditemukan. Silakan buat pembayaran baru.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error getting snap token:', error);
                Swal.fire({
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat mengambil token pembayaran. Periksa koneksi internet Anda.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        // Auto-polling for pending transactions
        if (window.transactionStatus === 'pending' || window.transactionStatus === 'processing') {
            console.log('🔄 Starting auto-polling for transaction status...');
            
            // Auto-check every 30 seconds for pending transactions
            const autoCheckInterval = setInterval(function() {
                fetch(`/mahasiswa/payment/${window.transactionId}/status`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'verified') {
                        console.log('✅ Payment verified via auto-check!');
                        clearInterval(autoCheckInterval);
                        
                        // Show success notification
                        Swal.fire({
                            title: '🎉 Pembayaran Berhasil!',
                            html: 'Pembayaran Anda telah berhasil diverifikasi secara otomatis!<br><br>Halaman akan dimuat ulang...',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (data.success && data.status !== window.transactionStatus) {
                        console.log('📊 Status changed from', window.transactionStatus, 'to', data.status);
                        
                        // Update global status
                        window.transactionStatus = data.status;
                        
                        // If status changed to verified, stop polling and reload
                        if (data.status === 'verified') {
                            clearInterval(autoCheckInterval);
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    }
                })
                .catch(error => {
                    console.log('Auto-check error:', error);
                });
            }, 30000); // Check every 30 seconds
        }
    </script>
@endsection
