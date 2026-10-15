@extends('central.backpage.core-backpage-mahasiswa')

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
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') }}</span>
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
                        
                        <div class="payment-methods-grid">
                            <!-- Virtual Account -->
                            <div class="payment-method-card" data-method="virtual_account">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                        🏦
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">Virtual Account</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Transfer melalui ATM, Mobile Banking, atau Internet Banking. Otomatis terverifikasi dalam hitungan menit.
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">BCA</div>
                                    <div class="bank-icon">BNI</div>
                                    <div class="bank-icon">BRI</div>
                                    <div class="bank-icon">MDR</div>
                                    <div class="bank-icon">CIMB</div>
                                    <div class="bank-icon">BSI</div>
                                  
                                </div>
                            </div>

                            <!-- E-Wallet -->
                            <!-- <div class="payment-method-card" data-method="e_wallet">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #059669, #10b981);">
                                        📱
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">E-Wallet</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Bayar dengan dompet digital favorit Anda. Cepat, mudah, dan aman dengan QR Code atau deep link.
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">GPY</div>
                                    <div class="bank-icon">OVO</div>
                                    <div class="bank-icon">DNA</div>
                                    <div class="bank-icon">SPY</div>
                                    <div class="bank-icon">LNK</div>
                                </div>
                            </div> -->

                            <!-- QRIS -->
                            <div class="payment-method-card" data-method="qris">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                                        📷
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">QRIS</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Scan QR Code dengan aplikasi e-wallet atau mobile banking apapun. Universal dan praktis.
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">QR</div>
                                    <div class="bank-icon">ALL</div>
                                </div>
                            </div>

                            <!-- Credit Card -->
                            <!-- <div class="payment-method-card" data-method="credit_card">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #7c2d12, #ea580c);">
                                        💳
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">Kartu Kredit</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Bayar dengan kartu kredit Visa, MasterCard, atau JCB. Aman dengan teknologi 3D Secure.
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">VISA</div>
                                    <div class="bank-icon">MC</div>
                                    <div class="bank-icon">JCB</div>
                                </div>
                            </div> -->

                            <!-- Manual Bank Transfer -->
                            <div class="payment-method-card" data-method="bank_transfer">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #0891b2, #0e7490);">
                                        🏛️
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">Transfer Bank Manual</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Transfer manual ke rekening bank. Perlu upload bukti transfer dan verifikasi admin (1x24 jam).
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">Manual</div>
                                    <div class="bank-icon">Upload</div>
                                </div>
                            </div>

                            <!-- Cash Payment -->
                            <div class="payment-method-card" data-method="cash">
                                <div class="payment-method-header">
                                    <div class="payment-method-icon" style="background: linear-gradient(135deg, #16a34a, #15803d);">
                                        💵
                                    </div>
                                    <div>
                                        <h6 class="payment-method-title">Pembayaran Tunai</h6>
                                    </div>
                                </div>
                                <p class="payment-method-description">
                                    Bayar langsung di bagian keuangan kampus. Buka jam kerja dan bawa bukti tagihan.
                                </p>
                                <div class="bank-icons">
                                    <div class="bank-icon">Cash</div>
                                    <div class="bank-icon">Office</div>
                                </div>
                            </div>
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
                    <div class="legacy-section">
                        <h6 class="fw-bold mb-2">🔄 Metode Pembayaran Alternatif</h6>
                        <p class="mb-3 small">Jika metode di atas tidak tersedia, gunakan sistem pembayaran lama dengan Midtrans Snap:</p>
                        <button type="button" class="legacy-button" onclick="useLegacyPayment()">
                            <i class="fas fa-credit-card me-2"></i>
                            Gunakan Midtrans Snap
                        </button>
                        <small class="d-block mt-2 text-muted">
                            Mendukung semua bank, e-wallet, dan kartu kredit melalui popup Midtrans
                        </small>
                    </div>
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
        // Get payment amount from PHP
        @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
            @php
                $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
            @endphp
            var PAYMENT_AMOUNT = '{{ $remainingAmount }}';
            var IS_INSTALLMENT = true;
        @else
            var PAYMENT_AMOUNT = '{{ $tagihan->amount }}';
            var IS_INSTALLMENT = false;
        @endif
        
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
                    
                    // Show appropriate sub-selection
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
                    } else {
                        // QRIS or Credit Card - no sub-selection needed
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
        
        // PRODUCTION FIX: Enhanced Legacy Midtrans Snap function with better error handling
        function useLegacyPayment() {
            Swal.fire({
                title: '💳 Midtrans Snap Payment',
                html: `
                    <div class="text-start">
                        <p><strong>Menggunakan sistem pembayaran Midtrans Snap?</strong></p>
                        <div class="mt-3">
                            <p class="mb-2">✅ Mendukung semua bank dan e-wallet</p>
                            <p class="mb-2">✅ Kartu kredit (Visa, MasterCard, JCB)</p>
                            <p class="mb-2">✅ Interface yang familiar dan aman</p>
                            <p class="mb-0">✅ Proses pembayaran yang cepat</p>
                        </div>
                    </div>
                `,
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
            }).then((result) => {
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
                    
                    // PRODUCTION FIX: Enhanced CSRF token handling
                    let csrfToken = '{{ csrf_token() }}';
                    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                    if (metaCsrf) {
                        csrfToken = metaCsrf.getAttribute('content');
                    }
                    
                    // Create Snap payment via AJAX with enhanced error handling
                    const formData = new FormData();
                    formData.append('tagihan_id', '{{ $tagihan->id }}');
                    formData.append('payment_method_id', '9'); // Midtrans Snap method ID (Dynamic)
                    formData.append('_token', csrfToken);
                    
                    console.log('🚀 Starting Snap payment request...');
                    
                    // PRODUCTION FIX: Enhanced fetch with timeout and retry mechanism
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
                    
                    fetch('{{ route("mahasiswa.payment.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        signal: controller.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        console.log('📡 Response received:', response.status);
                        
                        // PRODUCTION FIX: Handle different HTTP status codes
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Validation error (422). Please refresh the page and try again.');
                            });
                        } else if (response.status === 419) {
                            throw new Error('Session expired (419). Please refresh the page and try again.');
                        } else if (response.status === 500) {
                            throw new Error('Server error (500). Please try again in a few minutes.');
                        } else if (!response.ok) {
                            throw new Error('Network response was not ok: HTTP ' + response.status);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('📦 Response data:', data);
                        
                        // Close loading alert
                        Swal.close();
                        
                        if (data.success && data.snap_token) {
                            console.log('✅ Snap token received, opening popup...');
                            
                            // Check if snap is available
                            if (typeof window.snap === 'undefined') {
                                throw new Error('Midtrans Snap library not loaded. Please refresh the page.');
                            }
                            
                            // Show Midtrans Snap popup
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    console.log('✅ Payment success:', result);
                                    Swal.fire({
                                        icon: 'success',
                                        title: '🎉 Pembayaran Berhasil!',
                                        text: 'Pembayaran Anda telah berhasil diproses.',
                                        confirmButtonText: 'Lihat Riwayat',
                                        confirmButtonColor: '#48bb78'
                                    }).then(() => {
                                        window.location.href = '{{ route("mahasiswa.payment.index") }}';
                                    });
                                },
                                onPending: function(result) {
                                    console.log('⏳ Payment pending:', result);
                                    Swal.fire({
                                        icon: 'info',
                                        title: '⏳ Pembayaran Sedang Diproses',
                                        text: 'Pembayaran Anda sedang diproses. Silakan cek status pembayaran.',
                                        confirmButtonText: 'Cek Status',
                                        confirmButtonColor: '#667eea'
                                    }).then(() => {
                                        window.location.href = '{{ route("mahasiswa.payment.index") }}';
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
                                    // Reset button
                                    legacyBtn.innerHTML = originalText;
                                    legacyBtn.disabled = false;
                                },
                                onClose: function() {
                                    console.log('🔒 Payment popup closed');
                                    // Show SweetAlert asking if user wants to refresh
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
                                            // Refresh the page
                                            window.location.reload();
                                        } else {
                                            // Reset button if user chooses not to refresh
                                            legacyBtn.innerHTML = originalText;
                                            legacyBtn.disabled = false;
                                        }
                                    });
                                }
                            });
                        } else if (data.redirect_url) {
                            console.log('🔄 Redirecting to:', data.redirect_url);
                            // If no snap token but has redirect URL, redirect to payment page
                            window.location.href = data.redirect_url;
                        } else {
                            console.error('❌ Invalid response:', data);
                            
                            // PRODUCTION FIX: Better error message handling
                            let errorMessage = 'Gagal membuat pembayaran Snap';
                            if (data.message) {
                                errorMessage = data.message;
                            } else if (data.error_code === 'SNAP_TOKEN_GENERATION_FAILED') {
                                errorMessage = 'Sistem pembayaran sedang mengalami gangguan. Silakan coba lagi dalam beberapa menit.';
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Gagal Membuat Pembayaran',
                                text: errorMessage,
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#f56565'
                            });
                            legacyBtn.innerHTML = originalText;
                            legacyBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        console.error('❌ Fetch error:', error);
                        
                        // Close loading alert
                        Swal.close();
                        
                        // PRODUCTION FIX: Better error categorization
                        let errorTitle = '🚨 Kesalahan Sistem';
                        let errorMessage = 'Terjadi kesalahan sistem: ' + error.message;
                        
                        if (error.name === 'AbortError') {
                            errorTitle = '⏰ Timeout';
                            errorMessage = 'Permintaan timeout. Silakan periksa koneksi internet Anda dan coba lagi.';
                        } else if (error.message.includes('422')) {
                            errorTitle = '🔒 Masalah Validasi';
                            errorMessage = 'Terjadi masalah validasi. Silakan refresh halaman dan coba lagi.';
                        } else if (error.message.includes('419')) {
                            errorTitle = '🔑 Sesi Berakhir';
                            errorMessage = 'Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.';
                        } else if (error.message.includes('500')) {
                            errorTitle = '🔧 Server Error';
                            errorMessage = 'Server sedang mengalami gangguan. Silakan coba lagi dalam beberapa menit.';
                        } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                            errorTitle = '🌐 Masalah Koneksi';
                            errorMessage = 'Tidak dapat terhubung ke server. Silakan periksa koneksi internet Anda.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            text: errorMessage,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f56565'
                        });
                        legacyBtn.innerHTML = originalText;
                        legacyBtn.disabled = false;
                    });
                }
            });
        }
        
        // PRODUCTION FIX: Add network connectivity check
        function checkNetworkConnectivity() {
            return new Promise((resolve) => {
                if (!navigator.onLine) {
                    resolve(false);
                    return;
                }
                
                // Try to fetch a small resource to test connectivity
                fetch('/favicon.ico', { 
                    method: 'HEAD',
                    cache: 'no-cache',
                    mode: 'no-cors'
                })
                .then(() => resolve(true))
                .catch(() => resolve(false));
            });
        }
        
        // PRODUCTION FIX: Add page visibility change handler to refresh CSRF token
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Page became visible, refresh CSRF token if needed
                const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                if (metaCsrf) {
                    fetch('/csrf-token', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.token) {
                            metaCsrf.setAttribute('content', data.token);
                            console.log('🔄 CSRF token refreshed');
                        }
                    })
                    .catch(error => {
                        console.log('⚠️ Could not refresh CSRF token:', error);
                    });
                }
            }
        });
    </script>
@endsection
