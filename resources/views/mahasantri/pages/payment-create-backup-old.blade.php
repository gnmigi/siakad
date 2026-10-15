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
