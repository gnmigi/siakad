@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        .payment-card {
            background: var(--tblr-card-bg, #ffffff);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            margin-bottom: 1.5rem;
            color: var(--tblr-body-color, #1e293b);
        }
        
        /* Dark mode support */
        [data-bs-theme="dark"] .payment-card {
            background: var(--tblr-card-bg, #1e293b);
            border-color: var(--tblr-border-color, #334155);
            color: var(--tblr-body-color, #f1f5f9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .payment-method {
            border: 2px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--tblr-card-bg, #ffffff);
            color: var(--tblr-body-color, #1e293b);
        }
        
        [data-bs-theme="dark"] .payment-method {
            background: var(--tblr-card-bg, #1e293b);
            border-color: var(--tblr-border-color, #334155);
            color: var(--tblr-body-color, #f1f5f9);
        }
        
        .payment-method:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        [data-bs-theme="dark"] .payment-method:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.2);
        }
        
        .payment-method.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.15);
        }
        
        [data-bs-theme="dark"] .payment-method.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.25);
        }
        
        .bank-account {
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--tblr-card-bg, #ffffff);
            color: var(--tblr-body-color, #1e293b);
        }
        
        [data-bs-theme="dark"] .bank-account {
            background: var(--tblr-card-bg, #1e293b);
            border-color: var(--tblr-border-color, #334155);
            color: var(--tblr-body-color, #f1f5f9);
        }
        
        .bank-account:hover {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
        }
        
        [data-bs-theme="dark"] .bank-account:hover {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.2);
        }
        
        .bank-account.selected {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.15);
        }
        
        [data-bs-theme="dark"] .bank-account.selected {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.25);
        }
        
        /* Table styling for dark mode */
        [data-bs-theme="dark"] .table-borderless td {
            color: var(--tblr-body-color, #f1f5f9);
        }
        
        [data-bs-theme="dark"] .text-muted {
            color: var(--tblr-text-muted, #94a3b8) !important;
        }
        
        /* Form controls dark mode */
        [data-bs-theme="dark"] .form-control {
            background-color: var(--tblr-bg-surface, #334155);
            border-color: var(--tblr-border-color, #475569);
            color: var(--tblr-body-color, #f1f5f9);
        }
        
        [data-bs-theme="dark"] .form-control:focus {
            background-color: var(--tblr-bg-surface, #334155);
            border-color: #667eea;
            color: var(--tblr-body-color, #f1f5f9);
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        /* Alert styling for dark mode */
        [data-bs-theme="dark"] .alert-info {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
        }
    </style>
@endsection

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fas fa-credit-card me-2"></i>
                        Pembayaran Tagihan
                    </h2>
                    <div class="text-muted mt-1">Bayar tagihan kuliah Anda dengan mudah dan aman</div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('mahasiswa.payment.checkout-modern', $tagihan->id) }}" class="btn btn-gradient me-2" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none;">
                        <i class="fas fa-rocket me-1"></i>Coba UI Modern
                    </a>
                    <a href="{{ route('mahasiswa.payment.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Tagihan Information -->
            <div class="col-md-4">
                <div class="payment-card">
                    <h3 class="card-title mb-3">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>
                        Detail Tagihan
                    </h3>
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Kode Tagihan</td>
                            <td><strong>{{ $tagihan->code }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deskripsi</td>
                            <td><strong>{{ $tagihan->desc ?? 'Tagihan Kuliah' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah</td>
                            <td><strong class="text-danger">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jatuh Tempo</td>
                            <td>
                                @if($tagihan->due_date)
                                    <strong>{{ \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') }}</strong>
                                @else
                                    <span class="text-muted">Tidak ada tempo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span class="badge bg-warning">{{ $tagihan->status }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Mahasiswa Information -->
                <div class="payment-card">
                    <h3 class="card-title mb-3">
                        <i class="fas fa-user me-2 text-success"></i>
                        Data Mahasiswa
                    </h3>
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td><strong>{{ $mahasiswa->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIM</td>
                            <td><strong>{{ $mahasiswa->numb_nim }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td><strong>{{ $mahasiswa->prodi->name ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Payment Form -->
            <div class="col-md-8">
                <form id="payment-form" action="{{ route('mahasiswa.payment.store') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
                    @csrf
                    
                    <div class="payment-card">
                        <h3 class="card-title mb-3">
                            <i class="fas fa-credit-card me-2 text-info"></i>
                            Pilih Metode Pembayaran
                        </h3>
                        
                        <div class="row">
                            @foreach($paymentMethods as $method)
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" data-method="{{ $method->id }}">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" id="method_{{ $method->id }}" class="d-none">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-{{ $method->icon ?? 'credit-card' }} fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-medium">{{ $method->name }}</div>
                                                <div class="text-muted small">{{ $method->description }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Bank Account Selection (for Transfer) -->
                    <div class="payment-card" id="bank-selection" style="display: none;">
                        <h3 class="card-title mb-3">
                            <i class="fas fa-university me-2 text-success"></i>
                            Pilih Rekening Tujuan
                        </h3>
                        
                        <div class="row">
                            @foreach($bankAccounts as $bank)
                                <div class="col-md-6 mb-3">
                                    <div class="bank-account" data-bank="{{ $bank->id }}">
                                        <input type="radio" name="bank_account_id" value="{{ $bank->id }}" id="bank_{{ $bank->id }}" class="d-none">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="{{ $bank->logo_url ?? '/images/bank-default.png' }}" alt="{{ $bank->bank_name }}" width="40" height="40" class="rounded">
                                            </div>
                                            <div>
                                                <div class="font-weight-medium">{{ $bank->bank_name }}</div>
                                                <div class="text-muted small">{{ $bank->account_number }}</div>
                                                <div class="text-muted small">a.n. {{ $bank->account_name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Payment Instructions -->
                    <div class="payment-card" id="payment-instructions" style="display: none;">
                        <h3 class="card-title mb-3">
                            <i class="fas fa-info-circle me-2 text-warning"></i>
                            Instruksi Pembayaran
                        </h3>
                        <div id="instruction-content">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Upload Bukti Transfer -->
                    <div class="payment-card" id="upload-section" style="display: none;">
                        <h3 class="card-title mb-3">
                            <i class="fas fa-upload me-2 text-info"></i>
                            Upload Bukti Transfer
                        </h3>
                        <div class="mb-3">
                            <label for="transfer_proof" class="form-label">Bukti Transfer <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="transfer_proof" name="transfer_proof" accept="image/*,.pdf">
                            <div class="form-text">Upload foto atau scan bukti transfer (JPG, PNG, PDF, max 5MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="payment-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Total Pembayaran</div>
                                <div class="h3 text-danger mb-0">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg" id="submit-payment" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Proses Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <!-- Midtrans Snap.js Script -->
    <script type="text/javascript"
            src="{{ config('midtrans.is_production') ? config('midtrans.production_snap_url') : config('midtrans.sandbox_snap_url') }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('.payment-method');
            const bankAccounts = document.querySelectorAll('.bank-account');
            const bankSelection = document.getElementById('bank-selection');
            const paymentInstructions = document.getElementById('payment-instructions');
            const uploadSection = document.getElementById('upload-section');
            const submitButton = document.getElementById('submit-payment');
            const instructionContent = document.getElementById('instruction-content');
            const paymentForm = document.getElementById('payment-form');
            
            let selectedMethod = null;
            let selectedMethodType = null;
            let selectedBank = null;
            
            // Get payment methods data from backend
            const paymentMethodsData = @json($paymentMethods);
            
            // Payment method selection
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input[type="radio"]').checked = true;
                    
                    selectedMethod = this.dataset.method;
                    
                    // Find method type from data
                    const methodData = paymentMethodsData.find(m => m.id == selectedMethod);
                    selectedMethodType = methodData ? methodData.type : null;
                    
                    // Show/hide bank selection based on method type
                    if (selectedMethodType === 'bank_transfer') {
                        // Manual transfer - perlu pilih bank dan upload bukti
                        bankSelection.style.display = 'block';
                        uploadSection.style.display = 'block';
                        paymentInstructions.style.display = 'none';
                    } else if (selectedMethodType === 'midtrans_snap') {
                        // Midtrans Snap - langsung proses, tidak perlu pilih bank
                        bankSelection.style.display = 'none';
                        uploadSection.style.display = 'none';
                        paymentInstructions.style.display = 'block';
                        selectedBank = null;
                        
                        // Show Midtrans Snap instructions
                        instructionContent.innerHTML = 
                            '<div class="alert alert-success">' +
                                '<h5><i class="fas fa-rocket me-2"></i>Pembayaran Online (Midtrans Snap):</h5>' +
                                '<div class="row">' +
                                    '<div class="col-md-6">' +
                                        '<h6>🏦 Virtual Account:</h6>' +
                                        '<ul class="small">' +
                                            '<li>BCA, Mandiri, BNI, BRI</li>' +
                                            '<li>Permata, CIMB, Danamon</li>' +
                                            '<li>Maybank, OCBC, Panin</li>' +
                                            '<li>BTN, BSI, Muamalat</li>' +
                                            '<li>Dan 50+ bank lainnya</li>' +
                                        '</ul>' +
                                    '</div>' +
                                    '<div class="col-md-6">' +
                                        '<h6>💳 E-Wallet & Kartu:</h6>' +
                                        '<ul class="small">' +
                                            '<li>GoPay, OVO, DANA</li>' +
                                            '<li>ShopeePay, LinkAja</li>' +
                                            '<li>Visa, MasterCard, JCB</li>' +
                                            '<li>Indomaret, Alfamart</li>' +
                                            '<li>Internet Banking</li>' +
                                        '</ul>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="mt-3 p-3 bg-light rounded">' +
                                    '<strong>✨ Keuntungan:</strong>' +
                                    '<div class="row mt-2">' +
                                        '<div class="col-md-6">' +
                                            '<ul class="small mb-0">' +
                                                '<li>✅ Pilihan bank terlengkap</li>' +
                                                '<li>✅ Otomatis terverifikasi</li>' +
                                                '<li>✅ Tersedia 24/7</li>' +
                                            '</ul>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                            '<ul class="small mb-0">' +
                                                '<li>✅ Aman & mudah</li>' +
                                                '<li>✅ Tidak perlu upload bukti</li>' +
                                                '<li>✅ Proses instan</li>' +
                                            '</ul>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="mt-3 text-center">' +
                                    '<strong class="text-primary">Klik "Proses Pembayaran" untuk melanjutkan ke Midtrans</strong>' +
                                '</div>' +
                            '</div>';
                    } else {
                        // Other methods
                        bankSelection.style.display = 'none';
                        uploadSection.style.display = 'none';
                        paymentInstructions.style.display = 'none';
                        selectedBank = null;
                    }
                    
                    updateSubmitButton();
                });
            });
            
            // Bank account selection
            bankAccounts.forEach(bank => {
                bank.addEventListener('click', function() {
                    bankAccounts.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input[type="radio"]').checked = true;
                    
                    selectedBank = this.dataset.bank;
                    showPaymentInstructions();
                    updateSubmitButton();
                });
            });
            
            function showPaymentInstructions() {
                if (selectedBank && selectedMethodType) {
                    const bankData = @json($bankAccounts);
                    const bank = bankData.find(b => b.id == selectedBank);
                    const tagihanAmount = '{{ number_format($tagihan->amount, 0, ",", ".") }}';
                    
                    if (selectedMethodType === 'virtual_account') {
                        // Virtual Account instructions
                        instructionContent.innerHTML = 
                            '<div class="alert alert-info">' +
                                '<h5><i class="fas fa-info-circle me-2"></i>Cara Pembayaran Virtual Account:</h5>' +
                                '<ol>' +
                                    '<li>Klik <strong>"Proses Pembayaran"</strong> untuk mendapatkan nomor Virtual Account</li>' +
                                    '<li>Anda akan mendapatkan nomor Virtual Account <strong>' + bank.bank_name + '</strong></li>' +
                                    '<li>Bayar melalui ATM, Mobile Banking, atau Internet Banking</li>' +
                                    '<li>Masukkan nomor Virtual Account yang diberikan</li>' +
                                    '<li>Konfirmasi pembayaran sebesar <strong>Rp ' + tagihanAmount + '</strong></li>' +
                                    '<li>Pembayaran akan otomatis terverifikasi</li>' +
                                '</ol>' +
                                '<div class="mt-3">' +
                                    '<strong>Keuntungan Virtual Account:</strong>' +
                                    '<ul>' +
                                        '<li>✅ Pembayaran otomatis terverifikasi</li>' +
                                        '<li>✅ Tidak perlu upload bukti transfer</li>' +
                                        '<li>✅ Tersedia 24/7</li>' +
                                        '<li>✅ Aman dan mudah</li>' +
                                    '</ul>' +
                                '</div>' +
                            '</div>';
                    } else if (selectedMethodType === 'bank_transfer') {
                        // Manual Bank Transfer instructions
                        instructionContent.innerHTML = 
                            '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Cara Transfer Manual:</h5>' +
                                '<ol>' +
                                    '<li>Transfer ke rekening <strong>' + bank.bank_name + '</strong></li>' +
                                    '<li>Nomor rekening: <strong>' + bank.account_number + '</strong></li>' +
                                    '<li>Atas nama: <strong>' + bank.account_name + '</strong></li>' +
                                    '<li>Jumlah: <strong>Rp ' + tagihanAmount + '</strong></li>' +
                                    '<li>Upload bukti transfer di bawah ini</li>' +
                                    '<li>Tunggu verifikasi dari admin (1x24 jam)</li>' +
                                '</ol>' +
                                '<div class="mt-3">' +
                                    '<strong>Catatan:</strong>' +
                                    '<ul>' +
                                        '<li>⚠️ Memerlukan verifikasi manual</li>' +
                                        '<li>⚠️ Proses verifikasi 1x24 jam</li>' +
                                        '<li>⚠️ Wajib upload bukti transfer</li>' +
                                    '</ul>' +
                                '</div>' +
                            '</div>';
                    }
                    
                    paymentInstructions.style.display = 'block';
                }
            }
            
            function updateSubmitButton() {
                // Enable submit button based on method type
                if (selectedMethodType === 'bank_transfer') {
                    // Bank transfer needs method and bank selection
                    submitButton.disabled = !selectedMethod || !selectedBank;
                } else if (selectedMethodType === 'virtual_account') {
                    // Virtual Account needs method and bank selection
                    submitButton.disabled = !selectedMethod || !selectedBank;
                } else if (selectedMethod) {
                    // Other methods (e-wallet, credit card, etc.) only need method selection
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }
            }
            
            // Handle form submission for Midtrans Snap
            paymentForm.addEventListener('submit', function(e) {
                if (selectedMethodType === 'midtrans_snap') {
                    e.preventDefault(); // Prevent normal form submission
                    
                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                    
                    console.log('🚀 Starting Midtrans Snap payment process...');
                    
                    // Create payment transaction first via normal form submission
                    const formData = new FormData(paymentForm);
                    
                    fetch(paymentForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('📡 Payment creation response received:', response.status);
                        
                        if (response.redirected) {
                            // Payment created successfully, extract transaction ID from redirect URL
                            const redirectUrl = response.url;
                            const urlParts = redirectUrl.split('/');
                            const transactionId = urlParts[urlParts.length - 1];
                            
                            console.log('✅ Payment created, transaction ID:', transactionId);
                            console.log('🔍 Getting Snap token...');
                            
                            // Get Snap token from the new endpoint
                            return fetch(`/mahasiswa/payment/${transactionId}/snap-token`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(tokenResponse => {
                                if (!tokenResponse.ok) {
                                    throw new Error(`HTTP ${tokenResponse.status}: ${tokenResponse.statusText}`);
                                }
                                return tokenResponse.json();
                            })
                            .then(tokenData => {
                                console.log('📋 Token response:', tokenData);
                                
                                if (tokenData.success && tokenData.snap_token) {
                                    return {
                                        snap_token: tokenData.snap_token,
                                        transaction_id: transactionId
                                    };
                                } else {
                                    throw new Error(tokenData.message || 'Failed to get Snap token');
                                }
                            });
                        } else {
                            // Handle non-redirect response (error case)
                            return response.json().then(data => {
                                throw new Error(data.message || 'Payment creation failed');
                            });
                        }
                    })
                    .then(data => {
                        console.log('🎯 Ready to trigger Snap popup');
                        console.log('- Transaction ID:', data.transaction_id);
                        console.log('- Token length:', data.snap_token.length);
                        console.log('- Token preview:', data.snap_token.substring(0, 20) + '...');
                        
                        // Verify Snap script is loaded
                        if (typeof snap === 'undefined') {
                            throw new Error('Midtrans Snap script not loaded. Please refresh the page.');
                        }
                        
                        console.log('🚀 Triggering Midtrans Snap popup...');
                        
                        // Trigger Midtrans Snap popup
                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                console.log('✅ Payment success:', result);
                                alert('🎉 Pembayaran berhasil!\n\nAnda akan diarahkan ke halaman konfirmasi.');
                                window.location.href = `/mahasiswa/payment/${data.transaction_id}`;
                            },
                            onPending: function(result) {
                                console.log('⏳ Payment pending:', result);
                                alert('⏳ Pembayaran sedang diproses.\n\nAnda akan diarahkan ke halaman detail pembayaran.');
                                window.location.href = `/mahasiswa/payment/${data.transaction_id}`;
                            },
                            onError: function(result) {
                                console.error('❌ Payment error:', result);
                                alert('❌ Terjadi kesalahan dalam pembayaran.\n\nSilakan coba lagi atau hubungi admin.');
                                // Reset button
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Proses Pembayaran';
                            },
                            onClose: function() {
                                console.log('🔒 Payment popup closed by user');
                                alert('🔒 Pembayaran dibatalkan.\n\nAnda dapat mencoba lagi kapan saja.');
                                // Reset button
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Proses Pembayaran';
                            }
                        });
                    })
                    .catch(error => {
                        console.error('❌ Payment process error:', error);
                        
                        let errorMessage = 'Terjadi kesalahan dalam proses pembayaran.';
                        if (error.message.includes('Snap token not received')) {
                            errorMessage = 'Token pembayaran tidak diterima. Silakan coba lagi.';
                        } else if (error.message.includes('not loaded')) {
                            errorMessage = 'Script pembayaran belum dimuat. Silakan refresh halaman.';
                        } else if (error.message) {
                            errorMessage = error.message;
                        }
                        
                        alert('❌ Error: ' + errorMessage + '\n\nSilakan coba lagi atau hubungi admin jika masalah berlanjut.');
                        
                        // Reset button
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Proses Pembayaran';
                    });
                }
                // For other payment methods, let the form submit normally
            });
        });
    </script>
@endsection
