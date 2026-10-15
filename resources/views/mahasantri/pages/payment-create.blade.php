@extends('central.backpage.core-backpage-mahasiswa')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(in_array(strtolower($tagihan->status ?? ''), ['cicilan', 'CICILAN']))
        @php
            $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
        @endphp
        <meta name="payment-amount" content="{{ $remainingAmount }}">
        <meta name="is-installment" content="true">
    @else
        <meta name="payment-amount" content="{{ $tagihan->amount }}">
        <meta name="is-installment" content="false">
    @endif
    @php
        // Simple query without cache to avoid complexity
        $midtransSnapMethod = \App\Models\Keuangan\PaymentMethod::where('type', 'midtrans_snap')->first();
    @endphp
    <meta name="midtrans-snap-method-id" content="{{ $midtransSnapMethod ? $midtransSnapMethod->id : '13' }}">
@endsection

@section('custom-css')
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

        /* Modern Container - More compact for desktop */
        .modern-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Payment Card - More compact */
        .payment-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .payment-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Dark mode support */
        [data-bs-theme="dark"] .payment-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: white;
        }

        /* Header Section - More compact */
        .checkout-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkout-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .checkout-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        [data-bs-theme="dark"] .checkout-subtitle {
            color: var(--gray-400);
        }

        /* Bill Summary */
        .bill-summary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .bill-amount {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .bill-description {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        /* Payment Methods Grid - More compact for desktop */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-method-card {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 140px;
        }

        .payment-method-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .payment-method-card:hover::before {
            left: 100%;
        }

        .payment-method-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }

        .payment-method-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(90, 103, 216, 0.05));
            transform: translateY(-2px);
        }

        [data-bs-theme="dark"] .payment-method-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: white;
        }

        [data-bs-theme="dark"] .payment-method-card.selected {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(90, 103, 216, 0.1));
        }

        /* Payment Method Content */
        .payment-method-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .payment-method-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .payment-method-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }

        .payment-method-description {
            color: var(--gray-600);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        [data-bs-theme="dark"] .payment-method-description {
            color: var(--gray-400);
        }

        /* Bank Icons */
        .bank-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .bank-icon {
            width: 32px;
            height: 20px;
            background: var(--gray-100);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        [data-bs-theme="dark"] .bank-icon {
            background: var(--gray-700);
            color: var(--gray-300);
        }

        /* Bank Selection */
        .bank-selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .bank-option {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .bank-option:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }

        .bank-option.selected {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.15);
            position: relative;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .bank-option.selected::before {
            content: '';
            position: absolute;
            top: 12px;
            right: 12px;
            width: 20px;
            height: 20px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bank-option.selected::after {
            content: '';
            position: absolute;
            top: 18px;
            right: 17px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        [data-bs-theme="dark"] .bank-option {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: white;
        }

        /* Action Button */
        .checkout-button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .checkout-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .checkout-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

        /* Upload Section */
        .upload-section {
            background: rgba(102, 126, 234, 0.05);
            border: 2px dashed var(--primary-color);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }

        [data-bs-theme="dark"] .upload-section {
            background: rgba(102, 126, 234, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modern-container {
                padding: 1rem;
            }

            .payment-methods-grid {
                grid-template-columns: 1fr;
            }

            .checkout-title {
                font-size: 2rem;
            }

            .bill-amount {
                font-size: 2rem;
            }

            .bank-selection-grid {
                grid-template-columns: 1fr;
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

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* SweetAlert2 Custom Styles */
        .swal-wide {
            width: 600px !important;
        }

        .swal2-popup {
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .swal2-title {
            color: var(--gray-800) !important;
            font-weight: 700 !important;
        }

        .swal2-content {
            color: var(--gray-600) !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
        }

        .swal2-cancel {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
        }

        .swal2-loading .swal2-styled.swal2-confirm {
            background: var(--primary-color) !important;
        }

        /* Dark mode support for SweetAlert */
        [data-bs-theme="dark"] .swal2-popup {
            background: var(--gray-800) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .swal2-title {
            color: white !important;
        }

        [data-bs-theme="dark"] .swal2-content {
            color: var(--gray-300) !important;
        }

        /* Legacy Support Styles */
        .legacy-section {
            background: rgba(255, 193, 7, 0.1);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .legacy-button {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .legacy-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="modern-container">
        <!-- Header -->
        <div class="checkout-header fade-in">
            <h1 class="checkout-title">💳 Pembayaran Tagihan</h1>
            <p class="checkout-subtitle">Pilih metode pembayaran yang paling nyaman untuk Anda</p>
        </div>

        <div class="row">
            <!-- Bill Summary -->
            <div class="col-lg-4 mb-4">
                <div class="payment-card fade-in">
                    <div class="bill-summary">
                        <div class="text-center">
                            @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                @php
                                    $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                                @endphp
                                <div class="bill-amount">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</div>
                                <div class="bill-description">{{ $tagihan->desc ?? 'Pembayaran Cicilan' }}</div>
                                <small class="text-light opacity-75">Sisa dari Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</small>
                            @else
                                <div class="bill-amount">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>
                                <div class="bill-description">{{ $tagihan->desc ?? 'Pembayaran Akademik' }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Bill Details -->
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">📋 Detail Tagihan</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kode:</span>
                                <span class="fw-bold">{{ $tagihan->code }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Jatuh Tempo:</span>
                                <span class="fw-bold">
                                    @if($tagihan->due_date)
                                        {{ \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') }}
                                    @else
                                        <span class="text-muted">Tidak ada tempo</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Status:</span>
                                @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                    <span class="badge bg-info">{{ $tagihan->status }} (Cicilan)</span>
                                @else
                                    <span class="badge bg-warning">{{ $tagihan->status }}</span>
                                @endif
                            </div>
                            @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']) && $tagihan->paid_amount > 0)
                                <div class="d-flex justify-content-between">
                                    <span>Sudah Dibayar:</span>
                                    <span class="fw-bold text-success">Rp {{ number_format($tagihan->paid_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div>
                        <h6 class="fw-bold mb-2">👤 Data Mahasiswa</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Nama:</span>
                                <span class="fw-bold">{{ $mahasiswa->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>NIM:</span>
                                <span class="fw-bold">{{ $mahasiswa->numb_nim }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="col-lg-8">
                <div class="payment-card fade-in">
                    <h5 class="fw-bold mb-4">🏦 Pilih Metode Pembayaran</h5>
                    
                    <form id="payment-form" action="{{ route('mahasiswa.payment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
                        <input type="hidden" name="payment_method_type" id="payment_method_type">
                        <input type="hidden" name="selected_bank" id="selected_bank">
                        <input type="hidden" name="payment_mode" id="payment_mode" value="full">
                        <input type="hidden" name="payment_amount" id="payment_amount" value="{{ in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']) ? ($tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0))) : $tagihan->amount }}">
                        
                        <div class="payment-methods-grid">
                            @foreach($paymentMethods as $method)
                                @php
                                    $methodType = $method->type;
                                    $methodName = $method->name;
                                    $methodDescription = $method->description ?: 'Metode pembayaran ' . $methodName;
                                    $methodIcon = $method->icon ?: 'fas fa-credit-card';

                                    // Define colors and emojis based on method type
                                    $methodColors = [
                                        'virtual_account' => 'linear-gradient(135deg, #4f46e5, #7c3aed)',
                                        'e_wallet' => 'linear-gradient(135deg, #059669, #10b981)',
                                        'qris' => 'linear-gradient(135deg, #dc2626, #ef4444)',
                                        'credit_card' => 'linear-gradient(135deg, #7c2d12, #ea580c)',
                                        'bank_transfer' => 'linear-gradient(135deg, #0891b2, #0e7490)',
                                        'cash' => 'linear-gradient(135deg, #16a34a, #15803d)',
                                        'midtrans_snap' => 'linear-gradient(135deg, #667eea, #764ba2)',
                                    ];
                                    $methodEmojis = [
                                        'virtual_account' => '🏦',
                                        'e_wallet' => '📱',
                                        'qris' => '📷',
                                        'credit_card' => '💳',
                                        'bank_transfer' => '🏛️',
                                        'cash' => '💵',
                                        'midtrans_snap' => '💳',
                                    ];

                                    $methodColor = $methodColors[$methodType] ?? 'linear-gradient(135deg, #6b7280, #4b5563)';
                                    $methodEmoji = $methodEmojis[$methodType] ?? '💰';
                                @endphp

                                <div class="payment-method-card" data-method="{{ $methodType }}" data-method-id="{{ $method->id }}">
                                    <div class="payment-method-header">
                                        <div class="payment-method-icon" style="background: {{ $methodColor }};">
                                            {{ $methodEmoji }}
                                        </div>
                                        <div>
                                            <h6 class="payment-method-title">{{ $methodName }}</h6>
                                        </div>
                                    </div>
                                    <p class="payment-method-description">
                                        {{ $methodDescription }}
                                    </p>

                                    <!-- Bank Icons for specific types -->
                                    @if($methodType === 'virtual_account')
                                        <div class="bank-icons">
                                            <div class="bank-icon">BCA</div>
                                            <div class="bank-icon">BNI</div>
                                            <div class="bank-icon">BRI</div>
                                            <div class="bank-icon">MDR</div>
                                            <div class="bank-icon">CIMB</div>
                                            <div class="bank-icon">BSI</div>
                                        </div>
                                    @elseif($methodType === 'e_wallet')
                                        <div class="bank-icons">
                                            <div class="bank-icon">GPY</div>
                                            <div class="bank-icon">OVO</div>
                                            <div class="bank-icon">DNA</div>
                                            <div class="bank-icon">SPY</div>
                                            <div class="bank-icon">LNK</div>
                                        </div>
                                    @elseif($methodType === 'qris')
                                        <div class="bank-icons">
                                            <div class="bank-icon">QR</div>
                                            <div class="bank-icon">ALL</div>
                                        </div>
                                    @elseif($methodType === 'credit_card')
                                        <div class="bank-icons">
                                            <div class="bank-icon">VISA</div>
                                            <div class="bank-icon">MC</div>
                                            <div class="bank-icon">JCB</div>
                                        </div>
                                    @elseif($methodType === 'bank_transfer')
                                        <div class="bank-icons">
                                            <div class="bank-icon">Manual</div>
                                            <div class="bank-icon">Upload</div>
                                        </div>
                                    @elseif($methodType === 'cash')
                                        <div class="bank-icons">
                                            <div class="bank-icon">Cash</div>
                                            <div class="bank-icon">Office</div>
                                        </div>
                                    @elseif($methodType === 'midtrans_snap')
                                        <div class="bank-icons">
                                            <div class="bank-icon">ALL</div>
                                            <div class="bank-icon">BANK</div>
                                            <div class="bank-icon">EWALLET</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Bank Selection (for Virtual Account) -->
                        <div id="bank-selection" style="display: none;" class="slide-up">
                            <h6 class="fw-bold mb-3">🏛️ Pilih Bank Virtual Account</h6>
                            <div class="bank-selection-grid">
                                <!-- Major Banks -->
                                <div class="bank-option" data-bank="bca_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">🏦</div>
                                        <span>BCA</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="mandiri_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">🏦</div>
                                        <span>Mandiri</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="bni_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">🏦</div>
                                        <span>BNI</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="bri_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">🏦</div>
                                        <span>BRI</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="cimb_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">🏦</div>
                                        <span>CIMB Niaga</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="bsi_va">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">�</div>
                                        <span>BSI (Bank Syariah Indonesia)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet Selection -->
                        <div id="ewallet-selection" style="display: none;" class="slide-up">
                            <h6 class="fw-bold mb-3">📱 Pilih E-Wallet</h6>
                            <div class="bank-selection-grid">
                                <div class="bank-option" data-bank="gopay">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">📱</div>
                                        <span>GoPay</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="ovo">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">📱</div>
                                        <span>OVO</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="dana">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">📱</div>
                                        <span>DANA</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="shopeepay">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">📱</div>
                                        <span>ShopeePay</span>
                                    </div>
                                </div>
                                <div class="bank-option" data-bank="linkaja">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">📱</div>
                                        <span>LinkAja</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Bank Transfer Selection -->
                        <div id="manual-bank-selection" style="display: none;" class="slide-up">
                            <h6 class="fw-bold mb-3">🏛️ Pilih Rekening Tujuan</h6>
                            <div class="bank-selection-grid">
                                @foreach($bankAccounts as $bank)
                                    <div class="bank-option" data-bank="manual_{{ $bank->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">🏦</div>
                                            <div>
                                                <div class="fw-bold">{{ $bank->bank_name }}</div>
                                                <small class="text-muted">{{ $bank->account_number }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Upload Section (for manual methods) -->
                        <div id="upload-section" style="display: none;" class="upload-section">
                            <h6 class="fw-bold mb-3">📤 Upload Bukti Pembayaran</h6>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Mode Pembayaran</label>
                                    <div class="d-flex flex-column flex-md-row gap-2">
                                        <div class="form-check border rounded p-2 flex-fill text-start">
                                            <input class="form-check-input" type="radio" name="payment_mode_radio" id="mode_full" value="full" checked>
                                            <label class="form-check-label" for="mode_full">
                                                Bayar Full (Lunas)
                                            </label>
                                        </div>
                                        <div class="form-check border rounded p-2 flex-fill text-start">
                                            <input class="form-check-input" type="radio" name="payment_mode_radio" id="mode_installment" value="installment">
                                            <label class="form-check-label" for="mode_installment">
                                                Bayar Cicilan (Input Manual)
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        Sisa tagihan saat ini: <strong id="remaining-amount-text">Rp {{ number_format(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']) ? ($tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0))) : $tagihan->amount, 0, ',', '.') }}</strong>
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3" id="installment-amount-wrapper" style="display:none;">
                                <div class="col-md-6">
                                    <label class="form-label">Nominal Cicilan *</label>
                                    <input type="text" class="form-control" id="installment_amount_input" inputmode="numeric" autocomplete="off" placeholder="Masukkan nominal cicilan (contoh: 200.000)">
                                    <small class="text-muted">Minimal Rp 1, maksimal sebesar sisa tagihan.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bukti Transfer *</label>
                                    <input type="file" class="form-control" name="transfer_proof" accept="image/*,.pdf">
                                    <small class="text-muted">Format: JPG, PNG, PDF (Max: 5MB)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="mt-4">
                            <button id="checkout-btn" class="checkout-button" disabled>
                                <span id="btn-text">Pilih Metode Pembayaran</span>
                                <span id="btn-loading" style="display: none;">
                                    <span class="loading-spinner"></span>
                                    Memproses...
                                </span>
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Pembayaran Aman & Terenkripsi
                                </small>
                            </div>
                        </div>
                    </form>

                    <!-- Legacy Midtrans Snap Backup -->
                    <!--<div class="legacy-section">-->
                        <!--<h6 class="fw-bold mb-2">🔄 Metode Pembayaran Alternatif</h6>-->
                        <!--<p class="mb-3 small">Jika metode di atas tidak tersedia, gunakan sistem pembayaran lama dengan Midtrans Snap:</p>-->
                        <!--<button type="button" class="legacy-button" onclick="useLegacyPayment()">-->
                        <!--    <i class="fas fa-credit-card me-2"></i>-->
                        <!--    Gunakan Midtrans Snap-->
                        <!--</button>-->
                        <!--<small class="d-block mt-2 text-muted">-->
                        <!--    Mendukung semua bank, e-wallet, dan kartu kredit melalui popup Midtrans-->
                        <!--</small>-->
                    <!--</div>-->
                </div>

                <!-- Action Buttons -->
                <div class="payment-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted">Total Pembayaran</div>
                            @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                @php
                                    $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                                @endphp
                                <div class="h3 text-primary mb-0">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</div>
                                <small class="text-muted">Sisa dari Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</small>
                            @else
                                <div class="h3 text-danger mb-0">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('mahasiswa.payment.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Midtrans Snap JS -->
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <script>
        // DYNAMIC REDIRECT FIX - Added by fix_payment_redirect.php
        window.getPaymentRedirectUrl = function(transactionId) {
            // Try to get actual transaction ID from current context
            const urlParams = new URLSearchParams(window.location.search);
            const currentTransactionId = urlParams.get('transaction_id') || transactionId || 10;
            return '/mahasiswa/payment/' + currentTransactionId;
        };
        
        // Override redirect functions
        window.redirectToPaymentShow = function(transactionId) {
            const redirectUrl = window.getPaymentRedirectUrl(transactionId);
            console.log('🔄 Redirecting to:', redirectUrl);
            window.location.href = redirectUrl;
        };
    </script>
    
    <script>
        // FIXED: Get payment amount from PHP with null checks to avoid errors
        function getMetaContent(name, defaultValue = '') {
            const meta = document.querySelector('meta[name="' + name + '"]');
            return meta ? meta.getAttribute('content') : defaultValue;
        }
        
        var PAYMENT_AMOUNT = getMetaContent('payment-amount', '0');
        var IS_INSTALLMENT = getMetaContent('is-installment', 'false') === 'true';
        var FALLBACK_PAYMENT_AMOUNT = '{{ in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']) ? ($tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0))) : $tagihan->amount }}';
        
        console.log('💰 Payment configuration loaded:', {
            amount: PAYMENT_AMOUNT,
            is_installment: IS_INSTALLMENT
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            let selectedMethod = null;
            let selectedBank = null;
            
            const methodCards = document.querySelectorAll('.payment-method-card');
            const bankOptions = document.querySelectorAll('.bank-option');
            const checkoutBtn = document.getElementById('checkout-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const paymentForm = document.getElementById('payment-form');
            
            // Payment method selection
            methodCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove previous selections
                    methodCards.forEach(c => c.classList.remove('selected'));
                    document.getElementById('bank-selection').style.display = 'none';
                    document.getElementById('ewallet-selection').style.display = 'none';
                    document.getElementById('manual-bank-selection').style.display = 'none';
                    document.getElementById('upload-section').style.display = 'none';

                    // Select current card
                    this.classList.add('selected');
                    selectedMethod = this.dataset.method;
                    selectedBank = null;

                    // Update hidden form fields
                    document.getElementById('payment_method_type').value = selectedMethod;

                    // Show appropriate sub-selection based on method type
                    if (selectedMethod === 'virtual_account') {
                        document.getElementById('bank-selection').style.display = 'block';
                        btnText.textContent = 'Pilih Bank Virtual Account';
                        checkoutBtn.disabled = true;
                    } else if (selectedMethod === 'e_wallet') {
                        document.getElementById('ewallet-selection').style.display = 'block';
                        btnText.textContent = 'Pilih E-Wallet';
                        checkoutBtn.disabled = true;
                    } else if (selectedMethod === 'bank_transfer') {
                        document.getElementById('manual-bank-selection').style.display = 'block';
                        document.getElementById('upload-section').style.display = 'block';
                        btnText.textContent = 'Pilih Bank Tujuan';
                        checkoutBtn.disabled = true;
                    } else if (selectedMethod === 'cash') {
                        btnText.textContent = 'Proses Pembayaran Tunai';
                        checkoutBtn.disabled = false;
                    } else if (selectedMethod === 'midtrans_snap') {
                        // Midtrans Snap doesn't need sub-selection
                        selectedBank = selectedMethod;
                        btnText.textContent = 'Bayar dengan Midtrans Snap';
                        checkoutBtn.disabled = false;
                    } else {
                        // QRIS, Credit Card, or other methods - no sub-selection needed
                        selectedBank = selectedMethod;
                        btnText.textContent = `Bayar dengan ${getMethodName(selectedMethod)}`;
                        checkoutBtn.disabled = false;
                    }
                });
            });
            
            // Bank/E-wallet selection
            bankOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove previous selections
                    bankOptions.forEach(o => o.classList.remove('selected'));
                    
                    // Select current option
                    this.classList.add('selected');
                    selectedBank = this.dataset.bank;
                    
                    // Update hidden form field
                    document.getElementById('selected_bank').value = selectedBank;
                    
                    // Enable checkout button
                    btnText.textContent = `Bayar dengan ${getBankName(selectedBank)}`;
                    checkoutBtn.disabled = false;
                });
            });
            
            // Installment UI bindings
            const paymentModeInput = document.getElementById('payment_mode');
            const paymentAmountInput = document.getElementById('payment_amount');
            const modeFull = document.getElementById('mode_full');
            const modeInstallment = document.getElementById('mode_installment');
            const installmentWrapper = document.getElementById('installment-amount-wrapper');
            const installmentAmountInput = document.getElementById('installment_amount_input');
            const rawPaymentAmount = (PAYMENT_AMOUNT || '0').toString().replace(/[^0-9.]/g, '');
            const fallbackRawPaymentAmount = (FALLBACK_PAYMENT_AMOUNT || '0').toString().replace(/[^0-9.]/g, '');
            const remainingAmountNumber = parseFloat(rawPaymentAmount) || parseFloat(fallbackRawPaymentAmount) || 0;
            console.log('🧪 Cicilan Debug Amount', {
                meta_payment_amount: PAYMENT_AMOUNT,
                fallback_payment_amount: FALLBACK_PAYMENT_AMOUNT,
                parsed_meta: parseFloat(rawPaymentAmount) || 0,
                parsed_fallback: parseFloat(fallbackRawPaymentAmount) || 0,
                final_remaining: remainingAmountNumber
            });

            function formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, Math.floor(number || 0)));
            }

            function syncPaymentModeUI() {
                if (!paymentModeInput || !paymentAmountInput) return;

                if (modeInstallment && modeInstallment.checked) {
                    paymentModeInput.value = 'installment';
                    installmentWrapper.style.display = 'flex';

                    const val = parseInstallmentInputToNumber(installmentAmountInput.value || '');
                    paymentAmountInput.value = val > 0 ? val : '';
                } else {
                    paymentModeInput.value = 'full';
                    installmentWrapper.style.display = 'none';
                    paymentAmountInput.value = remainingAmountNumber;
                }
            }

            if (modeFull) {
                modeFull.addEventListener('change', syncPaymentModeUI);
            }
            if (modeInstallment) {
                modeInstallment.addEventListener('change', syncPaymentModeUI);
            }

            function parseInstallmentInputToNumber(value) {
                const numeric = (value || '').toString().replace(/\D/g, '');
                return parseInt(numeric || '0', 10);
            }

            function formatInstallmentInput(value) {
                const numeric = (value || '').toString().replace(/\D/g, '');
                if (!numeric) return '';
                return new Intl.NumberFormat('id-ID').format(parseInt(numeric, 10));
            }

            if (installmentAmountInput) {
                installmentAmountInput.addEventListener('input', function() {
                    const cursorAtEnd = this.selectionStart === this.value.length;
                    const numericVal = parseInstallmentInputToNumber(this.value);
                    this.value = formatInstallmentInput(this.value);

                    if (cursorAtEnd) {
                        this.setSelectionRange(this.value.length, this.value.length);
                    }
                });
            }

            if (installmentAmountInput) {
                installmentAmountInput.addEventListener('blur', function() {
                    const numericVal = parseInstallmentInputToNumber(this.value);
                    this.value = numericVal > 0 ? formatInstallmentInput(numericVal) : '';

                    if (paymentModeInput.value === 'installment') {
                        paymentAmountInput.value = numericVal > 0 ? numericVal : '';
                    }
                });
            }

            // init
            if (installmentAmountInput) {
                installmentAmountInput.setAttribute('max', String(remainingAmountNumber));
            }
            syncPaymentModeUI();

            // Checkout button click
            checkoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!selectedMethod) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Metode Pembayaran',
                        text: 'Silakan pilih metode pembayaran terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                if ((selectedMethod === 'virtual_account' || selectedMethod === 'e_wallet' || selectedMethod === 'bank_transfer') && !selectedBank) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Bank/E-Wallet',
                        text: 'Silakan pilih bank atau e-wallet terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Validate installment amount for manual bank transfer
                if (selectedMethod === 'bank_transfer') {
                    syncPaymentModeUI();

                    if (paymentModeInput.value === 'installment') {
                        const installmentVal = parseInstallmentInputToNumber((installmentAmountInput.value || '').toString());
                        if (!installmentVal || installmentVal <= 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nominal Cicilan Tidak Valid',
                                text: 'Masukkan nominal cicilan lebih dari 0.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#667eea'
                            });
                            return;
                        }

                        if (installmentVal > remainingAmountNumber) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nominal Melebihi Sisa Tagihan',
                                text: 'Nominal cicilan tidak boleh melebihi sisa tagihan ' + formatRupiah(remainingAmountNumber),
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#667eea'
                            });
                            return;
                        }

                        paymentAmountInput.value = installmentVal;
                    } else {
                        paymentAmountInput.value = remainingAmountNumber;
                    }
                }

                // Show loading
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
                checkoutBtn.disabled = true;
                
                // Handle different payment methods
                if (selectedMethod === 'virtual_account' || selectedMethod === 'e_wallet' || selectedMethod === 'qris' || selectedMethod === 'credit_card') {
                    // Use modern Core API
                    createModernTransaction();
                } else {
                    // Use traditional form submission for manual methods
                    paymentForm.submit();
                }
            });
            
            function createModernTransaction() {
                const formData = new FormData();
                formData.append('tagihan_id', '{{ $tagihan->id }}');
                formData.append('payment_method_type', selectedMethod);
                formData.append('selected_bank', selectedBank);
                formData.append('amount', PAYMENT_AMOUNT);
                formData.append('_token', '{{ csrf_token() }}');
                
                console.log('🚀 Sending data:', {
                    tagihan_id: '{{ $tagihan->id }}',
                    payment_method_type: selectedMethod,
                    selected_bank: selectedBank,
                    amount: PAYMENT_AMOUNT,
                    is_installment: IS_INSTALLMENT
                });
                
                fetch('{{ route("mahasiswa.payment.create-transaction") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil Dibuat!',
                            text: 'Anda akan diarahkan ke halaman pembayaran...',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        // Show error with SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Membuat Pembayaran',
                            text: data.message || 'Terjadi kesalahan saat membuat pembayaran',
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#667eea'
                        });
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Sistem',
                        text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea'
                    });
                    resetButton();
                });
            }
            
            function resetButton() {
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
                checkoutBtn.disabled = false;
            }
            
            function getMethodName(method) {
                const names = {
                    'qris': 'QRIS',
                    'credit_card': 'Kartu Kredit',
                    'cash': 'Tunai'
                };
                return names[method] || method;
            }
            
            function getBankName(bank) {
                const names = {
                    // Virtual Account Banks
                    'bca_va': 'BCA Virtual Account',
                    'mandiri_va': 'Mandiri Virtual Account',
                    'bni_va': 'BNI Virtual Account',
                    'bri_va': 'BRI Virtual Account',
                    'cimb_va': 'CIMB Virtual Account',
                    'bsi_va': 'BSI Virtual Account',
                    'bjb_va': 'BJB Virtual Account',
                    'other_va': 'Bank Lainnya Virtual Account',
                    
                    // E-Wallets
                    'gopay': 'GoPay',
                    'ovo': 'OVO',
                    'dana': 'DANA',
                    'shopeepay': 'ShopeePay',
                    'linkaja': 'LinkAja',
                    
                    // Other Methods
                    'qris': 'QRIS',
                    'credit_card': 'Kartu Kredit',
                    'cash': 'Pembayaran Tunai'
                };
                
                // Handle manual bank transfer
                if (bank && bank.startsWith('manual_')) {
                    return 'Transfer Bank Manual';
                }
                
                return names[bank] || bank;
            }
        });
        
        // FIXED Legacy Midtrans Snap function - Handles JSON parsing errors
        async function useLegacyPayment() {
            Swal.fire({
                title: '💳 Midtrans Snap Payment',
                html: '<div class="text-start">' +
                    '<p><strong>Menggunakan sistem pembayaran Midtrans Snap?</strong></p>' +
                    '<div class="mt-3">' +
                    '<p class="mb-2">✅ Mendukung semua bank dan e-wallet</p>' +
                    '<p class="mb-2">✅ Kartu kredit (Visa, MasterCard, JCB)</p>' +
                    '<p class="mb-2">✅ Interface yang familiar dan aman</p>' +
                    '<p class="mb-0">✅ Proses pembayaran yang cepat</p>' +
                    '</div>' +
                    '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '🚀 Ya, Gunakan Midtrans Snap',
                cancelButtonText: '❌ Batal',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Show loading alert
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        html: 'Sedang menyiapkan Midtrans Snap untuk Anda',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Show loading on button
                    const legacyBtn = event.target;
                    const originalText = legacyBtn.innerHTML;
                    legacyBtn.innerHTML = '<span class="loading-spinner"></span> Memproses...';
                    legacyBtn.disabled = true;
                    
                    // FIXED: Use proper authenticated endpoint for Snap payment
                    console.log('🚀 Creating Snap payment transaction...');
                    
                    // First, create a proper payment transaction
                    const formData = new FormData();
                    
                    // FIXED: Dynamic payment method detection with null checks (tetap baca dari database)
                    let midtransSnapMethodId = getMetaContent('midtrans-snap-method-id');
                    
                    if (!midtransSnapMethodId || midtransSnapMethodId === '') {
                        console.log('⚠️ Meta tag not found, fetching payment method ID dynamically...');
                        
                        // Fetch payment method ID dynamically from server
                        try {
                            const response = await fetch('/api/payment-methods/midtrans-snap', {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            if (response.ok) {
                                const data = await response.json();
                                if (data.success && data.payment_method_id) {
                                    midtransSnapMethodId = data.payment_method_id;
                                    console.log('✅ Dynamic payment method ID retrieved:', midtransSnapMethodId);
                                } else {
                                    throw new Error('Payment method not found in response');
                                }
                            } else {
                                throw new Error('Failed to fetch payment method');
                            }
                        } catch (error) {
                            console.error('❌ Failed to get payment method ID:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Metode Pembayaran Tidak Tersedia',
                                text: 'Tidak dapat menemukan metode pembayaran Midtrans Snap. Silakan hubungi administrator.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#f56565'
                            });
                            legacyBtn.innerHTML = originalText;
                            legacyBtn.disabled = false;
                            return;
                        }
                    } else {
                        console.log('✅ Using payment method ID from meta tag:', midtransSnapMethodId);
                    }
                    
                    formData.append('tagihan_id', '{{ $tagihan->id }}');
                    formData.append('payment_method_id', midtransSnapMethodId);
                    
                    // FIXED: Safe CSRF token access with null checks
                    const csrfToken = getMetaContent('csrf-token', '{{ csrf_token() }}');
                    formData.append('_token', csrfToken);
                    
                    console.log('🔐 CSRF token added:', csrfToken ? 'Present' : 'Missing');
                    
                    // Add timeout to prevent stuck loading
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
                    
                    fetch('{{ route("mahasiswa.payment.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: controller.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId); // Clear timeout on successful response
                        console.log('📡 Response Status:', response.status);
                        console.log('📡 Content-Type:', response.headers.get('content-type'));
                        
                        // FIXED: Check if response is HTML (the main issue)
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server returned HTML instead of JSON - Authentication issue');
                        }
                        
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('📦 Response data:', data);
                        
                        // Close loading alert
                        Swal.close();
                        
                        if (data.success && data.snap_token) {
                            console.log('✅ Snap token received:', data.snap_token.substring(0, 20) + '...');
                            
                            // Check if snap is available
                            if (typeof window.snap === 'undefined') {
                                console.log('📦 Loading Midtrans Snap JS...');
                                
                                // Load Midtrans Snap JS dynamically
                                const script = document.createElement('script');
                                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                                script.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
                                script.onload = function() {
                                    console.log('✅ Snap JS loaded successfully');
                                    openSnapPopupFixed(data.snap_token, data.transaction_id, legacyBtn, originalText);
                                };
                                script.onerror = function() {
                                    console.error('❌ Failed to load Midtrans Snap JS');
                                    Swal.fire({
                                        icon: 'error',
                                        title: '❌ Gagal Memuat Snap JS',
                                        text: 'Gagal memuat library Midtrans Snap',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#f56565'
                                    });
                                    legacyBtn.innerHTML = originalText;
                                    legacyBtn.disabled = false;
                                };
                                document.head.appendChild(script);
                            } else {
                                console.log('✅ Snap JS already loaded');
                                openSnapPopupFixed(data.snap_token, data.transaction_id, legacyBtn, originalText);
                            }
                        } else if (data.redirect_url && data.redirect_url.includes('/payment/')) {
                            // If we get a redirect URL, it means payment was created successfully
                            // Extract transaction ID from redirect URL and get the Snap token
                            const transactionId = data.redirect_url.split('/payment/')[1];
                            console.log('✅ Payment created, getting Snap token for transaction:', transactionId);
                            
                            // Get Snap token from the transaction
                            fetch(`/mahasiswa/payment/${transactionId}/snap-token`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(tokenData => {
                                if (tokenData.success && tokenData.snap_token) {
                                    console.log('✅ Snap token retrieved:', tokenData.snap_token.substring(0, 20) + '...');
                                    
                                    // Check if snap is available
                                    if (typeof window.snap === 'undefined') {
                                        console.log('📦 Loading Midtrans Snap JS...');
                                        
                                        // Load Midtrans Snap JS dynamically
                                        const script = document.createElement('script');
                                        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                                        script.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
                                        script.onload = function() {
                                            console.log('✅ Snap JS loaded successfully');
                                            openSnapPopupFixed(tokenData.snap_token, transactionId, legacyBtn, originalText);
                                        };
                                        script.onerror = function() {
                                            console.error('❌ Failed to load Midtrans Snap JS');
                                            Swal.fire({
                                                icon: 'error',
                                                title: '❌ Gagal Memuat Snap JS',
                                                text: 'Gagal memuat library Midtrans Snap',
                                                confirmButtonText: 'OK',
                                                confirmButtonColor: '#f56565'
                                            });
                                            legacyBtn.innerHTML = originalText;
                                            legacyBtn.disabled = false;
                                        };
                                        document.head.appendChild(script);
                                    } else {
                                        console.log('✅ Snap JS already loaded');
                                        openSnapPopupFixed(tokenData.snap_token, transactionId, legacyBtn, originalText);
                                    }
                                } else {
                                    console.error('❌ Failed to get Snap token:', tokenData);
                                    Swal.fire({
                                        icon: 'error',
                                        title: '❌ Gagal Mendapatkan Token',
                                        text: tokenData.message || 'Gagal mendapatkan Snap token',
                                        confirmButtonText: 'Coba Lagi',
                                        confirmButtonColor: '#f56565'
                                    });
                                    legacyBtn.innerHTML = originalText;
                                    legacyBtn.disabled = false;
                                }
                            })
                            .catch(error => {
                                console.error('❌ Error getting Snap token:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: '❌ Gagal Mendapatkan Token',
                                    text: 'Terjadi kesalahan saat mendapatkan token pembayaran',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#f56565'
                                });
                                legacyBtn.innerHTML = originalText;
                                legacyBtn.disabled = false;
                            });
                        } else {
                            console.error('❌ Invalid response:', data);
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Gagal Membuat Pembayaran',
                                text: data.message || 'Gagal membuat pembayaran Snap',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#f56565'
                            });
                            legacyBtn.innerHTML = originalText;
                            legacyBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId); // Clear timeout on error
                        console.error('❌ Fetch error:', error);
                        // Close loading alert
                        Swal.close();
                        
                        // FIXED: Better error handling for different error types
                        let errorTitle = '🚨 Kesalahan Sistem';
                        let errorText = 'Terjadi kesalahan sistem: ' + error.message;
                        
                        if (error.name === 'AbortError') {
                            errorTitle = '⏰ Timeout';
                            errorText = 'Permintaan terlalu lama. Silakan coba lagi atau periksa koneksi internet Anda.';
                        } else if (error.message.includes('HTML instead of JSON')) {
                            errorTitle = '🔐 Masalah Autentikasi';
                            errorText = 'Sesi Anda mungkin telah berakhir. Silakan refresh halaman dan login kembali.';
                        } else if (error.message.includes('Network')) {
                            errorTitle = '🌐 Masalah Jaringan';
                            errorText = 'Periksa koneksi internet Anda dan coba lagi.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            text: errorText,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f56565'
                        });
                        legacyBtn.innerHTML = originalText;
                        legacyBtn.disabled = false;
                    });
                }
            });
        }
        
        // FIXED: Separate function to handle Snap popup
        function openSnapPopupFixed(snapToken, transactionId, button, originalText) {
            try {
                console.log('🚀 Opening Snap popup...');
                
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('✅ Payment success:', result);
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 Pembayaran Berhasil!',
                            text: 'Pembayaran Anda telah berhasil diproses.',
                            confirmButtonText: 'Lihat Detail Pembayaran',
                            confirmButtonColor: '#48bb78'
                        }).then(() => {
                            // Redirect to payment show page
                            const redirectTransactionId = transactionId || 10;
                            window.location.href = '/mahasiswa/payment/' + redirectTransactionId;
                        });
                    },
                    onPending: function(result) {
                        console.log('⏳ Payment pending:', result);
                        Swal.fire({
                            icon: 'info',
                            title: '⏳ Pembayaran Sedang Diproses',
                            text: 'Pembayaran Anda sedang diproses. Silakan cek status pembayaran.',
                            confirmButtonText: 'Lihat Status Pembayaran',
                            confirmButtonColor: '#667eea'
                        }).then(() => {
                            // Redirect to payment show page
                            const redirectTransactionId = transactionId || 10;
                            window.location.href = '/mahasiswa/payment/' + redirectTransactionId;
                        });
                    },
                    onError: function(result) {
                        console.error('❌ Payment error:', result);
                        Swal.fire({
                            icon: 'error',
                            title: '❌ Pembayaran Gagal',
                            text: 'Terjadi kesalahan dalam pembayaran. Silakan coba lagi.',
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#f56565'
                        });
                        button.innerHTML = originalText;
                        button.disabled = false;
                    },
                    onClose: function() {
                        console.log('🔒 Payment popup closed');
                        Swal.fire({
                            icon: 'question',
                            title: '💳 Popup Ditutup',
                            text: 'Popup pembayaran telah ditutup. Apakah Anda ingin memuat ulang halaman untuk melihat status terbaru?',
                            showCancelButton: true,
                            confirmButtonText: '🔄 Muat Ulang',
                            cancelButtonText: '❌ Tidak',
                            confirmButtonColor: '#667eea',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            } else {
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        });
                    }
                });
            } catch (error) {
                console.error('❌ Snap popup error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '❌ Gagal Membuka Popup',
                    text: 'Gagal membuka popup pembayaran: ' + error.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f56565'
                });
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    
        // DYNAMIC REDIRECT FIX - Added by fix_payment_redirect.php
        window.getPaymentRedirectUrl = function(transactionId) {
            // Try to get actual transaction ID from current context
            const urlParams = new URLSearchParams(window.location.search);
            const currentTransactionId = urlParams.get('transaction_id') || transactionId || 10;
            return '/mahasiswa/payment/' + currentTransactionId;
        };
        
        // Override redirect functions
        window.redirectToPaymentShow = function(transactionId) {
            const redirectUrl = window.getPaymentRedirectUrl(transactionId);
            console.log('🔄 Redirecting to:', redirectUrl);
            window.location.href = redirectUrl;
        };
    </script>
@endsection
