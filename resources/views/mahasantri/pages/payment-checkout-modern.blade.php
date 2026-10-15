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

        /* Modern Container */
        .modern-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Payment Card */
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
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

        /* Header Section */
        .checkout-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .checkout-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .checkout-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
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

        /* Payment Methods Grid */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .payment-method-card {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        [data-bs-theme="dark"] .security-badge {
            color: var(--gray-400);
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
    </style>
@endsection

@section('content')
    <div class="modern-container">
        <!-- Header -->
        <div class="checkout-header fade-in">
            <h1 class="checkout-title">💳 Checkout Pembayaran</h1>
            <p class="checkout-subtitle">Pilih metode pembayaran yang paling nyaman untuk Anda</p>
        </div>

        <div class="row">
            <!-- Bill Summary -->
            <div class="col-lg-4 mb-4">
                <div class="payment-card fade-in">
                    <div class="bill-summary">
                        <div class="text-center">
                            <div class="bill-amount">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>
                            <div class="bill-description">{{ $tagihan->desc ?? 'Pembayaran Akademik' }}</div>
                        </div>
                    </div>

                    <!-- Student Info -->
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
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                <span class="badge bg-warning">{{ $tagihan->status }}</span>
                            </div>
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
                                <div class="bank-icon">PMT</div>
                                <div class="bank-icon">CIMB</div>
                                <div class="bank-icon">BTN</div>
                                <div class="bank-icon">BSI</div>
                                <div class="bank-icon">+15</div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="payment-method-card" data-method="e_wallet">
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
                        </div>

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
                        <div class="payment-method-card" data-method="credit_card">
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
                        </div>
                    </div>

                    <!-- Bank Selection (for Virtual Account) -->
                    <div id="bank-selection" style="display: none;" class="slide-up">
                        <h6 class="fw-bold mb-3">🏛️ Pilih Bank Virtual Account</h6>
                        <div class="row">
                            <!-- Major Banks -->
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="bca_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>BCA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="mandiri_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Mandiri</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="bni_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>BNI</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="bri_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>BRI</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Permata</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="cimb_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>CIMB Niaga</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Banks -->
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="bsi_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>BSI (Bank Syariah Indonesia)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="bjb_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>BJB (Bank Jabar Banten)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="banten_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Banten</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="dki_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank DKI</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="jateng_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Jateng</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="jatim_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Jatim</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="sumut_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Sumut</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="sumsel_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Sumsel Babel</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="kalbar_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Kalbar</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="kaltim_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Kaltim</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="sulselbar_va">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">🏦</div>
                                        <span>Bank Sulselbar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E-Wallet Selection -->
                    <div id="ewallet-selection" style="display: none;" class="slide-up">
                        <h6 class="fw-bold mb-3">📱 Pilih E-Wallet</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="bank-option" data-bank="gopay">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="me-2">📱</div>
                                        <span>GoPay</span>
                                    </div>
                                </div>
                            </div>
                            <!-- OVO, DANA, ShopeePay, LinkAja removed - not supported in Core API -->
                            <div class="col-md-8 mb-2">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Info:</strong> Untuk e-wallet lainnya (OVO, DANA, ShopeePay, LinkAja), gunakan "Metode Pembayaran Alternatif" di bawah.
                                </div>
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
                        
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Pembayaran Aman & Terenkripsi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedMethod = null;
            let selectedBank = null;
            
            const methodCards = document.querySelectorAll('.payment-method-card');
            const bankOptions = document.querySelectorAll('.bank-option');
            const checkoutBtn = document.getElementById('checkout-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            
            // Payment method selection
            methodCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove previous selections
                    methodCards.forEach(c => c.classList.remove('selected'));
                    document.getElementById('bank-selection').style.display = 'none';
                    document.getElementById('ewallet-selection').style.display = 'none';
                    
                    // Select current card
                    this.classList.add('selected');
                    selectedMethod = this.dataset.method;
                    selectedBank = null;
                    
                    console.log('💳 Payment method selected:', {
                        method: selectedMethod,
                        element: this.dataset.method
                    });
                    
                    // Show appropriate sub-selection
                    if (selectedMethod === 'virtual_account') {
                        document.getElementById('bank-selection').style.display = 'block';
                        btnText.textContent = 'Pilih Bank Virtual Account';
                        checkoutBtn.disabled = true;
                    } else if (selectedMethod === 'e_wallet') {
                        document.getElementById('ewallet-selection').style.display = 'block';
                        btnText.textContent = 'Pilih E-Wallet';
                        checkoutBtn.disabled = true;
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
                    
                    console.log('🏦 Bank selected:', {
                        method: selectedMethod,
                        bank: selectedBank,
                        element: this.dataset.bank
                    });
                    
                    // Enable checkout button
                    btnText.textContent = `Bayar dengan ${getBankName(selectedBank)}`;
                    checkoutBtn.disabled = false;
                });
            });
            
            // Checkout button click
            checkoutBtn.addEventListener('click', function() {
                if (!selectedMethod || !selectedBank) {
                    alert('Silakan pilih metode pembayaran terlebih dahulu');
                    return;
                }
                
                // Show loading
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
                checkoutBtn.disabled = true;
                
                // Create transaction
                createTransaction();
            });
            
            function createTransaction() {
                console.log('🔧 Creating transaction with:', {
                    selectedMethod: selectedMethod,
                    selectedBank: selectedBank
                });
                
                const formData = new FormData();
                formData.append('tagihan_id', '{{ $tagihan->id }}');
                formData.append('payment_method_type', selectedMethod);
                formData.append('selected_bank', selectedBank);
                formData.append('_token', '{{ csrf_token() }}');
                
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
                        // Redirect to status page
                        window.location.href = data.redirect_url;
                    } else {
                        // Show error
                        alert('Error: ' + data.message);
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem. Silakan coba lagi.');
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
                    'credit_card': 'Kartu Kredit'
                };
                return names[method] || method;
            }
            
            function getBankName(bank) {
                const names = {
                    // Major Banks
                    'bca_va': 'BCA Virtual Account',
                    'mandiri_va': 'Mandiri Virtual Account',
                    'bni_va': 'BNI Virtual Account',
                    'bri_va': 'BRI Virtual Account',
                    'cimb_va': 'CIMB Virtual Account',
                    
                    // Additional Banks
                    'bsi_va': 'BSI Virtual Account',
                    'bjb_va': 'BJB Virtual Account',
                    'banten_va': 'Bank Banten Virtual Account',
                    'dki_va': 'Bank DKI Virtual Account',
                    'jateng_va': 'Bank Jateng Virtual Account',
                    'jatim_va': 'Bank Jatim Virtual Account',
                    'sumut_va': 'Bank Sumut Virtual Account',
                    'sumsel_va': 'Bank Sumsel Babel Virtual Account',
                    'kalbar_va': 'Bank Kalbar Virtual Account',
                    'kaltim_va': 'Bank Kaltim Virtual Account',
                    'sulselbar_va': 'Bank Sulselbar Virtual Account',
                    
                    // E-Wallets
                    'gopay': 'GoPay',
                    'ovo': 'OVO',
                    'dana': 'DANA',
                    'shopeepay': 'ShopeePay',
                    'linkaja': 'LinkAja',
                    
                    // Other Methods
                    'qris': 'QRIS',
                    'credit_card': 'Kartu Kredit'
                };
                return names[bank] || bank;
            }
        });
    </script>
@endsection
