@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        :root {
            --primary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
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
        .status-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            background: var(--gray-50);
        }

        /* Status Card */
        .status-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        /* Dark mode support */
        [data-bs-theme="dark"] .status-container {
            background: var(--gray-900);
        }

        [data-bs-theme="dark"] .status-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: white;
        }

        /* Status Header */
        .status-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            animation: pulse 2s infinite;
        }

        .status-icon.pending {
            background: linear-gradient(135deg, var(--warning-color), #f6ad55);
            color: white;
        }

        .status-icon.verified {
            background: linear-gradient(135deg, var(--success-color), #68d391);
            color: white;
        }

        .status-icon.failed {
            background: linear-gradient(135deg, var(--danger-color), #fc8181);
            color: white;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .status-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .status-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        [data-bs-theme="dark"] .status-subtitle {
            color: var(--gray-400);
        }

        /* Payment Details */
        .payment-details {
            background: var(--gray-50);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        [data-bs-theme="dark"] .payment-details {
            background: var(--gray-700);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        [data-bs-theme="dark"] .detail-row {
            border-bottom-color: var(--gray-600);
        }

        .detail-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .detail-value {
            font-weight: 600;
            text-align: right;
        }

        [data-bs-theme="dark"] .detail-label {
            color: var(--gray-400);
        }

        /* Virtual Account Display */
        .va-number {
            background: linear-gradient(135deg, var(--primary-color), #2563eb) !important;
            color: white !important;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin: 1rem 0;
        }

        .va-number-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
            color: white !important; /* Ensure white text on blue background */
        }

        /* Ensure VA number is always visible in this context - force blue background */
        .va-number .va-number-text {
            color: white !important; /* White text on blue gradient background */
        }

        .va-bank {
            color: white !important; /* Ensure bank name is also white */
        }

        /* Force blue background in light mode */
        :not([data-bs-theme="dark"]) .va-number {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            color: white !important;
        }

        :not([data-bs-theme="dark"]) .va-number .va-number-text {
            color: white !important;
        }

        :not([data-bs-theme="dark"]) .va-number .va-bank {
            color: white !important;
        }

        /* Dark mode support for VA number */
        [data-bs-theme="dark"] .va-number {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .va-number .va-number-text {
            color: white !important; /* Keep white text in dark mode too */
        }

        [data-bs-theme="dark"] .va-number .va-bank {
            color: white !important;
        }

        .va-bank {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* QR Code Display */
        .qr-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            margin: 1rem 0;
        }

        [data-bs-theme="dark"] .qr-container {
            background: var(--gray-700);
            color: white;
        }

        .qr-code {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            background: white;
            padding: 0.5rem;
        }

        /* Instructions */
        .instructions {
            background: var(--info-color);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 1rem 0;
        }

        .instructions h6 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: white !important;
        }

        .instructions ol {
            margin: 0;
            padding-left: 1.5rem;
        }

        .instructions li {
            margin-bottom: 0.5rem;
            color: white !important;
        }

        /* Ensure all text in instructions is white */
        .instructions * {
            color: white !important;
        }

        /* Dark mode support for instructions */
        [data-bs-theme="dark"] .instructions {
            background: var(--info-color);
            color: white;
        }

        [data-bs-theme="dark"] .instructions h6,
        [data-bs-theme="dark"] .instructions li,
        [data-bs-theme="dark"] .instructions * {
            color: white !important;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .action-btn {
            flex: 1;
            min-width: 200px;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            color: white !important;
            border: 3px solid #1d4ed8 !important;
            font-weight: 700 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
        }

        .action-btn.primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            color: white !important;
            border-color: #1e40af !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5) !important;
        }

        .action-btn.primary:focus,
        .action-btn.primary:active {
            background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
            color: white !important;
            border-color: #1e3a8a !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
        }

        .action-btn.success {
            background: linear-gradient(135deg, var(--success-color), #68d391) !important;
            color: white !important;
            border: 2px solid var(--success-color) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
        }

        .action-btn.success:hover {
            background: linear-gradient(135deg, #059669, #047857) !important;
            color: white !important;
            border-color: #059669 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0) !important;
            color: #1e293b !important;
            border: 2px solid #64748b !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.8) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .action-btn.secondary:hover {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1) !important;
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2) !important;
        }

        .action-btn.secondary:focus,
        .action-btn.secondary:active {
            background: linear-gradient(135deg, #cbd5e1, #94a3b8) !important;
            color: #1e293b !important;
            border-color: #475569 !important;
            outline: none !important;
        }

        [data-bs-theme="dark"] .action-btn.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
            color: white !important;
            border: 2px solid var(--primary-color) !important;
        }

        [data-bs-theme="dark"] .action-btn.success {
            background: linear-gradient(135deg, var(--success-color), #68d391) !important;
            color: white !important;
            border: 2px solid var(--success-color) !important;
        }

        [data-bs-theme="dark"] .action-btn.secondary {
            background: rgba(51, 65, 85, 0.95) !important;
            color: #f1f5f9 !important;
            border: 2px solid var(--gray-500) !important;
        }

        [data-bs-theme="dark"] .action-btn.secondary:hover {
            background: var(--gray-600) !important;
            color: white !important;
            border-color: var(--gray-400) !important;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge.pending {
            background: #fbbf24;
            color: #1f2937;
            border: 1px solid #f59e0b;
            font-weight: 700;
            text-shadow: none;
        }
        
        /* Dark mode support for status badges */
        [data-bs-theme="dark"] .status-badge.pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.4);
        }

        .status-badge.verified {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success-color);
        }

        .status-badge.failed {
            background: rgba(245, 101, 101, 0.1);
            color: var(--danger-color);
        }

        /* Loading Animation */
        .loading-dots {
            display: inline-flex;
            gap: 4px;
        }

        .loading-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: loading 1.4s infinite ease-in-out both;
        }

        .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes loading {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* Loading Spinner for buttons */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Copy Button */
        .copy-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .status-container {
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                min-width: auto;
            }

            .va-number-text {
                font-size: 1.2rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('content')
    <div class="status-container">
        <!-- Status Header -->
        <div class="status-card fade-in">
            <div class="status-header">
                <div class="status-icon {{ $transaction->status }}" id="status-icon">
                    @if($transaction->status === 'verified')
                        ✅
                    @elseif($transaction->status === 'failed')
                        ❌
                    @else
                        ⏳
                    @endif
                </div>
                <h1 class="status-title" id="status-title">
                    @if($transaction->status === 'verified')
                        Pembayaran Berhasil!
                    @elseif($transaction->status === 'failed')
                        Pembayaran Gagal
                    @else
                        Menunggu Pembayaran
                    @endif
                </h1>
                <p class="status-subtitle" id="status-subtitle">
                    @if($transaction->status === 'verified')
                        Terima kasih! Pembayaran Anda telah berhasil diverifikasi.
                    @elseif($transaction->status === 'failed')
                        Pembayaran tidak dapat diproses. Silakan coba lagi.
                    @else
                        Silakan lanjutkan pembayaran sesuai instruksi di bawah ini.
                    @endif
                </p>
                <div class="status-badge {{ $transaction->status }}" id="status-badge">
                    <span id="status-text">{{ ucfirst($transaction->status) }}</span>
                    @if($transaction->status === 'pending')
                        <div class="loading-dots">
                            <div class="loading-dot"></div>
                            <div class="loading-dot"></div>
                            <div class="loading-dot"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="status-card fade-in">
            <h5 class="fw-bold mb-3">📋 Detail Pembayaran</h5>
            <div class="payment-details">
                <div class="detail-row">
                    <span class="detail-label">Kode Transaksi</span>
                    <span class="detail-value">{{ $transaction->transaction_code }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tagihan</span>
                    <span class="detail-value">{{ $transaction->tagihan->desc ?? 'Pembayaran Akademik' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah</span>
                    <span class="detail-value text-danger fw-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran</span>
                    <span class="detail-value">{{ $transaction->paymentMethod->name ?? 'Unknown' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu Dibuat</span>
                    <span class="detail-value">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($transaction->status === 'verified' && $transaction->verified_at)
                <div class="detail-row">
                    <span class="detail-label">Waktu Verifikasi</span>
                    <span class="detail-value">{{ $transaction->verified_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Instructions -->
        @if($transaction->status !== 'verified' && $transaction->status !== 'failed')
        <div class="status-card fade-in" id="payment-instructions">
            <h5 class="fw-bold mb-3">💳 Instruksi Pembayaran</h5>
            
            @php
                $gatewayResponse = $transaction->gateway_response ?? [];
                $paymentMethod = $gatewayResponse['payment_type'] ?? 'unknown';
            @endphp

            @if(str_contains($paymentMethod, 'va') || isset($gatewayResponse['va_numbers']) || isset($gatewayResponse['bill_key']) || $paymentMethod === 'echannel')
                <!-- Virtual Account -->
                @php
                    // Handle different VA types - prioritize Mandiri VA (echannel)
                    $vaNumber = null;
                    $billerCode = null;
                    
                    if (isset($gatewayResponse['bill_key'])) {
                        // Mandiri VA (echannel) uses bill_key
                        $vaNumber = $gatewayResponse['bill_key'];
                        $billerCode = $gatewayResponse['biller_code'] ?? null;
                    } elseif (isset($gatewayResponse['va_numbers']) && !empty($gatewayResponse['va_numbers'])) {
                        // Other VA banks use va_numbers
                        $vaNumber = $gatewayResponse['va_numbers'][0]['va_number'];
                    } elseif (isset($gatewayResponse['permata_va_number'])) {
                        // Permata VA specifically
                        $vaNumber = $gatewayResponse['permata_va_number'];
                    }
                    
                    // Determine bank name based on actual VA response and payment method
                    $bankName = 'Bank Virtual Account';
                    
                    // First, check if we can determine from VA number pattern or response
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
                    } elseif ($paymentMethod === 'echannel' || isset($gatewayResponse['bill_key'])) {
                        $bankName = 'Bank Mandiri';
                    } else {
                        // Fallback: determine from payment method in transaction
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
                
                @if($vaNumber)
                <div class="va-number">
                    <div class="va-bank">{{ $bankName }} Virtual Account</div>
                    <div class="va-number-text" id="va-number">{{ $vaNumber }}</div>
                    <button class="copy-btn" onclick="copyToClipboard('{{ $vaNumber }}')">
                        📋 Salin Nomor VA
                    </button>
                    @if($billerCode)
                        <div style="margin-top: 0.5rem; font-size: 0.9rem; opacity: 0.8;">
                            <strong>Biller Code:</strong> {{ $billerCode }}
                        </div>
                    @endif
                </div>
                @else
                <div class="alert alert-info" style="background: rgba(66, 153, 225, 0.1); border: 1px solid rgba(66, 153, 225, 0.3); border-radius: 8px; padding: 1rem; margin: 1rem 0;">
                    <i class="fas fa-info-circle"></i>
                    Nomor Virtual Account sedang diproses. Silakan refresh halaman dalam beberapa saat.
                    <button type="button" class="btn btn-sm btn-primary ms-2" onclick="window.location.reload()" style="background: var(--primary-color); border: none; padding: 0.25rem 0.75rem; border-radius: 4px; color: white;">
                        🔄 Refresh
                    </button>
                </div>
                @endif

                <div class="instructions">
                    <h6>📝 Cara Pembayaran:</h6>
                    <ol>
                        @if($bankName === 'Mandiri')
                            <li>Buka aplikasi Livin' by Mandiri, Mandiri Online, atau kunjungi ATM Mandiri</li>
                            <li>Pilih menu "Bayar" atau "Transfer"</li>
                            <li>Pilih "Virtual Account" atau "Multipayment"</li>
                            @if($billerCode)
                                <li>Masukkan Biller Code: <strong>{{ $billerCode }}</strong></li>
                            @endif
                            <li>Masukkan nomor Virtual Account: <strong>{{ $vaNumber }}</strong></li>
                        @else
                            <li>Buka aplikasi mobile banking atau kunjungi ATM {{ $bankName }}</li>
                            <li>Pilih menu "Transfer" atau "Bayar"</li>
                            <li>Pilih "Virtual Account" atau "Rekening Ponsel"</li>
                            <li>Masukkan nomor Virtual Account: <strong>{{ $vaNumber }}</strong></li>
                        @endif
                        <li>Masukkan jumlah: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                        <li>Konfirmasi pembayaran</li>
                        <li>Pembayaran akan otomatis terverifikasi dalam 1-5 menit</li>
                    </ol>
                </div>

            @elseif(in_array($paymentMethod, ['gopay', 'ovo', 'dana', 'shopeepay', 'linkaja']))
                <!-- E-Wallet -->
                @if(isset($gatewayResponse['qr_string']) || isset($gatewayResponse['actions']))
                    @php
                        $qrCode = null;
                        $paymentUrl = null;
                        
                        if (isset($gatewayResponse['actions'])) {
                            foreach ($gatewayResponse['actions'] as $action) {
                                if ($action['name'] === 'generate-qr-code') {
                                    $qrCode = $action['url'];
                                }
                                if ($action['name'] === 'deeplink-redirect') {
                                    $paymentUrl = $action['url'];
                                }
                            }
                        }
                    @endphp

                    @if($qrCode)
                    <div class="qr-container">
                        <h6>📱 Scan QR Code</h6>
                        <img src="{{ $qrCode }}" alt="QR Code" class="qr-code">
                        <p class="mt-2 text-muted">Scan dengan aplikasi {{ ucfirst($paymentMethod) }}</p>
                    </div>
                    @endif

                    @if($paymentUrl)
                    <div class="text-center mb-3">
                        <a href="{{ $paymentUrl }}" class="action-btn primary" target="_blank">
                            📱 Buka {{ ucfirst($paymentMethod) }}
                        </a>
                    </div>
                    @endif
                @endif

                <div class="instructions">
                    <h6>📝 Cara Pembayaran:</h6>
                    <ol>
                        <li>Buka aplikasi {{ ucfirst($paymentMethod) }} di smartphone Anda</li>
                        <li>Scan QR Code di atas atau klik tombol "Buka {{ ucfirst($paymentMethod) }}"</li>
                        <li>Konfirmasi pembayaran sebesar <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                        <li>Masukkan PIN {{ ucfirst($paymentMethod) }} Anda</li>
                        <li>Pembayaran akan otomatis terverifikasi</li>
                    </ol>
                </div>

            @elseif($paymentMethod === 'qris')
                <!-- QRIS -->
                @php
                    $qrCodeUrl = null;
                    $qrString = null;
                    
                    // Get QR Code URL from Midtrans actions (preferred)
                    if (isset($gatewayResponse['actions'])) {
                        foreach ($gatewayResponse['actions'] as $action) {
                            if ($action['name'] === 'generate-qr-code') {
                                $qrCodeUrl = $action['url'];
                                break;
                            }
                        }
                    }
                    
                    // Get QRIS string for copy functionality
                    if (isset($gatewayResponse['qr_string'])) {
                        $qrString = $gatewayResponse['qr_string'];
                    }
                @endphp
                
                @if($qrCodeUrl || $qrString)
                <div class="qr-container">
                    <h6>📷 Scan QRIS</h6>
                    
                    @if($qrCodeUrl)
                        <!-- Real Midtrans QR Code -->
                        <img src="{{ $qrCodeUrl }}" alt="QRIS Code" class="qr-code" id="qris-image">
                        <p class="mt-2 text-muted">Scan dengan aplikasi e-wallet atau mobile banking apapun</p>
                        
                        <!-- QR Code Action Buttons -->
                        <div class="mt-3">
                            <button class="copy-btn me-2" onclick="copyQRCodeURL()" title="Copy QR Code URL">
                                🔗 Copy URL QR Code
                            </button>
                            <button class="copy-btn" onclick="downloadQRCode()" title="Download QR Code">
                                📥 Download QR Code
                            </button>
                        </div>
                    @endif
                    
                    @if($qrString)
                        <!-- QRIS String for Manual Copy -->
                        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; margin: 1rem 0;">
                            <h6>QRIS String (untuk simulator):</h6>
                            <p id="qris-string" style="word-break: break-all; font-family: monospace; font-size: 0.8rem; background: white; padding: 0.5rem; border-radius: 4px; border: 1px solid #dee2e6;">{{ $qrString }}</p>
                            <button class="copy-btn" onclick="copyQRISString()">
                                📋 Copy QRIS String
                            </button>
                            <small class="d-block mt-2 text-muted">
                                💡 Copy string ini untuk paste ke simulator QRIS atau aplikasi e-wallet
                            </small>
                        </div>
                    @endif
                </div>
                @endif

                <div class="instructions">
                    <h6>📝 Cara Pembayaran:</h6>
                    <ol>
                        <li>Buka aplikasi e-wallet atau mobile banking favorit Anda</li>
                        <li>Pilih menu "Scan QR" atau "QRIS"</li>
                        <li>Scan QR Code di atas</li>
                        <li>Konfirmasi pembayaran sebesar <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                        <li>Masukkan PIN aplikasi Anda</li>
                        <li>Pembayaran akan otomatis terverifikasi</li>
                    </ol>
                </div>

            @elseif($paymentMethod === 'credit_card')
                <!-- Credit Card -->
                @if(isset($gatewayResponse['redirect_url']))
                <div class="text-center mb-3">
                    <a href="{{ $gatewayResponse['redirect_url'] }}" class="action-btn primary" target="_blank">
                        💳 Lanjutkan ke Pembayaran Kartu Kredit
                    </a>
                </div>
                @endif

                <div class="instructions">
                    <h6>📝 Cara Pembayaran:</h6>
                    <ol>
                        <li>Klik tombol "Lanjutkan ke Pembayaran Kartu Kredit"</li>
                        <li>Masukkan detail kartu kredit Anda</li>
                        <li>Masukkan kode OTP yang dikirim ke nomor HP terdaftar</li>
                        <li>Konfirmasi pembayaran</li>
                        <li>Pembayaran akan otomatis terverifikasi</li>
                    </ol>
                </div>
            @endif
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="status-card fade-in">
            <div class="action-buttons">
                @if($transaction->status === 'verified')
                    <a href="{{ route('mahasiswa.payment.view-receipt', $transaction->id) }}" class="action-btn success">
                        🧾 Lihat Kwitansi
                    </a>
                    <a href="{{ route('mahasiswa.payment.receipt', $transaction->id) }}" class="action-btn primary">
                        📥 Download PDF
                    </a>
                @else
                    <button id="check-status-btn" class="action-btn primary" onclick="checkPaymentStatus()">
                        🔄 Cek Status Pembayaran
                    </button>
                    
                    @if($transaction->paymentMethod && $transaction->paymentMethod->type === 'midtrans_snap')
                        <button id="open-snap-btn" class="action-btn success" onclick="openSnapPayment()">
                            💳 Buka Pembayaran Snap
                        </button>
                    @endif
                    
                    <!-- Cancel Payment Button - Only show for pending payments -->
                    @if($transaction->status === 'pending')
                        <button id="cancel-payment-btn" class="action-btn" onclick="cancelPayment()" style="background: linear-gradient(135deg, var(--danger-color), #fc8181); color: white;">
                            🗑️ Batalkan Pembayaran
                        </button>
                    @endif
                @endif
                
                <!-- Tombol untuk akses ke halaman detail pembayaran -->
                <a href="{{ route('mahasiswa.payment.show', $transaction->id) }}" class="action-btn primary">
                    📄 Detail Pembayaran
                </a>
                
                <a href="{{ route('mahasiswa.payment.index') }}" class="action-btn secondary">
                    🏠 Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let statusCheckInterval;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-check status for pending payments
            @if($transaction->status === 'pending')
                startStatusPolling();
            @endif
        });
        
        function startStatusPolling() {
            // Check immediately
            checkPaymentStatus();
            
            // Then check every 10 seconds
            statusCheckInterval = setInterval(checkPaymentStatus, 10000);
        }
        
        function stopStatusPolling() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
            }
        }
        
        function checkPaymentStatus() {
            const btn = document.getElementById('check-status-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Mengecek...';
            }
            
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';
            
            fetch('{{ route("mahasiswa.payment.check-status-modern", $transaction->id) }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin'
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned HTML instead of JSON. Session may have expired.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateStatus(data.status, data.status_text, data.message);
                    
                    if (data.status === 'verified') {
                        stopStatusPolling();
                        showSuccessMessage();
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                } else {
                    console.error('Status check failed:', data.message);
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                
                // Handle session expiry
                if (error.message.includes('HTML instead of JSON')) {
                    console.warn('Session expired, stopping status polling');
                    stopStatusPolling();
                }
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '🔄 Cek Status Pembayaran';
                }
            });
        }
        
        function updateStatus(status, statusText, message) {
            const statusIcon = document.getElementById('status-icon');
            const statusTitle = document.getElementById('status-title');
            const statusSubtitle = document.getElementById('status-subtitle');
            const statusBadge = document.getElementById('status-badge');
            const statusTextEl = document.getElementById('status-text');
            
            // Update icon
            if (statusIcon) {
                statusIcon.className = `status-icon ${status}`;
                if (status === 'verified') {
                    statusIcon.textContent = '✅';
                } else if (status === 'failed') {
                    statusIcon.textContent = '❌';
                } else {
                    statusIcon.textContent = '⏳';
                }
            }
            
            // Update title
            if (statusTitle) {
                if (status === 'verified') {
                    statusTitle.textContent = 'Pembayaran Berhasil!';
                } else if (status === 'failed') {
                    statusTitle.textContent = 'Pembayaran Gagal';
                } else {
                    statusTitle.textContent = 'Menunggu Pembayaran';
                }
            }
            
            // Update subtitle
            if (statusSubtitle) {
                statusSubtitle.textContent = message || statusText;
            }
            
            // Update badge
            if (statusBadge) {
                statusBadge.className = `status-badge ${status}`;
            }
            
            if (statusTextEl) {
                statusTextEl.textContent = statusText;
            }
        }
        
        function showSuccessMessage() {
            // Create success notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #48bb78, #68d391);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
            `;
            notification.innerHTML = '🎉 Pembayaran berhasil terverifikasi!';
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show copy success message
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #4299e1;
                    color: white;
                    padding: 0.75rem 1rem;
                    border-radius: 8px;
                    z-index: 9999;
                `;
                notification.textContent = '📋 Nomor berhasil disalin!';
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 2000);
            });
        }
        
        // QRIS QR Code Functions
        function copyQRCodeURL() {
            const qrImage = document.getElementById('qris-image');
            if (qrImage && qrImage.src) {
                const qrUrl = qrImage.src;
                
                // Fallback copy method for better browser compatibility
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(qrUrl).then(() => {
                        showNotification('🔗 URL QR Code berhasil disalin!', '#4299e1');
                        updateButtonState(event.target, '✅ Copied!');
                    }).catch(err => {
                        console.error('Clipboard API failed: ', err);
                        fallbackCopyText(qrUrl);
                    });
                } else {
                    fallbackCopyText(qrUrl);
                }
            } else {
                showNotification('❌ QR Code tidak ditemukan', '#f56565');
            }
        }
        
        function fallbackCopyText(text) {
            // Create temporary textarea for fallback copy
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showNotification('🔗 URL QR Code berhasil disalin!', '#4299e1');
                    updateButtonState(event.target, '✅ Copied!');
                } else {
                    showNotification('❌ Gagal menyalin URL QR Code', '#f56565');
                }
            } catch (err) {
                console.error('Fallback copy failed: ', err);
                showNotification('❌ Gagal menyalin URL QR Code', '#f56565');
            } finally {
                document.body.removeChild(textArea);
            }
        }
        
        function updateButtonState(button, text) {
            const originalText = button.textContent;
            button.textContent = text;
            button.style.background = 'rgba(72, 187, 120, 0.3)';
            
            setTimeout(() => {
                button.textContent = originalText;
                button.style.background = '';
            }, 2000);
        }
        
        function downloadQRCode() {
            const qrImage = document.getElementById('qris-image');
            if (qrImage && qrImage.src) {
                const qrUrl = qrImage.src;
                
                // Create download link
                const link = document.createElement('a');
                link.href = qrUrl;
                link.download = 'qris-payment-' + '{{ $transaction->transaction_code ?? "qris" }}' + '.png';
                link.target = '_blank';
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showNotification('📥 QR Code sedang didownload...', '#48bb78');
                
                // Update button temporarily
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Downloaded!';
                button.style.background = 'rgba(72, 187, 120, 0.3)';
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 2000);
            } else {
                showNotification('❌ QR Code tidak ditemukan', '#f56565');
            }
        }
        
        function copyQRISString() {
            const qrisStringElement = document.getElementById('qris-string');
            if (qrisStringElement) {
                const qrisString = qrisStringElement.textContent.trim();
                
                navigator.clipboard.writeText(qrisString).then(() => {
                    showNotification('📋 QRIS String berhasil disalin!', '#4299e1');
                    
                    // Update button temporarily
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '✅ Copied!';
                    button.style.background = 'rgba(72, 187, 120, 0.3)';
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.style.background = '';
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy QRIS string: ', err);
                    showNotification('❌ Gagal menyalin QRIS String', '#f56565');
                });
            } else {
                showNotification('❌ QRIS String tidak ditemukan', '#f56565');
            }
        }
        
        function showNotification(message, color = '#4299e1') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${color};
                color: white;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Open Snap Payment Function
        function openSnapPayment() {
            const btn = document.getElementById('open-snap-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Membuka Snap...';
            }
            
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';
            
            // Get Snap token from server with proper headers
            fetch('{{ route("mahasiswa.payment.snap-token", $transaction->id) }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin'
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned HTML instead of JSON. Please check your session and try again.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.snap_token) {
                    // Load Midtrans Snap JS if not already loaded
                    if (typeof window.snap === 'undefined') {
                        loadSnapJS(() => {
                            openSnapPopup(data.snap_token);
                        });
                    } else {
                        openSnapPopup(data.snap_token);
                    }
                } else {
                    showNotification('❌ ' + (data.message || 'Gagal mendapatkan token pembayaran'), '#f56565');
                    
                    // If authentication error, suggest refresh
                    if (data.error_code === 'AUTH_REQUIRED') {
                        setTimeout(() => {
                            if (confirm('Session expired. Refresh page to login again?')) {
                                window.location.reload();
                            }
                        }, 2000);
                    }
                }
            })
            .catch(error => {
                console.error('Error getting snap token:', error);
                
                // Handle specific error types
                if (error.message.includes('HTML instead of JSON')) {
                    showNotification('❌ Session expired. Please refresh the page and try again.', '#f56565');
                    setTimeout(() => {
                        if (confirm('Session expired. Refresh page now?')) {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    showNotification('❌ Terjadi kesalahan sistem: ' + error.message, '#f56565');
                }
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '💳 Buka Pembayaran Snap';
                }
            });
        }
        
        function loadSnapJS(callback) {
            // Check if Snap JS is already loaded
            if (window.snap) {
                callback();
                return;
            }
            
            // Load Snap JS dynamically
            const script = document.createElement('script');
            script.src = '{{ config("midtrans.is_production") ? "https://app.midtrans.com/snap/snap.js" : "https://app.sandbox.midtrans.com/snap/snap.js" }}';
            script.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
            script.onload = callback;
            script.onerror = function() {
                showNotification('❌ Gagal memuat Midtrans Snap JS', '#f56565');
            };
            document.head.appendChild(script);
        }
        
        function openSnapPopup(snapToken) {
            try {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        showNotification('🎉 Pembayaran berhasil!', '#48bb78');
                        
                        // Auto-check status after successful payment
                        setTimeout(() => {
                            checkPaymentStatus();
                        }, 2000);
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        showNotification('⏳ Pembayaran sedang diproses', '#ed8936');
                        
                        // Auto-check status after pending
                        setTimeout(() => {
                            checkPaymentStatus();
                        }, 2000);
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        showNotification('❌ Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan'), '#f56565');
                    },
                    onClose: function() {
                        console.log('Snap popup closed');
                        // Optional: Check status when popup is closed
                        setTimeout(() => {
                            checkPaymentStatus();
                        }, 1000);
                    }
                });
            } catch (error) {
                console.error('Error opening Snap popup:', error);
                showNotification('❌ Gagal membuka popup pembayaran', '#f56565');
            }
        }
        
        // Cancel payment function with SweetAlert
        function cancelPayment() {
            Swal.fire({
                title: '⚠️ Batalkan Pembayaran?',
                html: `
                    <div class="text-start">
                        <p><strong>Apakah Anda yakin ingin membatalkan pembayaran ini?</strong></p>
                        <div class="mt-3">
                            <p class="mb-2">⚠️ Transaksi akan dibatalkan secara permanen</p>
                            <p class="mb-2">🔄 Anda perlu membuat transaksi baru untuk melakukan pembayaran</p>
                            <p class="mb-0">💡 Pastikan Anda belum melakukan transfer sebelum membatalkan</p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🗑️ Ya, Batalkan Transaksi',
                cancelButtonText: '❌ Tidak, Tetap Lanjutkan',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading with progress
                    Swal.fire({
                        title: '🔄 Membatalkan Pembayaran...',
                        html: 'Sedang memproses pembatalan transaksi Anda',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit cancel request via AJAX for better error handling
                    fetch('/mahasiswa/payment/{{ $transaction->id }}/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '✅ Pembayaran Berhasil Dibatalkan!',
                                html: `
                                    <div class="text-center">
                                        <p>Transaksi telah berhasil dibatalkan.</p>
                                        <p class="text-muted">Anda akan diarahkan ke halaman pembayaran...</p>
                                    </div>
                                `,
                                icon: 'success',
                                timer: 3000,
                                showConfirmButton: false,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = '/mahasiswa/payment';
                            });
                        } else {
                            Swal.fire({
                                title: '❌ Gagal Membatalkan',
                                text: data.message || 'Terjadi kesalahan saat membatalkan pembayaran',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#667eea'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Cancel error:', error);
                        Swal.fire({
                            title: '🚨 Kesalahan Sistem',
                            text: 'Tidak dapat membatalkan pembayaran. Silakan coba lagi atau hubungi admin.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#667eea'
                        });
                    });
                }
            });
        }
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            stopStatusPolling();
        });
    </script>
@endsection

