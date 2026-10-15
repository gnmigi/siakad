@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Detail Pembayaran - {{ config('app.name') }}
@endsection

@section('custom-css')
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Mobile-first responsive design */
        .payment-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        
        .payment-info-table {
            font-size: 0.9rem;
        }
        
        .payment-info-table td {
            padding: 0.5rem 0;
            border: none;
            vertical-align: top;
        }
        
        .copy-button {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        
        .copy-button:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        
        .copy-button.copied {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .account-number-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin: 0.5rem 0;
            position: relative;
        }
        
        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 1.25rem;
            font-weight: bold;
            color: #495057;
            margin: 0;
            word-break: break-all;
        }
        
        .copy-icon {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        }
        
        .copy-icon:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.4);
        }
        
        .copy-icon:active {
            transform: translateY(0);
        }
        
        .copy-icon.copied {
            background: #28a745;
            color: white;
        }
        
        .payment-method-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .bank-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .payment-instructions {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid rgba(255, 255, 255, 0.3);
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 0 8px 8px 0;
            color: white;
        }
        
        .payment-instructions h6 {
            color: white;
            margin-bottom: 0.75rem;
        }
        
        .payment-instructions ol {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .payment-instructions strong {
            color: white;
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        /* Enhanced badge styling for better visibility */
        .badge {
            font-weight: 600;
            font-size: 0.9rem !important;
            padding: 0.6rem 1.2rem !important;
            border-radius: 25px !important;
            text-shadow: none !important;
            border: 2px solid transparent;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            color: white !important;
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(40,167,69,0.3);
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
            color: #212529 !important;
            border-color: rgba(33,37,41,0.2);
            box-shadow: 0 2px 8px rgba(255,193,7,0.3);
        }
        
        .badge.bg-info {
            background: linear-gradient(135deg, #17a2b8, #007bff) !important;
            color: white !important;
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(23,162,184,0.3);
        }
        
        .badge.bg-primary {
            background: linear-gradient(135deg, #007bff, #6610f2) !important;
            color: white !important;
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, #dc3545, #e83e8c) !important;
            color: white !important;
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(220,53,69,0.3);
        }
        
        .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            color: white !important;
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(108,117,125,0.3);
        }
        
        .action-buttons .btn {
            margin-bottom: 0.5rem;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .payment-info-table {
                font-size: 0.8rem;
            }
            
            .account-number {
                font-size: 1.1rem;
            }
            
            .payment-method-card {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
            
            .alert {
                font-size: 0.875rem;
                padding: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }
            
            .card {
                margin-bottom: 0.75rem;
            }
            
            .payment-info-table td:first-child {
                width: 35%;
                font-weight: 500;
            }
            
            .account-number {
                font-size: 1rem;
                line-height: 1.4;
            }
            
            .copy-icon {
                position: static;
                display: block;
                margin-top: 0.5rem;
                text-align: center;
            }
        }
        
        /* Toast notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .toast {
            background: #28a745;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* QR Code Styling */
        .qr-code-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            padding: 1rem;
        }
        
        .qr-code-display {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .qr-code-image {
            max-width: 200px;
            max-height: 200px;
            width: 100%;
            height: auto;
            border-radius: 8px;
            background: white;
            padding: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .qr-instruction {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            margin-top: 1rem;
            margin-bottom: 0;
        }
        
        .qr-placeholder {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem 1rem;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .qr-code-simulator .qr-placeholder {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Mobile QR Code optimizations */
        @media (max-width: 768px) {
            .qr-code-container {
                min-height: 150px;
                padding: 0.5rem;
            }
            
            .qr-code-display {
                padding: 1rem;
            }
            
            .qr-code-image {
                max-width: 150px;
                max-height: 150px;
            }
            
            .qr-placeholder {
                padding: 1.5rem 1rem;
                min-height: 150px;
            }
            
            .payment-method-card .row {
                flex-direction: column-reverse;
            }
            
            .payment-method-card .col-md-6:first-child {
                margin-top: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .qr-code-image {
                max-width: 120px;
                max-height: 120px;
            }
            
            .qr-placeholder {
                padding: 1rem;
                min-height: 120px;
            }
        }
    </style>
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <!-- Payment Success Alert -->
            @if($transaction->status === 'verified')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">🎉 Pembayaran Berhasil!</h5>
                            <p class="mb-0">Pembayaran Anda telah berhasil diverifikasi. Terima kasih atas pembayaran tepat waktu.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif($transaction->status === 'paid')
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Pembayaran Sedang Diverifikasi</h6>
                            <p class="mb-0">Pembayaran Anda sudah diterima dan sedang dalam proses verifikasi.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Payment Status Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Pembayaran</h5>
                    <div class="payment-status" data-transaction-id="{{ $transaction->id }}">
                        @if($transaction->status === 'pending')
                            <span class="badge bg-warning fs-6">Menunggu Pembayaran</span>
                        @elseif($transaction->status === 'processing')
                            <span class="badge bg-info fs-6">Sedang Diproses</span>
                        @elseif($transaction->status === 'paid')
                            <span class="badge bg-primary fs-6">Sudah Dibayar</span>
                        @elseif($transaction->status === 'verified')
                            <span class="badge bg-success fs-6">✅ Terverifikasi</span>
                        @elseif($transaction->status === 'failed')
                            <span class="badge bg-danger fs-6">Gagal</span>
                        @elseif($transaction->status === 'cancelled')
                            <span class="badge bg-secondary fs-6">Dibatalkan</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Tagihan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">Kode Transaksi:</td>
                                    <td><strong>{{ $transaction->transaction_code ?? $transaction->id }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Tagihan:</td>
                                    <td>{{ $transaction->tagihan->name ?? 'Tagihan Akademik' }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah:</td>
                                    <td><strong>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong></td>
                                </tr>
                                @if($transaction->fee_amount > 0)
                                <tr>
                                    <td>Biaya Admin:</td>
                                    <td>Rp {{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Total Bayar:</td>
                                    <td><strong class="text-primary">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Jatuh Tempo:</td>
                                    <td>
                                        @if($transaction->due_date)
                                            {{ $transaction->due_date->format('d M Y H:i') }}
                                        @elseif($transaction->tagihan && $transaction->tagihan->due_date)
                                            {{ $transaction->tagihan->due_date->format('d M Y H:i') }}
                                        @else
                                            <span class="text-muted">Tidak ada tempo</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Metode Pembayaran</h6>
                            <div class="d-flex align-items-center mb-3">
                                <i class="{{ $transaction->paymentMethod->icon ?? 'fas fa-credit-card' }} fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-0">{{ $transaction->paymentMethod->name }}</h6>
                                    <small class="text-muted">{{ ucfirst($transaction->paymentMethod->type) }}</small>
                                </div>
                            </div>

                            @if($transaction->paymentMethod->type === 'virtual_account')
                                <div class="payment-method-card">
                                    <h6 class="mb-3">
                                        <i class="fas fa-university me-2"></i>
                                        Virtual Account
                                    </h6>
                                    
                                    @php
                                        $vaNumber = '';
                                        if(isset($paymentInfo['virtual_account'])) {
                                            $vaNumber = $paymentInfo['virtual_account'];
                                        } elseif($transaction->virtual_account) {
                                            $vaNumber = $transaction->virtual_account;
                                        } else {
                                            $vaNumber = $transaction->gateway_response['va_numbers'][0]['va_number'] ?? 'Generating...';
                                        }
                                        
                                        $bankCode = '';
                                        $bankName = '';
                                        
                                        // Priority: Use selected bank account first, then gateway response
                                        if ($transaction->bankAccount) {
                                            $bankCode = $transaction->bankAccount->bank_code;
                                            $bankName = $transaction->bankAccount->bank_name;
                                        } elseif (isset($transaction->gateway_response['va_numbers'][0]['bank'])) {
                                            $bankCode = $transaction->gateway_response['va_numbers'][0]['bank'];
                                        } else {
                                            $bankCode = 'permata'; // default
                                        }
                                        
                                        // If bank name not set from bank account, use mapping
                                        if (empty($bankName)) {
                                            $bankNames = [
                                                'permata' => 'Bank Permata',
                                                'bca' => 'Bank Central Asia (BCA)',
                                                'bni' => 'Bank Negara Indonesia (BNI)',
                                                'bri' => 'Bank Rakyat Indonesia (BRI)',
                                                'mandiri' => 'Bank Mandiri',
                                                'cimb' => 'CIMB Niaga',
                                                'danamon' => 'Bank Danamon',
                                                'maybank' => 'Maybank Indonesia',
                                                'mega' => 'Bank Mega',
                                                'btpn' => 'Bank BTPN'
                                            ];
                                            
                                            $bankName = $bankNames[strtolower($bankCode)] ?? ucfirst($bankCode);
                                        }
                                    @endphp
                                    
                                    <div class="bank-info">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $bankName }}</strong>
                                            <small class="opacity-75">Virtual Account</small>
                                        </div>
                                        
                                        <div class="account-number-display">
                                            <p class="account-number" id="va-number">{{ $vaNumber }}</p>
                                            <button class="copy-icon" onclick="copyToClipboard('va-number', this)" title="Copy Virtual Account">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Jumlah Transfer:</span>
                                                <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-instructions">
                                        <h6><i class="fas fa-info-circle me-2"></i>Cara Pembayaran:</h6>
                                        <ol class="mb-0 ps-3">
                                            <li>Buka aplikasi mobile banking atau internet banking {{ $bankName }}</li>
                                            <li>Pilih menu <strong>Transfer > Virtual Account</strong></li>
                                            <li>Masukkan nomor Virtual Account di atas</li>
                                            <li>Masukkan nominal: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                                            <li>Konfirmasi dan selesaikan pembayaran</li>
                                            <li>Simpan bukti transfer sebagai konfirmasi</li>
                                        </ol>
                                    </div>
                                </div>
                            @elseif($transaction->paymentMethod->type === 'bank_transfer' && $transaction->bankAccount)
                                <div class="payment-method-card">
                                    <h6 class="mb-3">
                                        <i class="fas fa-university me-2"></i>
                                        Transfer Bank
                                    </h6>
                                    
                                    <div class="bank-info">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $transaction->bankAccount->bank_name }}</strong>
                                            <small class="opacity-75">Transfer Manual</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small opacity-75">Nomor Rekening:</label>
                                            <div class="account-number-display">
                                                <p class="account-number" id="bank-account">{{ $transaction->bankAccount->account_number }}</p>
                                                <button class="copy-icon" onclick="copyToClipboard('bank-account', this)" title="Copy Nomor Rekening">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small opacity-75">Atas Nama:</label>
                                            <div class="account-number-display">
                                                <p class="account-number" id="account-name">{{ $transaction->bankAccount->account_name }}</p>
                                                <button class="copy-icon" onclick="copyToClipboard('account-name', this)" title="Copy Nama Rekening">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Jumlah Transfer:</span>
                                                <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-instructions">
                                        <h6><i class="fas fa-info-circle me-2"></i>Cara Pembayaran:</h6>
                                        <ol class="mb-0 ps-3">
                                            <li>Buka aplikasi mobile banking atau internet banking</li>
                                            <li>Pilih menu <strong>Transfer > Antar Bank</strong></li>
                                            <li>Masukkan nomor rekening dan nama penerima di atas</li>
                                            <li>Masukkan nominal: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                                            <li>Konfirmasi dan selesaikan pembayaran</li>
                                            <li>Upload bukti transfer di form di bawah</li>
                                        </ol>
                                    </div>
                                </div>
                            @elseif($transaction->paymentMethod->type === 'e_wallet')
                                <div class="payment-method-card">
                                    <h6 class="mb-3">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        Pembayaran {{ $transaction->paymentMethod->name }}
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="bank-info">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong>{{ $transaction->paymentMethod->name }}</strong>
                                                    <small class="opacity-75">E-Wallet</small>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span>Jumlah Bayar:</span>
                                                        <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                                
                                                @if(isset($paymentInfo['payment_url']) && $paymentInfo['payment_url'] !== '#')
                                                    <div class="mt-3">
                                                        <a href="{{ $paymentInfo['payment_url'] }}" target="_blank" class="btn btn-light btn-sm w-100" 
                                                           onclick="return confirmOpenApp('{{ $transaction->paymentMethod->name }}')">
                                                            <i class="fas fa-mobile-alt me-2"></i> Buka {{ $transaction->paymentMethod->name }}
                                                        </a>
                                                        <small class="text-white opacity-75 d-block mt-2">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Akan membuka aplikasi {{ $transaction->paymentMethod->name }} di perangkat Anda
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                {{-- QRIS String Copy Feature --}}
                                @if(strpos(strtolower($transaction->paymentMethod->name), 'qris') !== false)
                                    @if(isset($paymentInfo['qris_string']))
                                        <div class="mt-3">
                                            <label class="form-label small opacity-75">QRIS String (untuk simulator):</label>
                                            <div class="account-number-display">
                                                <p class="account-number small" id="qris-string" style="font-size: 0.7rem; line-height: 1.2; word-break: break-all;">{{ $paymentInfo['qris_string'] }}</p>
                                                <button class="copy-icon" onclick="copyToClipboard('qris-string', this)" title="Copy QRIS String">
                                                    <i class="fas fa-copy"></i> Copy String
                                                </button>
                                            </div>
                                            <small class="text-white opacity-75 d-block mt-1">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Copy string ini untuk paste ke simulator QRIS
                                            </small>
                                        </div>
                                    @endif
                                    
                                    @if(isset($paymentInfo['qr_code_url']))
                                        <div class="mt-3">
                                            <label class="form-label small opacity-75">QR Code URL (untuk copy/download):</label>
                                            <div class="account-number-display">
                                                <p class="account-number small" id="qr-code-url" style="font-size: 0.7rem; line-height: 1.2; word-break: break-all;">{{ $paymentInfo['qr_code_url'] }}</p>
                                                <button class="copy-icon" onclick="copyToClipboard('qr-code-url', this)" title="Copy QR Code URL">
                                                    <i class="fas fa-link"></i> Copy URL
                                                </button>
                                            </div>
                                            <small class="text-white opacity-75 d-block mt-1">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Copy URL ini untuk akses langsung ke QR Code
                                            </small>
                                        </div>
                                    @endif
                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="qr-code-container">
                                                @if(isset($paymentInfo['qr_code']))
                                                    @if(isset($paymentInfo['is_simulator']) && $paymentInfo['is_simulator'])
                                                        <!-- Simulator QR Code -->
                                                        <div class="qr-code-simulator">
                                                            <div class="qr-placeholder">
                                                                <i class="fas fa-qrcode fa-4x text-white opacity-75"></i>
                                                                <p class="mt-2 mb-1 text-white"><strong>QR Code {{ $transaction->paymentMethod->name }}</strong></p>
                                                                <small class="text-white opacity-75">Scan dengan aplikasi {{ $transaction->paymentMethod->name }}</small>
                                                                <div class="mt-3">
                                                                    <button class="btn btn-success btn-sm" onclick="simulatePayment()">
                                                                        <i class="fas fa-check me-1"></i> Simulasi Bayar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Real QR Code -->
                                                        <div class="qr-code-display">
                                                            <img src="{{ $paymentInfo['qr_code'] }}" alt="QR Code" class="qr-code-image" id="qris-image">
                                                            <p class="qr-instruction">Scan QR Code dengan aplikasi {{ $transaction->paymentMethod->name }}</p>
                                                            
                                                            {{-- QR Code Action Buttons --}}
                                                            <div class="mt-2">
                                                                <button class="btn btn-light btn-sm me-2" onclick="copyQRCodeURL()" title="Copy QR Code URL">
                                                                    <i class="fas fa-link me-1"></i> Copy URL
                                                                </button>
                                                                <button class="btn btn-light btn-sm" onclick="downloadQRCode()" title="Download QR Code">
                                                                    <i class="fas fa-download me-1"></i> Download
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <!-- Fallback when no QR code -->
                                                    <div class="qr-placeholder">
                                                        <i class="fas fa-qrcode fa-4x text-white opacity-50"></i>
                                                        <p class="mt-2 mb-1 text-white">QR Code sedang dimuat...</p>
                                                        <small class="text-white opacity-75">Silakan refresh halaman jika QR Code tidak muncul</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-instructions">
                                        <h6><i class="fas fa-info-circle me-2"></i>Cara Pembayaran:</h6>
                                        <ol class="mb-0 ps-3">
                                            <li>Buka aplikasi <strong>{{ $transaction->paymentMethod->name }}</strong> di smartphone Anda</li>
                                            <li>Pilih menu <strong>Scan QR Code</strong> atau <strong>Bayar</strong></li>
                                            <li>Arahkan kamera ke QR Code di samping</li>
                                            <li>Konfirmasi pembayaran sebesar <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></li>
                                            <li>Selesaikan pembayaran dan tunggu konfirmasi</li>
                                            <li>Status pembayaran akan otomatis terupdate</li>
                                        </ol>
                                        
                                        @if(strpos(strtolower($transaction->paymentMethod->name), 'qris') !== false)
                                        <div class="mt-3 p-2" style="background: rgba(255,255,255,0.1); border-radius: 6px;">
                                            <small class="text-white">
                                                <strong>💡 Tips untuk Testing:</strong><br>
                                                • Copy QRIS String di atas untuk paste ke simulator<br>
                                                • Copy URL QR Code untuk akses langsung<br>
                                                • Download QR Code untuk simpan offline
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
            @elseif($transaction->paymentMethod->type === 'midtrans_snap')
                <div class="payment-method-card">
                    <h6 class="mb-3">
                        <i class="fas fa-credit-card me-2"></i>
                        Pembayaran Online (Midtrans Snap)
                    </h6>
                    
                    <div class="bank-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Midtrans Payment Gateway</strong>
                            <small class="opacity-75">All Payment Methods</small>
                        </div>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Jumlah Bayar:</span>
                                <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        
                        @if(session('snap_token') || (isset($transaction->gateway_response['token']) && $transaction->status === 'pending'))
                            <div class="mt-3">
                                <button id="pay-button" class="btn btn-success btn-lg w-100" onclick="payWithSnap()">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Bayar Sekarang
                                </button>
                                <small class="text-white opacity-75 d-block mt-2 text-center">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Klik untuk membuka popup pembayaran Midtrans
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="payment-instructions">
                        <h6><i class="fas fa-info-circle me-2"></i>Metode Pembayaran Tersedia:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🏦 Virtual Account:</h6>
                                <ul class="small mb-0 ps-3">
                                    <li>BCA, Mandiri, BNI, BRI</li>
                                    <li>Permata, CIMB, Danamon</li>
                                    <li>Maybank, OCBC, Panin</li>
                                    <li>BTN, BSI, Muamalat</li>
                                    <li>Dan 50+ bank lainnya</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>💳 E-Wallet & Kartu:</h6>
                                <ul class="small mb-0 ps-3">
                                    <li>GoPay, OVO, DANA</li>
                                    <li>ShopeePay, LinkAja</li>
                                    <li>Visa, MasterCard, JCB</li>
                                    <li>Indomaret, Alfamart</li>
                                    <li>Internet Banking</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($transaction->paymentMethod->type === 'cash')
                <div class="alert alert-warning">
                    <h6>Pembayaran Tunai:</h6>
                    <p class="mb-0">{{ $paymentInfo['instructions'] ?? 'Silakan datang ke bagian keuangan untuk melakukan pembayaran tunai' }}</p>
                </div>
            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            @if($transaction->status === 'pending' || $transaction->status === 'processing')
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Aksi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($transaction->paymentMethod->type === 'bank_transfer' || $transaction->paymentMethod->type === 'cash')
                                <div class="col-md-8">
                                    <h6>Upload Bukti Pembayaran</h6>
                                    <form action="{{ route('mahasiswa.payment.upload-proof', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Bukti Pembayaran *</label>
                                                <input type="file" class="form-control" name="proof_file" accept="image/*,application/pdf" required>
                                                <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tanggal Pembayaran</label>
                                                <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nama Pengirim</label>
                                                <input type="text" class="form-control" name="sender_name" value="{{ $mahasiswa->name }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">No. Rekening Pengirim</label>
                                                <input type="text" class="form-control" name="sender_account">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Catatan</label>
                                                <textarea class="form-control" name="notes" rows="2"></textarea>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <h6>Aksi Lainnya</h6>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" onclick="checkPaymentStatusModern()">
                                        <i class="fas fa-sync"></i> Cek Status
                                    </button>
                                    @if($transaction->status === 'pending')
                                        <form action="{{ route('mahasiswa.payment.cancel', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Yakin ingin membatalkan pembayaran ini?')">
                                                <i class="fas fa-times"></i> Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Receipt Download -->
            @if($transaction->status === 'verified')
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Kwitansi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <p>Pembayaran Anda telah berhasil diverifikasi. Anda dapat melihat atau mengunduh kwitansi pembayaran.</p>
                        <div class="btn-group" role="group">
                            <a href="{{ route('mahasiswa.payment.view-receipt', $transaction->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Lihat Kwitansi
                            </a>
                            <a href="{{ route('mahasiswa.payment.receipt', $transaction->id) }}" class="btn btn-success">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('custom-js')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <!-- Midtrans Snap.js Script -->
    @if($transaction->paymentMethod->type === 'midtrans_snap')
    <script type="text/javascript"
            src="{{ config('midtrans.is_production') ? config('midtrans.production_snap_url') : config('midtrans.sandbox_snap_url') }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <!-- Custom Snap Styling -->
    <style id="snap-custom-styles">
        /* Custom Midtrans Snap Popup Styling */
        .snap-overlay {
            background: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(5px) !important;
        }
        
        .snap-container {
            border-radius: 20px !important;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3) !important;
            overflow: hidden !important;
            max-width: 450px !important;
            margin: auto !important;
        }
        
        .snap-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 20px !important;
            text-align: center !important;
            border-bottom: none !important;
        }
        
        .snap-header h2 {
            color: white !important;
            font-weight: 600 !important;
            margin: 0 !important;
            font-size: 1.5rem !important;
        }
        
        .snap-body {
            background: #ffffff !important;
            padding: 0 !important;
        }
        
        .snap-payment-method {
            border: none !important;
            border-radius: 12px !important;
            margin: 10px !important;
            padding: 15px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }
        
        .snap-payment-method:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2) !important;
            border-color: #667eea !important;
        }
        
        .snap-payment-method.selected {
            border: 2px solid #667eea !important;
            background: rgba(102, 126, 234, 0.05) !important;
        }
        
        .snap-button-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 15px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }
        
        .snap-button-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
        }
        
        .snap-button-secondary {
            background: transparent !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 15px 30px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        
        .snap-button-secondary:hover {
            border-color: #667eea !important;
            color: #667eea !important;
            background: rgba(102, 126, 234, 0.05) !important;
        }
        
        .snap-amount {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
            border-radius: 12px !important;
            padding: 20px !important;
            margin: 15px !important;
            text-align: center !important;
            border: 1px solid #e2e8f0 !important;
        }
        
        .snap-amount-value {
            font-size: 1.8rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin: 0 !important;
        }
        
        .snap-amount-label {
            color: #64748b !important;
            font-size: 0.9rem !important;
            margin-top: 5px !important;
        }
        
        .snap-payment-list {
            max-height: 400px !important;
            overflow-y: auto !important;
            padding: 10px !important;
        }
        
        .snap-payment-list::-webkit-scrollbar {
            width: 6px !important;
        }
        
        .snap-payment-list::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 3px !important;
        }
        
        .snap-payment-list::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 3px !important;
        }
        
        .snap-payment-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }
        
        .snap-footer {
            background: #f8fafc !important;
            padding: 20px !important;
            border-top: 1px solid #e2e8f0 !important;
            text-align: center !important;
        }
        
        .snap-close-button {
            position: absolute !important;
            top: 15px !important;
            right: 15px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            border: none !important;
            border-radius: 50% !important;
            width: 35px !important;
            height: 35px !important;
            color: white !important;
            font-size: 1.2rem !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        
        .snap-close-button:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            transform: scale(1.1) !important;
        }
        
        .snap-logo {
            max-height: 40px !important;
            margin-bottom: 10px !important;
        }
        
        .snap-institution-name {
            font-size: 1.1rem !important;
            font-weight: 500 !important;
            color: rgba(255, 255, 255, 0.9) !important;
            margin-top: 5px !important;
        }
        
        /* Bank/Payment Method Icons Styling */
        .snap-payment-method img {
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Loading Animation */
        .snap-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%) !important;
            background-size: 200% 100% !important;
            animation: loading 1.5s infinite !important;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .snap-container {
                max-width: 95% !important;
                margin: 10px auto !important;
                border-radius: 15px !important;
            }
            
            .snap-header {
                padding: 15px !important;
            }
            
            .snap-header h2 {
                font-size: 1.3rem !important;
            }
            
            .snap-payment-method {
                margin: 8px !important;
                padding: 12px !important;
            }
            
            .snap-button-primary,
            .snap-button-secondary {
                padding: 12px 25px !important;
                font-size: 0.9rem !important;
            }
            
            .snap-amount {
                margin: 10px !important;
                padding: 15px !important;
            }
            
            .snap-amount-value {
                font-size: 1.5rem !important;
            }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .snap-body {
                background: #1e293b !important;
                color: #f1f5f9 !important;
            }
            
            .snap-payment-method {
                background: #334155 !important;
                border-color: #475569 !important;
                color: #f1f5f9 !important;
            }
            
            .snap-payment-method:hover {
                background: #475569 !important;
            }
            
            .snap-amount {
                background: #334155 !important;
                border-color: #475569 !important;
            }
            
            .snap-amount-value {
                color: #f1f5f9 !important;
            }
            
            .snap-footer {
                background: #334155 !important;
                border-color: #475569 !important;
            }
        }
    </style>
    @endif
    
    <script>
// Test function to check if JavaScript works
function testAlert() {
    alert('🔧 JavaScript berfungsi! Sekarang coba tombol Cek Status di bawah.');
}

// Define variables at global scope
window.transactionId = {{ $transaction->id }};
window.transactionStatus = '{{ $transaction->status }}';
window.paymentMethodName = '{{ addslashes($transaction->paymentMethod->name) }}';

// Modern Payment Status Check with SweetAlert
function checkPaymentStatusModern() {
    Swal.fire({
        title: '🔄 Mengecek Status Pembayaran',
        text: 'Sedang menghubungi server...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
            
            var xhr = new XMLHttpRequest();
            var url = '{{ url('/mahasiswa/payment') }}/{{ $transaction->id }}/status';
            
            xhr.open('GET', url, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            
                            if (data.success) {
                                if (data.status === 'verified') {
                                    Swal.fire({
                                        title: '🎉 Pembayaran Berhasil!',
                                        html: '<strong>Status:</strong> Terverifikasi<br><strong>Jumlah:</strong> Rp ' + (data.amount ? new Intl.NumberFormat('id-ID').format(data.amount) : 'N/A') + '<br><br>Halaman akan dimuat ulang...',
                                        icon: 'success',
                                        timer: 3000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else if (data.status === 'pending' && data.midtrans_status === 'settlement') {
                                    Swal.fire({
                                        title: '💰 Pembayaran Ditemukan!',
                                        html: '<strong>Status Sistem:</strong> ' + data.status + '<br><strong>Status Midtrans:</strong> ' + data.midtrans_status + '<br><strong>Jumlah:</strong> Rp ' + (data.midtrans_amount ? new Intl.NumberFormat('id-ID').format(data.midtrans_amount) : 'N/A') + '<br><br>✅ Pembayaran sudah diterima di Midtrans dan sedang diproses untuk verifikasi otomatis.<br><br>Halaman akan dimuat ulang...',
                                        icon: 'success',
                                        timer: 5000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    var statusMsg = '<strong>Status Sistem:</strong> ' + data.status;
                                    if (data.midtrans_status) {
                                        statusMsg += '<br><strong>Status Midtrans:</strong> ' + data.midtrans_status;
                                    }
                                    if (data.message) {
                                        statusMsg += '<br><strong>Pesan:</strong> ' + data.message;
                                    }
                                    statusMsg += '<br><br>Jika Anda sudah melakukan pembayaran, silakan tunggu beberapa saat untuk verifikasi otomatis.';
                                    
                                    Swal.fire({
                                        title: 'ℹ️ Status Pembayaran',
                                        html: statusMsg,
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
                        } catch (e) {
                            Swal.fire({
                                title: '❌ Error Parsing Data',
                                html: '<strong>Error:</strong> ' + e.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    } else {
                        var errorMsg = 'Kesalahan Koneksi (HTTP ' + xhr.status + ')';
                        if (xhr.status === 404) {
                            errorMsg = 'Transaksi tidak ditemukan dalam sistem';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Terjadi kesalahan pada server';
                        }
                        
                        Swal.fire({
                            title: '❌ Error Koneksi',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            };
            
            xhr.onerror = function() {
                Swal.fire({
                    title: '❌ Kesalahan Jaringan',
                    text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            };
            
            xhr.send();
        }
    });
}

// Simple Payment Status Check - langsung pakai alert, pasti jalan
function checkPaymentStatusSimple(transactionId) {
    alert('🔄 Mengecek status pembayaran...');
    
    var xhr = new XMLHttpRequest();
    var url = '{{ url('/mahasiswa/payment') }}/' + transactionId + '/status';
    
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            alert('📡 Response received - HTTP Status: ' + xhr.status);
            
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    alert('✅ Data parsed successfully');
                    
                    if (data.success) {
                        if (data.status === 'verified') {
                            alert('🎉 PEMBAYARAN BERHASIL DIVERIFIKASI!\n\nStatus: Terverifikasi\nJumlah: Rp ' + (data.amount ? new Intl.NumberFormat('id-ID').format(data.amount) : 'N/A') + '\n\nHalaman akan dimuat ulang...');
                            window.location.reload();
                        } else if (data.status === 'pending' && data.midtrans_status === 'settlement') {
                            alert('💰 PEMBAYARAN DITEMUKAN!\n\nStatus Sistem: ' + data.status + '\nStatus Midtrans: ' + data.midtrans_status + '\nJumlah: Rp ' + (data.midtrans_amount ? new Intl.NumberFormat('id-ID').format(data.midtrans_amount) : 'N/A') + '\n\n✅ Pembayaran sudah diterima di Midtrans dan sedang diproses untuk verifikasi otomatis.\n\nHalaman akan dimuat ulang...');
                            window.location.reload();
                        } else {
                            var statusMsg = 'Status Pembayaran:\n\nStatus Sistem: ' + data.status;
                            if (data.midtrans_status) {
                                statusMsg += '\nStatus Midtrans: ' + data.midtrans_status;
                            }
                            if (data.message) {
                                statusMsg += '\nPesan: ' + data.message;
                            }
                            statusMsg += '\n\nJika Anda sudah melakukan pembayaran, silakan tunggu beberapa saat untuk verifikasi otomatis.';
                            alert(statusMsg);
                        }
                    } else {
                        alert('❌ Gagal mengecek status: ' + (data.message || 'Terjadi kesalahan saat mengecek status pembayaran'));
                    }
                } catch (e) {
                    alert('❌ JSON Parse Error: ' + e.message + '\n\nResponse: ' + xhr.responseText.substring(0, 200));
                }
            } else {
                var errorMsg = '❌ Kesalahan Koneksi (HTTP ' + xhr.status + ')';
                if (xhr.status === 404) {
                    errorMsg = '❌ Transaksi tidak ditemukan dalam sistem';
                } else if (xhr.status === 500) {
                    errorMsg = '❌ Terjadi kesalahan pada server';
                }
                alert(errorMsg);
            }
        }
    };
    
    xhr.onerror = function() {
        alert('❌ Kesalahan Jaringan: Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
    };
    
    xhr.send();
    alert('📤 Request sent to: ' + url);
};

// Define simulatePayment function at global scope
window.simulatePayment = function() {
    if (confirm('Simulasi pembayaran ' + window.paymentMethodName + '?')) {
        var button = event.target;
        var originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        button.disabled = true;
        
        // Simulate payment success after 2 seconds
        setTimeout(function() {
            alert('Pembayaran ' + window.paymentMethodName + ' berhasil! Halaman akan dimuat ulang...');
            
            // Trigger payment status check
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        }, 2000);
    }
};

// Confirm open app function
function confirmOpenApp(appName) {
    return confirm('Akan membuka aplikasi ' + appName + ' di perangkat Anda.\n\nPastikan aplikasi ' + appName + ' sudah terinstall.\n\nLanjutkan?');
}

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
        
        // Show toast notification
        showToast('Berhasil disalin: ' + text);
        
        // Reset button after 2 seconds
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('copied');
        }, 2000);
        
    } catch (err) {
        console.error('Failed to copy: ', err);
        showToast('Gagal menyalin teks', 'error');
    }
    
    document.body.removeChild(textarea);
}

// Show toast notification
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast element
    var toast = document.createElement('div');
    toast.className = 'toast';
    if (type === 'error') {
        toast.style.background = '#dc3545';
    }
    
    toast.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check' : 'exclamation-triangle') + ' me-2"></i>' + message;
    
    // Add to container
    container.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

console.log('Payment status JavaScript loaded successfully');
console.log('Transaction ID:', window.transactionId);
console.log('Transaction Status:', window.transactionStatus);

// QRIS QR Code Functions
function copyQRCodeURL() {
    var qrImage = document.getElementById('qris-image');
    if (qrImage && qrImage.src) {
        var qrUrl = qrImage.src;
        
        // Create temporary textarea
        var textarea = document.createElement('textarea');
        textarea.value = qrUrl;
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            showToast('QR Code URL berhasil disalin!');
            
            // Update button temporarily
            var button = event.target;
            var originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
            button.classList.add('btn-success');
            button.classList.remove('btn-light');
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-light');
            }, 2000);
            
        } catch (err) {
            console.error('Failed to copy QR URL: ', err);
            showToast('Gagal menyalin URL QR Code', 'error');
        }
        
        document.body.removeChild(textarea);
    } else {
        showToast('QR Code tidak ditemukan', 'error');
    }
}

function downloadQRCode() {
    var qrImage = document.getElementById('qris-image');
    if (qrImage && qrImage.src) {
        var qrUrl = qrImage.src;
        
        // Create download link
        var link = document.createElement('a');
        link.href = qrUrl;
        link.download = 'qris-payment-{{ $transaction->transaction_code }}.png';
        link.target = '_blank';
        
        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('QR Code sedang didownload...');
        
        // Update button temporarily
        var button = event.target;
        var originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check me-1"></i> Downloaded!';
        button.classList.add('btn-success');
        button.classList.remove('btn-light');
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-light');
        }, 2000);
        
    } else {
        showToast('QR Code tidak ditemukan', 'error');
    }
}

// Midtrans Snap Payment Handler Function
@if($transaction->paymentMethod->type === 'midtrans_snap')
function payWithSnap() {
    console.log('🚀 payWithSnap() called');
    
    // Get snap token from session or transaction
    var snapToken = '';
    @if(session('snap_token'))
        snapToken = '{{ session('snap_token') }}';
    @elseif(isset($transaction->gateway_response['token']))
        snapToken = '{{ $transaction->gateway_response["token"] }}';
    @endif
    
    console.log('- Snap token length:', snapToken.length);
    console.log('- Snap token preview:', snapToken.substring(0, 20) + '...');
    
    if (typeof snap === 'undefined') {
        console.error('❌ Midtrans Snap script not loaded');
        alert('❌ Error: Midtrans Snap script tidak dapat dimuat. Silakan refresh halaman.');
        return;
    }
    
    if (!snapToken || snapToken.length < 10) {
        console.error('❌ Invalid snap token:', snapToken);
        alert('❌ Error: Token pembayaran tidak valid. Silakan coba lagi atau hubungi admin.');
        return;
    }
    
    console.log('🌟 Triggering Snap popup...');
    
    // Show loading state
    var payButton = document.getElementById('pay-button');
    var originalText = payButton.innerHTML;
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memuat...';
    payButton.disabled = true;
    
    // Trigger Snap popup
    snap.pay(snapToken, {
        onSuccess: function(result) {
            console.log('✅ Payment success:', result);
            
            alert('🎉 Pembayaran Berhasil!\n\nPembayaran Anda telah berhasil diproses.\n\nHalaman akan dimuat ulang untuk memperbarui status...');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        },
        onPending: function(result) {
            console.log('⏳ Payment pending:', result);
            
            alert('⏳ Pembayaran Tertunda\n\nPembayaran Anda sedang diproses.\n\nSilakan selesaikan pembayaran sesuai instruksi yang diberikan.');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        },
        onError: function(result) {
            console.error('❌ Payment error:', result);
            
            // Reset button
            payButton.innerHTML = originalText;
            payButton.disabled = false;
            
            alert('❌ Pembayaran Gagal\n\nTerjadi kesalahan saat memproses pembayaran.\n\nSilakan coba lagi atau hubungi admin.');
        },
        onClose: function() {
            console.log('🔒 Payment popup closed');
            
            // Reset button
            payButton.innerHTML = originalText;
            payButton.disabled = false;
            
            // Check payment status after popup is closed
            setTimeout(() => {
                checkPaymentStatusModern();
            }, 1000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('🌟 Midtrans Snap integration loaded');
    console.log('- Snap script loaded:', typeof snap !== 'undefined');
    console.log('- Transaction ID:', {{ $transaction->id }});
    console.log('- Payment Method:', '{{ $transaction->paymentMethod->type }}');
    
    var payButton = document.getElementById('pay-button');
    if (payButton) {
        console.log('✅ Pay button found with onclick handler');
    } else {
        console.log('⚠️ Pay button not found - probably not a pending Midtrans Snap transaction');
    }
});
@endif

// Auto-polling for pending transactions
if (window.transactionStatus === 'pending' || window.transactionStatus === 'processing') {
    console.log('🔄 Starting auto-polling for transaction status...');
    
    // Auto-check every 30 seconds for pending transactions
    var autoCheckInterval = setInterval(function() {
        console.log('🔄 Auto-checking payment status...');
        
        var xhr = new XMLHttpRequest();
        var url = '{{ url('/mahasiswa/payment') }}/{{ $transaction->id }}/status';
        
        xhr.open('GET', url, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    
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
                        
                        // Update status badge
                        var statusElement = document.querySelector('.payment-status');
                        if (statusElement && data.status_badge) {
                            statusElement.innerHTML = data.status_badge;
                        }
                        
                        // Update global status
                        window.transactionStatus = data.status;
                        
                        // If status changed to verified, stop polling and reload
                        if (data.status === 'verified') {
                            clearInterval(autoCheckInterval);
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    }
                } catch (e) {
                    console.log('Auto-check parse error:', e.message);
                }
            }
        };
        
        xhr.send();
    }, 30000); // Check every 30 seconds
    
    // Also check immediately after 5 seconds
    setTimeout(function() {
        console.log('🚀 Initial auto-check after 5 seconds...');
        var xhr = new XMLHttpRequest();
        var url = '{{ url('/mahasiswa/payment') }}/{{ $transaction->id }}/status';
        
        xhr.open('GET', url, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    
                    if (data.success && data.status === 'verified') {
                        console.log('✅ Payment already verified!');
                        window.location.reload();
                    }
                } catch (e) {
                    console.log('Initial check parse error:', e.message);
                }
            }
        };
        
        xhr.send();
    }, 5000);
    
    // Show auto-refresh notification
    setTimeout(function() {
        var notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
        notification.innerHTML = '<div class="d-flex align-items-center">' +
            '<i class="fas fa-sync fa-spin me-2"></i>' +
            '<div>' +
            '<small><strong>Auto-Sync Aktif</strong></small><br>' +
            '<small>Status pembayaran akan dicek otomatis setiap 30 detik</small>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(notification);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }, 2000);
}
    </script>
@endsection
