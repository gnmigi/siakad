@extends('core-themes.core-backpage')

@php
    $menus = 'Master Keuangan';
    $pages = 'Pembayaran Manual';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                        <path d="M3 3v18h18" />
                        <path d="M18.7 8a3 3 0 0 0 -5.4 0" />
                        <path d="M13.3 16a3 3 0 0 0 5.4 0" />
                        <path d="M9 10l2 2l4 -4" />
                    </svg>
                    Pembayaran Manual
                </h3>
                <div class="btn-list">
                    <a href="{{ route('keuangan.pembayaran-manual-index') }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M3 12a9 9 0 1 0 9 -9a9.75 9.75 0 0 0 -6.74 2.74l-1.26 1.26" />
                            <path d="M3 3v6h6" />
                            <path d="M12 7v5l4 2" />
                        </svg>
                        Riwayat Pembayaran
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Student Search Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Cari Mahasiswa</label>
                            <div class="position-relative">
                                <input type="text" id="studentSearch" class="form-control form-control-lg" 
                                       placeholder="Cari berdasarkan nama, NIM, atau ID santri..." autocomplete="off">
                                <div id="studentDropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                    <!-- Student search results will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Info Section (Hidden by default) -->
                <div id="studentInfoSection" class="row mb-4" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="row g-2">
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <strong>Nama Lengkap:</strong><br>
                                    <span id="studentName" class="text-break">-</span>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                                    <strong>ID Santri:</strong><br>
                                    <span id="studentId">-</span>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                                    <strong>NIM:</strong><br>
                                    <span id="studentNim">-</span>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <strong>Program Studi:</strong><br>
                                    <span id="studentProdi" class="text-break">-</span>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-12">
                                    <strong>Total Tagihan:</strong><br>
                                    <span id="totalTagihan" class="text-danger fw-bold">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills Table Section (Hidden by default) -->
                <div id="billsSection" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Daftar Tagihan</h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllBills()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M9 12l2 2l4 -4" />
                                </svg>
                                Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllBills()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Bersihkan
                            </button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-lg-6 col-md-12 col-12">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <label class="form-label mb-0 text-nowrap">Tampilkan:</label>
                                <select id="perPageSelect" class="form-select form-select-sm" style="width: auto; min-width: 80px;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-muted small" id="paginationInfo">-</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-12">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-lg-end">
                                <label class="form-label mb-0 text-nowrap">Urutkan:</label>
                                <select id="sortSelect" class="form-select form-select-sm" style="width: auto; min-width: 200px;">
                                    <option value="status">Status (Belum Bayar Dulu)</option>
                                    <option value="due_date">Tanggal Jatuh Tempo</option>
                                    <option value="amount">Jumlah Tagihan</option>
                                    <option value="desc">Nama Tagihan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-vcenter" id="billsTable">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                    </th>
                                    <th>No Tagihan</th>
                                    <th>Kategori</th>
                                    <th>Nama Tagihan</th>
                                    <th>Jumlah Tagihan</th>
                                    <th>Sisa Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="billsTableBody">
                                <!-- Bills will be populated here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-lg-none" id="billsCardContainer">
                        <!-- Mobile cards will be populated here -->
                    </div>

                    <!-- Tablet Horizontal Scroll View -->
                    <div class="table-responsive d-lg-none d-md-block d-none">
                        <table class="table table-vcenter table-mobile" id="billsTableMobile">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckboxMobile" class="form-check-input">
                                    </th>
                                    <th style="min-width: 120px;">No Tagihan</th>
                                    <th style="min-width: 100px;">Kategori</th>
                                    <th style="min-width: 200px;">Nama Tagihan</th>
                                    <th style="min-width: 130px;">Jumlah</th>
                                    <th style="min-width: 130px;">Sisa</th>
                                    <th style="min-width: 100px;">Status</th>
                                    <th style="min-width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="billsTableBodyMobile">
                                <!-- Bills will be populated here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationSection" class="d-flex justify-content-between align-items-center mt-3" style="display: none;">
                        <div class="text-muted">
                            <span id="paginationText">-</span>
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <!-- Pagination buttons will be populated here -->
                            </ul>
                        </nav>
                    </div>

                    <!-- Payment Form Section -->
                    <div id="paymentFormSection" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Form Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <form id="paymentForm">
                                    <input type="hidden" id="selectedMahasiswaId" name="mahasiswa_id">
                                    <input type="hidden" id="selectedTagihanIds" name="tagihan_ids">
                                    
                                    <div class="row g-3">
                                        <div class="col-lg-6 col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required">Metode Pembayaran</label>
                                                <select name="payment_method_id" id="paymentMethodSelect" class="form-select" required>
                                                    <option value="">Pilih Metode Pembayaran...</option>
                                                    @foreach($paymentMethods as $method)
                                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Rekening Bank</label>
                                                <select name="bank_account_id" id="bankAccountSelect" class="form-select">
                                                    <option value="">Pilih Rekening Bank (Opsional)...</option>
                                                    @foreach($bankAccounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->account_name }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label required">Tanggal Pembayaran</label>
                                                <input type="date" name="payment_date" id="paymentDate" class="form-control" 
                                                       value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required">Jumlah Dibayar</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="paid_amount" id="paidAmount" class="form-control currency-input" 
                                                           placeholder="0" required>
                                                    <input type="hidden" name="paid_amount_raw" id="paidAmountRaw">
                                                </div>
                                                <small class="form-hint">
                                                    Total tagihan terpilih: <span id="selectedBillsTotal" class="fw-bold text-primary">Rp 0</span>
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Referensi Pembayaran</label>
                                                <input type="text" name="payment_reference" id="paymentReference" class="form-control" 
                                                       placeholder="Nomor referensi, kode transfer, dll">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bukti Pembayaran</label>
                                                <input type="file" name="payment_proof" id="paymentProof" class="form-control" 
                                                       accept=".jpg,.jpeg,.png,.pdf">
                                                <small class="form-hint">Format: JPG, PNG, PDF. Maksimal 5MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea name="notes" id="paymentNotes" class="form-control" rows="3" 
                                                  placeholder="Catatan tambahan tentang pembayaran ini..."></textarea>
                                    </div>

                                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="resetPaymentForm()">Reset</button>
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                            Proses Pembayaran
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5">
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('dashboard/static/illustrations/undraw_searching_p5ux.svg') }}" height="128" alt="">
                        </div>
                        <p class="empty-title">Cari Mahasiswa</p>
                        <p class="empty-subtitle text-muted">
                            Mulai dengan mencari mahasiswa berdasarkan nama, NIM, atau ID santri untuk melihat tagihan dan memproses pembayaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card me-2"></i>
                    Pembayaran Manual
                </h5>
                <button type="button" class="btn-close" onclick="closePaymentModal()" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <!-- Student Info -->
                <div class="alert alert-info mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama:</strong> <span id="modalStudentName">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>NIM:</strong> <span id="modalStudentNim">-</span>
                        </div>
                    </div>
                </div>

                <!-- Bill Info -->
                <div class="mb-3">
                    <h6>Tagihan yang akan dibayar:</h6>
                    <div id="modalBillInfo" class="border rounded p-3">
                        <!-- Bill info will be populated here -->
                    </div>
                </div>

                <!-- Payment Form -->
                <form id="modalPaymentForm">
                    <input type="hidden" id="modalMahasiswaId" name="mahasiswa_id">
                    <input type="hidden" id="modalTagihanId" name="tagihan_ids">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Metode Pembayaran</label>
                            <select name="payment_method_id" class="form-select" required>
                                <option value="">Pilih Metode...</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rekening Bank</label>
                            <select name="bank_account_id" class="form-select">
                                <option value="">Pilih Rekening (Opsional)...</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->bank_name }} - {{ $account->account_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jumlah Dibayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="paid_amount" id="modalPaidAmount" class="form-control currency-input" 
                                       placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tanggal Pembayaran</label>
                            <input type="date" name="payment_date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Referensi Pembayaran</label>
                            <input type="text" name="payment_reference" class="form-control" 
                                   placeholder="Nomor referensi, kode transfer, dll">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" name="payment_proof" class="form-control" 
                                   accept=".jpg,.jpeg,.png,.pdf">
                            <small class="form-hint">Format: JPG, PNG, PDF. Maksimal 5MB</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Catatan tambahan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Batal</button>
                <button type="button" class="btn btn-primary" id="processModalPaymentBtn">
                    <i class="fas fa-credit-card me-1"></i>
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal modal-blur fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-success icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10" />
                </svg>
                <h3>Pembayaran Berhasil!</h3>
                <div class="text-muted" id="successMessage">Pembayaran telah berhasil diproses.</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Tutup</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success w-100" id="downloadReceiptBtn">Download Receipt</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-css')
<style>
/* Styling untuk tagihan lunas - responsive untuk dark/light mode */
.paid-bill-row {
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.paid-bill-row:hover {
    opacity: 0.9;
}

/* Light mode styling */
[data-bs-theme="light"] .paid-bill-row,
.paid-bill-row {
    background-color: #f8f9fa !important;
    border-left: 3px solid #28a745;
}

[data-bs-theme="light"] .paid-bill-text,
.paid-bill-text {
    color: #6c757d !important;
}

[data-bs-theme="light"] .paid-bill-badge {
    background-color: #e9ecef !important;
    color: #495057 !important;
    border: 1px solid #dee2e6;
}

/* Dark mode styling */
[data-bs-theme="dark"] .paid-bill-row {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-left: 3px solid #20c997;
}

[data-bs-theme="dark"] .paid-bill-text {
    color: rgba(255, 255, 255, 0.8) !important;
}

[data-bs-theme="dark"] .paid-bill-badge {
    background-color: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    font-weight: 500;
}

/* Specific styling for payment date text */
[data-bs-theme="dark"] .paid-bill-date {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 500;
}

[data-bs-theme="light"] .paid-bill-date,
.paid-bill-date {
    color: #6c757d !important;
    font-weight: 500;
}

/* Status badge improvements */
.status-badge-paid {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    color: white !important;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    border: none;
}

[data-bs-theme="dark"] .status-badge-paid {
    background: linear-gradient(135deg, #20c997, #17a2b8) !important;
}

/* Fix warning badge text color */
.badge.bg-warning {
    color: white !important;
    font-weight: 600;
}

[data-bs-theme="dark"] .badge.bg-warning {
    color: white !important;
    background-color: #ffc107 !important;
}

/* Fix blue badge text color for SPP */
.badge.bg-blue {
    color: white !important;
    font-weight: 600;
}

[data-bs-theme="dark"] .badge.bg-blue {
    color: white !important;
    background-color: #0d6efd !important;
}

/* Button styling for paid bills */
.btn-paid-disabled {
    background-color: transparent !important;
    border: 1px solid #6c757d !important;
    color: #6c757d !important;
}

[data-bs-theme="dark"] .btn-paid-disabled {
    border-color: rgba(255, 255, 255, 0.3) !important;
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Pagination styling improvements */
.pagination-sm .page-link {
    border-radius: 6px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

[data-bs-theme="dark"] .pagination-sm .page-link {
    border-color: rgba(255, 255, 255, 0.2);
    background-color: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.8);
}

[data-bs-theme="dark"] .pagination-sm .page-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

[data-bs-theme="dark"] .pagination-sm .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Currency input styling */
.currency-input {
    text-align: right;
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

/* ===== MOBILE RESPONSIVE STYLES ===== */

/* Mobile Card Styling */
.bill-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    padding: 1rem;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.bill-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}

[data-bs-theme="dark"] .bill-card {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
}

[data-bs-theme="dark"] .bill-card:hover {
    background: rgba(255, 255, 255, 0.08);
}

/* Paid bill card styling */
.bill-card.paid {
    opacity: 0.7;
    border-left: 4px solid #28a745;
}

[data-bs-theme="dark"] .bill-card.paid {
    border-left-color: #20c997;
}

/* Mobile card header */
.bill-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.bill-card-title {
    font-weight: 600;
    font-size: 1rem;
    margin: 0;
    flex: 1;
    min-width: 0;
}

.bill-card-checkbox {
    margin-top: 0.25rem;
    transform: scale(1.2);
}

/* Mobile card body */
.bill-card-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.bill-card-field {
    display: flex;
    flex-direction: column;
}

.bill-card-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

[data-bs-theme="dark"] .bill-card-label {
    color: rgba(255, 255, 255, 0.7);
}

.bill-card-value {
    font-size: 0.875rem;
    font-weight: 500;
    word-break: break-word;
}

/* Mobile card footer */
.bill-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid #dee2e6;
    flex-wrap: wrap;
    gap: 0.5rem;
}

[data-bs-theme="dark"] .bill-card-footer {
    border-top-color: rgba(255, 255, 255, 0.2);
}

/* Mobile table improvements */
.table-mobile {
    font-size: 0.875rem;
}

.table-mobile th,
.table-mobile td {
    padding: 0.5rem;
    white-space: nowrap;
}

/* Mobile form improvements */
@media (max-width: 576px) {
    .form-select,
    .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .btn {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Tablet improvements */
@media (min-width: 577px) and (max-width: 991px) {
    .bill-card-body {
        grid-template-columns: 1fr 1fr 1fr;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .btn {
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
    
    .form-check-input {
        transform: scale(1.3);
        margin: 0.25rem;
    }
    
    .dropdown-item {
        padding: 0.75rem 1rem;
    }
}

/* Alert responsive improvements */
.alert {
    word-break: break-word;
}

.alert .row {
    margin: 0;
}

.alert .col-12,
.alert .col-6,
.alert .col-sm-6,
.alert .col-md-3,
.alert .col-md-4,
.alert .col-lg-2,
.alert .col-lg-3 {
    padding: 0.25rem 0.5rem;
}

@media (max-width: 576px) {
    .alert .row > div {
        margin-bottom: 0.5rem;
    }
    
    .alert .row > div:last-child {
        margin-bottom: 0;
    }
}

/* Pagination mobile improvements */
@media (max-width: 576px) {
    .pagination-sm {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination-sm .page-item {
        margin: 0.125rem;
    }
    
    .pagination-sm .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Search dropdown mobile improvements */
#studentDropdown {
    max-height: 60vh !important;
    overflow-y: auto;
}

@media (max-width: 576px) {
    #studentDropdown .dropdown-item {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    #studentDropdown .dropdown-item:last-child {
        border-bottom: none;
    }
}

/* Empty state mobile improvements */
.empty-img img {
    max-width: 100%;
    height: auto;
}

@media (max-width: 576px) {
    .empty-img img {
        max-height: 80px;
    }
    
    .empty-title {
        font-size: 1.25rem;
    }
    
    .empty-subtitle {
        font-size: 0.875rem;
    }
}

/* Modal header fixes */
.modal-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 1rem !important;
    border-bottom: 1px solid #dee2e6 !important;
}

.modal-header .modal-title {
    margin: 0 !important;
    flex: 1 !important;
}

.modal-header .btn-close {
    margin-left: auto !important;
    padding: 0.5rem !important;
    background: transparent !important;
    border: none !important;
    font-size: 1.5rem !important;
    font-weight: bold !important;
    cursor: pointer !important;
    opacity: 0.8 !important;
    transition: opacity 0.15s ease-in-out !important;
    color: #000 !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-header .btn-close:hover {
    opacity: 1 !important;
    background-color: rgba(0, 0, 0, 0.1) !important;
    border-radius: 4px !important;
}

.modal-header .btn-close:focus {
    opacity: 1 !important;
    outline: none !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Ensure modal is clickable and not blocked by other elements */
.modal {
    pointer-events: auto !important;
    z-index: 1055 !important;
}

.modal-dialog {
    pointer-events: auto !important;
    z-index: 1056 !important;
}

.modal-content {
    pointer-events: auto !important;
}

/* Dark mode modal header */
[data-bs-theme="dark"] .modal-header {
    border-bottom-color: rgba(255, 255, 255, 0.2) !important;
}

[data-bs-theme="dark"] .modal-header .btn-close {
    color: #fff !important;
    opacity: 0.8 !important;
}

[data-bs-theme="dark"] .modal-header .btn-close:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    opacity: 1 !important;
}
</style>
@endsection

@section('custom-js')
<script>
let selectedStudent = null;
let studentBills = [];
let selectedBills = [];
let currentPage = 1;
let perPage = 10;
let currentSort = 'status';
let paginationData = null;

// Modal instances for proper management
let paymentModalInstance = null;
let successModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap modals properly
    initializeModals();
    
    // Setup modal close event handlers
    setupModalCloseHandlers();
    const studentSearch = document.getElementById('studentSearch');
    const studentDropdown = document.getElementById('studentDropdown');
    let searchTimeout;
    let isSelectingFromDropdown = false;

    // Load all students when page loads
    loadAllStudents();

    // Pagination and sorting event listeners
    document.getElementById('perPageSelect').addEventListener('change', function() {
        perPage = parseInt(this.value);
        currentPage = 1; // Reset to first page
        if (selectedStudent) {
            loadStudentBills(selectedStudent.id);
        }
    });

    document.getElementById('sortSelect').addEventListener('change', function() {
        currentSort = this.value;
        currentPage = 1; // Reset to first page
        if (selectedStudent) {
            loadStudentBills(selectedStudent.id);
        }
    });

    // Student search functionality - improved UX
    studentSearch.addEventListener('focus', function() {
        // Show all students when focused (if no search query)
        if (this.value.trim() === '') {
            loadAllStudents();
        } else {
            // If there's existing text, show dropdown with current results
            studentDropdown.style.display = 'block';
        }
    });

    studentSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        // If user is typing and there's no query, show all students
        if (query === '') {
            loadAllStudents();
            return;
        }

        // If query is less than 2 characters, still show all students but filtered
        if (query.length < 2) {
            searchStudents(query, true); // true = show all with filter
            return;
        }

        // Normal search with debounce
        searchTimeout = setTimeout(() => {
            searchStudents(query);
        }, 300);
    });

    // Clear input when user starts typing (if not selecting from dropdown)
    studentSearch.addEventListener('keydown', function(e) {
        if (!isSelectingFromDropdown && this.dataset.selected === 'true') {
            // User is typing on a selected value, clear it
            if (e.key.length === 1 || e.key === 'Backspace' || e.key === 'Delete') {
                this.value = '';
                this.dataset.selected = 'false';
                resetStudentSelection();
                loadAllStudents();
            }
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!studentSearch.contains(e.target) && !studentDropdown.contains(e.target)) {
            studentDropdown.style.display = 'none';
        }
    });

    // Payment form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment();
    });

    // Bill selection change handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('bill-checkbox')) {
            updateSelectedBills();
        }
    });

    // Select all checkbox handlers (both desktop and mobile)
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllCheckboxMobile = document.getElementById('selectAllCheckboxMobile');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            // Only select checkboxes that are currently visible (not hidden by pagination)
            const checkboxes = getVisibleBillCheckboxes();
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            // Sync mobile checkbox
            if (selectAllCheckboxMobile) selectAllCheckboxMobile.checked = this.checked;
            updateSelectedBills();
        });
    }
    
    if (selectAllCheckboxMobile) {
        selectAllCheckboxMobile.addEventListener('change', function() {
            // Only select checkboxes that are currently visible (not hidden by pagination)
            const checkboxes = getVisibleBillCheckboxes();
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            // Sync desktop checkbox
            if (selectAllCheckbox) selectAllCheckbox.checked = this.checked;
            updateSelectedBills();
        });
    }

    // Modal payment form submission
    document.getElementById('processModalPaymentBtn').addEventListener('click', function() {
        processModalPayment();
    });

    // Currency input formatting
    setupCurrencyInput('paidAmount');
    setupCurrencyInput('modalPaidAmount');
    
    // Enhanced modal input handling for large numbers
    const modalPaidAmountInput = document.getElementById('modalPaidAmount');
    if (modalPaidAmountInput) {
        // Remove type="number" restriction to allow formatted input
        modalPaidAmountInput.type = 'text';
        
        modalPaidAmountInput.addEventListener('input', function(e) {
            try {
                const oldValue = e.target.value;
                const rawValue = parseCurrency(e.target.value);
                
                // Handle large numbers properly
                if (rawValue === 0 && oldValue.replace(/[^\d]/g, '').length > 0) {
                    const digitOnly = oldValue.replace(/[^\d]/g, '');
                    if (digitOnly.length > 12) {
                        const limitedValue = digitOnly.substring(0, 12);
                        const formattedValue = formatCurrency(limitedValue);
                        e.target.value = formattedValue;
                        return;
                    }
                }
                
                const formattedValue = formatCurrency(rawValue.toString());
                e.target.value = formattedValue;
            } catch (error) {
                console.error('Error in modal currency formatting:', error);
            }
        });
        
        // Handle focus - select all for easy replacement
        modalPaidAmountInput.addEventListener('focus', function(e) {
            setTimeout(() => {
                e.target.select();
            }, 0);
        });
        
        // Handle blur - ensure proper formatting
        modalPaidAmountInput.addEventListener('blur', function(e) {
            try {
                const rawValue = parseCurrency(e.target.value);
                const formattedValue = formatCurrency(rawValue.toString());
                e.target.value = formattedValue;
            } catch (error) {
                console.error('Error in modal blur formatting:', error);
            }
        });
    }
});

// Currency formatting functions
function formatCurrency(value) {
    // Handle empty or invalid input
    if (!value || value === '') return '';
    
    // Remove all non-digits
    const numericValue = value.toString().replace(/[^\d]/g, '');
    
    if (!numericValue || numericValue === '0') return '';
    
    // Convert to integer and format with thousand separators
    const intValue = parseInt(numericValue);
    
    // Prevent overflow for very large numbers
    if (intValue > 999999999999) {
        return '999.999.999.999';
    }
    
    // Add thousand separators using Indonesian format
    return intValue.toLocaleString('id-ID');
}

function parseCurrency(value) {
    // Handle empty or invalid input
    if (!value || value === '') return 0;
    
    // Remove all non-digits and return as integer
    const cleanValue = value.toString().replace(/[^\d]/g, '');
    const intValue = parseInt(cleanValue);
    
    // Return 0 for invalid numbers or prevent overflow
    if (isNaN(intValue) || intValue > 999999999999) return 0;
    
    return intValue;
}

function setupCurrencyInput(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    // Set initial formatting if input has value
    if (input.value && input.value !== '') {
        const integerValue = Math.floor(parseFloat(input.value) || 0);
        const formattedValue = formatCurrency(integerValue.toString());
        input.value = formattedValue;
        
        const rawInput = document.getElementById(inputId + 'Raw');
        if (rawInput) {
            rawInput.value = integerValue;
        }
    }
    
    input.addEventListener('input', function(e) {
        try {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const oldLength = oldValue.length;
            
            // Parse and ensure integer value
            const rawValue = parseCurrency(e.target.value);
            
            // Prevent the input from being reset to 0 for large numbers
            if (rawValue === 0 && oldValue.replace(/[^\d]/g, '').length > 0) {
                // If we have digits but parseCurrency returns 0, there might be an overflow
                const digitOnly = oldValue.replace(/[^\d]/g, '');
                if (digitOnly.length > 12) {
                    // Limit to 12 digits (999,999,999,999)
                    const limitedValue = digitOnly.substring(0, 12);
                    const formattedValue = formatCurrency(limitedValue);
                    e.target.value = formattedValue;
                    
                    const rawInput = document.getElementById(inputId + 'Raw');
                    if (rawInput) {
                        rawInput.value = parseInt(limitedValue);
                    }
                    return;
                }
            }
            
            const formattedValue = formatCurrency(rawValue.toString());
            e.target.value = formattedValue;
            
            // Adjust cursor position intelligently
            const newLength = formattedValue.length;
            let newCursorPosition = cursorPosition;
            
            // If the length changed significantly, try to maintain relative position
            if (Math.abs(newLength - oldLength) > 1) {
                const relativePosition = cursorPosition / oldLength;
                newCursorPosition = Math.round(relativePosition * newLength);
            } else {
                newCursorPosition = cursorPosition + (newLength - oldLength);
            }
            
            // Ensure cursor position is within bounds
            newCursorPosition = Math.max(0, Math.min(newCursorPosition, newLength));
            
            // Set cursor position
            setTimeout(() => {
                if (e.target === document.activeElement) {
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            }, 0);
            
            // Update hidden field with raw integer value
            const rawInput = document.getElementById(inputId + 'Raw');
            if (rawInput) {
                rawInput.value = rawValue;
            }
        } catch (error) {
            console.error('Error in currency input formatting:', error);
        }
    });
    
    // Handle paste events
    input.addEventListener('paste', function(e) {
        setTimeout(() => {
            try {
                const rawValue = parseCurrency(e.target.value);
                const formattedValue = formatCurrency(rawValue.toString());
                e.target.value = formattedValue;
                
                const rawInput = document.getElementById(inputId + 'Raw');
                if (rawInput) {
                    rawInput.value = rawValue;
                }
            } catch (error) {
                console.error('Error in paste formatting:', error);
            }
        }, 0);
    });
    
    // Handle focus events - select all text for easy replacement
    input.addEventListener('focus', function(e) {
        setTimeout(() => {
            e.target.select();
        }, 0);
    });
    
    // Handle blur events - ensure formatting is correct
    input.addEventListener('blur', function(e) {
        try {
            const rawValue = parseCurrency(e.target.value);
            const formattedValue = formatCurrency(rawValue.toString());
            e.target.value = formattedValue;
            
            const rawInput = document.getElementById(inputId + 'Raw');
            if (rawInput) {
                rawInput.value = rawValue;
            }
        } catch (error) {
            console.error('Error in blur formatting:', error);
        }
    });
}

function processModalPayment() {
    const formData = new FormData(document.getElementById('modalPaymentForm'));
    
    // Get tagihan ID from hidden field and convert to array format
    const tagihanId = document.getElementById('modalTagihanId').value;
    formData.delete('tagihan_ids'); // Remove the hidden field value
    formData.append('tagihan_ids[]', tagihanId); // Add as array
    
    // Convert formatted amount to raw number
    const modalPaidAmountInput = document.getElementById('modalPaidAmount');
    const rawAmount = parseCurrency(modalPaidAmountInput.value);
    formData.set('paid_amount', rawAmount);
    
    // Show loading state
    const btn = document.getElementById('processModalPaymentBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    btn.disabled = true;

    fetch('/keuangan/pembayaran-manual', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide payment modal
            closePaymentModal();
            
            // Show success modal
            document.getElementById('successMessage').textContent = data.message;
            if (data.receipt_url) {
                document.getElementById('downloadReceiptBtn').onclick = () => {
                    window.open(data.receipt_url, '_blank');
                };
                document.getElementById('downloadReceiptBtn').style.display = 'block';
            } else {
                document.getElementById('downloadReceiptBtn').style.display = 'none';
            }
            
            showSuccessModal();
            
            // Reload student bills
            loadStudentBills(selectedStudent.id);
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan saat memproses pembayaran.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
    })
    .finally(() => {
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function searchStudents(query) {
    fetch(`/keuangan/pembayaran-manual/ajax/search-mahasiswa?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            displayStudentResults(data.results || []);
        })
        .catch(error => {
            console.error('Error searching students:', error);
            console.log('Fetch URL:', `/keuangan/pembayaran-manual/ajax/search-mahasiswa?q=${encodeURIComponent(query)}`);
        });
}

function displayStudentResults(students) {
    const dropdown = document.getElementById('studentDropdown');
    
    if (students.length === 0) {
        dropdown.innerHTML = '<div class="dropdown-item-text text-muted">Tidak ada mahasiswa ditemukan</div>';
    } else {
        dropdown.innerHTML = students.map(student => `
            <a href="#" class="dropdown-item" onclick="selectStudent(${student.id}, '${student.name}', '${student.nim}', '${student.program_studi}')">
                <div class="d-flex">
                    <div>
                        <div class="fw-bold">${student.name}</div>
                        <div class="text-muted small">NIM: ${student.nim} • ${student.program_studi}</div>
                    </div>
                </div>
            </a>
        `).join('');
    }
    
    dropdown.style.display = 'block';
}

function selectStudent(id, name, nim, prodi) {
    selectedStudent = { id, name, nim, prodi };
    
    // Update search input
    document.getElementById('studentSearch').value = name;
    document.getElementById('studentDropdown').style.display = 'none';
    
    // Update student info
    document.getElementById('studentName').textContent = name;
    document.getElementById('studentId').textContent = id;
    document.getElementById('studentNim').textContent = nim;
    document.getElementById('studentProdi').textContent = prodi;
    document.getElementById('selectedMahasiswaId').value = id;
    
    // Show student info section
    document.getElementById('studentInfoSection').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    
    // Load student bills
    loadStudentBills(id);
}

function loadStudentBills(studentId, page = null) {
    if (page) currentPage = page;
    
    const params = new URLSearchParams({
        page: currentPage,
        per_page: perPage,
        sort_by: currentSort,
        sort_order: 'asc'
    });

    fetch(`/keuangan/pembayaran-manual/ajax/mahasiswa/${studentId}/tagihan?${params}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            studentBills = data.tagihans || [];
            paginationData = data.pagination || null;
            displayStudentBills();
            updateTotalTagihan();
            updatePaginationInfo();
            renderPagination();
            
            // Reset select all checkboxes when page changes
            resetSelectAllCheckboxes();
        })
        .catch(error => {
            console.error('Error loading student bills:', error);
            console.log('Fetch URL:', `/keuangan/pembayaran-manual/ajax/mahasiswa/${studentId}/tagihan?${params}`);
            alert('Gagal memuat data tagihan mahasiswa');
        });
}

function updatePaginationInfo() {
    if (!paginationData) {
        document.getElementById('paginationInfo').textContent = '-';
        document.getElementById('paginationSection').style.display = 'none';
        return;
    }

    const { from, to, total } = paginationData;
    document.getElementById('paginationInfo').textContent = `Menampilkan ${from || 0}-${to || 0} dari ${total || 0} tagihan`;
    document.getElementById('paginationText').textContent = `Menampilkan ${from || 0} sampai ${to || 0} dari ${total || 0} tagihan`;
    
    // Show pagination if there are multiple pages
    if (paginationData.last_page > 1) {
        document.getElementById('paginationSection').style.display = 'flex';
    } else {
        document.getElementById('paginationSection').style.display = 'none';
    }
}

function renderPagination() {
    if (!paginationData || paginationData.last_page <= 1) {
        return;
    }

    const nav = document.getElementById('paginationNav');
    const { current_page, last_page, has_more_pages } = paginationData;
    
    let paginationHTML = '';

    // Previous button
    if (current_page > 1) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadStudentBills(${selectedStudent.id}, ${current_page - 1}); return false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    Sebelumnya
                </a>
            </li>
        `;
    }

    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(last_page, current_page + 2);

    if (startPage > 1) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadStudentBills(${selectedStudent.id}, 1); return false;">1</a>
            </li>
        `;
        if (startPage > 2) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <li class="page-item ${i === current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadStudentBills(${selectedStudent.id}, ${i}); return false;">${i}</a>
            </li>
        `;
    }

    if (endPage < last_page) {
        if (endPage < last_page - 1) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadStudentBills(${selectedStudent.id}, ${last_page}); return false;">${last_page}</a>
            </li>
        `;
    }

    // Next button
    if (has_more_pages) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadStudentBills(${selectedStudent.id}, ${current_page + 1}); return false;">
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
            </li>
        `;
    }

    nav.innerHTML = paginationHTML;
}

function displayStudentBills() {
    const tbody = document.getElementById('billsTableBody');
    const tbodyMobile = document.getElementById('billsTableBodyMobile');
    const cardContainer = document.getElementById('billsCardContainer');
    
    if (studentBills.length === 0) {
        const emptyContent = `
            <div class="text-center text-muted py-4">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('dashboard/static/illustrations/undraw_empty_xct9.svg') }}" height="64" alt="">
                    </div>
                    <p class="empty-title">Tidak ada tagihan</p>
                    <p class="empty-subtitle">Mahasiswa ini tidak memiliki tagihan.</p>
                </div>
            </div>
        `;
        
        tbody.innerHTML = `<tr><td colspan="8">${emptyContent}</td></tr>`;
        if (tbodyMobile) tbodyMobile.innerHTML = `<tr><td colspan="8">${emptyContent}</td></tr>`;
        if (cardContainer) cardContainer.innerHTML = emptyContent;
        
        document.getElementById('billsSection').style.display = 'block';
        return;
    }

    // Desktop table content
    tbody.innerHTML = studentBills.map((bill, index) => {
        return generateTableRow(bill);
    }).join('');

    // Mobile table content (for tablets)
    if (tbodyMobile) {
        tbodyMobile.innerHTML = studentBills.map((bill, index) => {
            return generateTableRow(bill);
        }).join('');
    }

    // Mobile card content
    if (cardContainer) {
        cardContainer.innerHTML = studentBills.map((bill, index) => {
            return generateMobileCard(bill);
        }).join('');
    }

    document.getElementById('billsSection').style.display = 'block';
}

function generateTableRow(bill) {
    const isPaid = bill.is_paid;
    const isPending = bill.is_pending;
    
    return `
        <tr class="${isPaid ? 'paid-bill-row' : ''}">
            <td>
                ${!isPaid ? `<input type="checkbox" class="form-check-input bill-checkbox" 
                       value="${bill.id}" data-amount="${bill.remaining_amount || bill.amount}">` : 
                       '<i class="fas fa-check-circle text-success"></i>'}
            </td>
            <td class="${isPaid ? 'paid-bill-text' : ''}">${bill.bill_number || '-'}</td>
            <td>
                <span class="badge ${isPaid ? 'paid-bill-badge' : 'bg-blue'}">${bill.category || 'SPP'}</span>
            </td>
            <td>
                <div>
                    <div class="fw-bold ${isPaid ? 'paid-bill-text' : ''}">${bill.name}</div>
                    <div class="text-muted small">${bill.description || ''}</div>
                    ${bill.due_date ? `<div class="text-muted small">Jatuh tempo: ${bill.due_date}</div>` : '<div class="text-muted small">Tidak ada tempo</div>'}
                </div>
            </td>
            <td>
                <span class="fw-bold ${isPaid ? 'paid-bill-text' : ''}">${bill.formatted_amount}</span>
            </td>
            <td>
                ${isPaid ? 
                    '<span class="fw-bold text-success">Lunas</span>' : 
                    (bill.is_installment ? 
                        `<div>
                            <span class="fw-bold text-warning">${bill.formatted_remaining_amount}</span>
                            <br><small class="text-muted">Dibayar: ${bill.formatted_paid_amount} (${bill.payment_percentage}%)</small>
                        </div>` :
                        `<span class="fw-bold text-danger">${bill.formatted_remaining_amount}</span>`
                    )
                }
            </td>
            <td>
                ${isPaid ? 
                    `<span class="badge status-badge-paid">
                        <i class="fas fa-check me-1"></i>Lunas
                    </span>
                    ${bill.paid_date ? `<br><small class="paid-bill-date">Dibayar: ${bill.paid_date}</small>` : ''}` :
                    (bill.is_overdue ? 
                        '<span class="badge bg-danger">Terlambat</span>' : 
                        '<span class="badge bg-warning">Belum Bayar</span>')
                }
            </td>
            <td>
                ${!isPaid ? 
                    `<button type="button" class="btn btn-sm btn-primary" 
                            onclick="payIndividualBill(${bill.id})">
                        <i class="fas fa-credit-card me-1"></i>Bayar
                    </button>` : 
                    `<button type="button" class="btn btn-sm btn-paid-disabled" disabled>
                        <i class="fas fa-check me-1"></i>Sudah Dibayar
                    </button>`
                }
            </td>
        </tr>
    `;
}

function generateMobileCard(bill) {
    const isPaid = bill.is_paid;
    const isPending = bill.is_pending;
    
    return `
        <div class="bill-card ${isPaid ? 'paid' : ''}">
            <div class="bill-card-header">
                <h6 class="bill-card-title ${isPaid ? 'paid-bill-text' : ''}">${bill.name}</h6>
                ${!isPaid ? 
                    `<input type="checkbox" class="form-check-input bill-checkbox bill-card-checkbox" 
                           value="${bill.id}" data-amount="${bill.remaining_amount || bill.amount}">` : 
                    '<i class="fas fa-check-circle text-success fs-5"></i>'
                }
            </div>
            
            <div class="bill-card-body">
                <div class="bill-card-field">
                    <div class="bill-card-label">No Tagihan</div>
                    <div class="bill-card-value ${isPaid ? 'paid-bill-text' : ''}">${bill.bill_number || '-'}</div>
                </div>
                
                <div class="bill-card-field">
                    <div class="bill-card-label">Kategori</div>
                    <div class="bill-card-value">
                        <span class="badge ${isPaid ? 'paid-bill-badge' : 'bg-blue'}">${bill.category || 'SPP'}</span>
                    </div>
                </div>
                
                <div class="bill-card-field">
                    <div class="bill-card-label">Jumlah Tagihan</div>
                    <div class="bill-card-value fw-bold ${isPaid ? 'paid-bill-text' : ''}">${bill.formatted_amount}</div>
                </div>
                
                <div class="bill-card-field">
                    <div class="bill-card-label">Sisa Pembayaran</div>
                    <div class="bill-card-value">
                        ${isPaid ? 
                            '<span class="fw-bold text-success">Lunas</span>' : 
                            (bill.is_installment ? 
                                `<div>
                                    <span class="fw-bold text-warning">${bill.formatted_remaining_amount}</span>
                                    <br><small class="text-muted">Dibayar: ${bill.formatted_paid_amount}</small>
                                </div>` :
                                `<span class="fw-bold text-danger">${bill.formatted_remaining_amount}</span>`
                            )
                        }
                    </div>
                </div>
                
                ${bill.description ? `
                <div class="bill-card-field" style="grid-column: 1 / -1;">
                    <div class="bill-card-label">Deskripsi</div>
                    <div class="bill-card-value text-muted small">${bill.description}</div>
                </div>
                ` : ''}
                
                <div class="bill-card-field" style="grid-column: 1 / -1;">
                    <div class="bill-card-label">Jatuh Tempo</div>
                    <div class="bill-card-value text-muted small">
                        ${bill.due_date ? bill.due_date : '<span class="text-muted">Tidak ada tempo</span>'}
                    </div>
                </div>
            </div>
            
            <div class="bill-card-footer">
                <div>
                    ${isPaid ? 
                        `<span class="badge status-badge-paid">
                            <i class="fas fa-check me-1"></i>Lunas
                        </span>
                        ${bill.paid_date ? `<br><small class="paid-bill-date">Dibayar: ${bill.paid_date}</small>` : ''}` :
                        (bill.is_overdue ? 
                            '<span class="badge bg-danger">Terlambat</span>' : 
                            '<span class="badge bg-warning">Belum Bayar</span>')
                    }
                </div>
                
                <div>
                    ${!isPaid ? 
                        `<button type="button" class="btn btn-sm btn-primary" 
                                onclick="payIndividualBill(${bill.id})">
                            <i class="fas fa-credit-card me-1"></i>Bayar
                        </button>` : 
                        `<button type="button" class="btn btn-sm btn-paid-disabled" disabled>
                            <i class="fas fa-check me-1"></i>Sudah Dibayar
                        </button>`
                    }
                </div>
            </div>
        </div>
    `;
}

function updateTotalTagihan() {
    // Only count remaining amounts for unpaid bills
    const unpaidBills = studentBills.filter(bill => !bill.is_paid);
    const total = unpaidBills.reduce((sum, bill) => {
        // Parse the amount properly - handle both numeric and string values
        let remainingAmount = 0;
        
        if (bill.remaining_amount !== undefined && bill.remaining_amount !== null) {
            // If remaining_amount exists, use it
            remainingAmount = typeof bill.remaining_amount === 'string' ? 
                parseFloat(bill.remaining_amount.replace(/[^\d.-]/g, '')) : 
                parseFloat(bill.remaining_amount);
        } else if (bill.amount !== undefined && bill.amount !== null) {
            // Otherwise use full amount
            remainingAmount = typeof bill.amount === 'string' ? 
                parseFloat(bill.amount.replace(/[^\d.-]/g, '')) : 
                parseFloat(bill.amount);
        }
        
        // Ensure we have a valid number
        remainingAmount = isNaN(remainingAmount) ? 0 : remainingAmount;
        
        return sum + remainingAmount;
    }, 0);
    
    // Format the total properly
    const formattedTotal = total.toLocaleString('id-ID');
    document.getElementById('totalTagihan').textContent = `Rp ${formattedTotal}`;
}

function updateSelectedBills() {
    // Only count visible checked checkboxes
    const visibleCheckboxes = getVisibleBillCheckboxes();
    const checkedVisibleCheckboxes = visibleCheckboxes.filter(cb => cb.checked);
    
    console.log('=== UPDATE SELECTED BILLS DEBUG ===');
    console.log('Visible checkboxes:', visibleCheckboxes.length);
    console.log('Checked visible checkboxes:', checkedVisibleCheckboxes.length);
    
    selectedBills = Array.from(checkedVisibleCheckboxes).map(cb => {
        const amount = Math.floor(parseFloat(cb.dataset.amount) || 0);
        console.log(`Bill ID: ${cb.value}, Amount: ${amount}`);
        return {
            id: cb.value,
            amount: amount
        };
    });

    const total = selectedBills.reduce((sum, bill) => sum + bill.amount, 0);
    const integerTotal = Math.floor(total); // Ensure no decimals
    
    console.log('Selected bills:', selectedBills);
    console.log('Total calculated:', integerTotal);
    
    document.getElementById('selectedBillsTotal').textContent = `Rp ${integerTotal.toLocaleString('id-ID')}`;
    
    // Format the amount properly for the input field (ensure integer)
    const paidAmountInput = document.getElementById('paidAmount');
    const formattedAmount = formatCurrency(integerTotal.toString());
    paidAmountInput.value = formattedAmount;
    
    // Update hidden field with raw integer value
    const rawInput = document.getElementById('paidAmountRaw');
    if (rawInput) {
        rawInput.value = integerTotal;
    }
    
    document.getElementById('selectedTagihanIds').value = selectedBills.map(b => b.id).join(',');

    // Show/hide payment form
    if (selectedBills.length > 0) {
        document.getElementById('paymentFormSection').style.display = 'block';
    } else {
        document.getElementById('paymentFormSection').style.display = 'none';
    }

    // Update select all checkboxes (both desktop and mobile) - only consider visible checkboxes
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllCheckboxMobile = document.getElementById('selectAllCheckboxMobile');
    
    const isAllSelected = visibleCheckboxes.length > 0 && checkedVisibleCheckboxes.length === visibleCheckboxes.length;
    
    if (selectAllCheckbox) selectAllCheckbox.checked = isAllSelected;
    if (selectAllCheckboxMobile) selectAllCheckboxMobile.checked = isAllSelected;
}

// Get only visible bill checkboxes (current page, not hidden by pagination)
function getVisibleBillCheckboxes() {
    // Simple approach: get all checkboxes that are actually visible in the DOM
    const allCheckboxes = document.querySelectorAll('.bill-checkbox');
    const visibleCheckboxes = [];
    
    console.log('Total checkboxes found:', allCheckboxes.length);
    
    allCheckboxes.forEach((checkbox, index) => {
        // Check if the checkbox is actually visible (not hidden by display:none or visibility:hidden)
        const rect = checkbox.getBoundingClientRect();
        const isVisible = rect.width > 0 && rect.height > 0 && 
                         window.getComputedStyle(checkbox).visibility !== 'hidden' &&
                         window.getComputedStyle(checkbox).display !== 'none';
        
        // Also check if parent elements are visible
        let parent = checkbox.parentElement;
        let parentVisible = true;
        while (parent && parent !== document.body) {
            const parentStyle = window.getComputedStyle(parent);
            if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') {
                parentVisible = false;
                break;
            }
            parent = parent.parentElement;
        }
        
        if (isVisible && parentVisible) {
            visibleCheckboxes.push(checkbox);
            console.log(`Checkbox ${index} is visible:`, checkbox.value, checkbox.dataset.amount);
        } else {
            console.log(`Checkbox ${index} is hidden:`, checkbox.value);
        }
    });
    
    console.log('Visible checkboxes:', visibleCheckboxes.length);
    return visibleCheckboxes;
}

// Reset select all checkboxes when page changes
function resetSelectAllCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllCheckboxMobile = document.getElementById('selectAllCheckboxMobile');
    
    if (selectAllCheckbox) selectAllCheckbox.checked = false;
    if (selectAllCheckboxMobile) selectAllCheckboxMobile.checked = false;
    
    // Clear selected bills array and update UI
    selectedBills = [];
    document.getElementById('selectedBillsTotal').textContent = 'Rp 0';
    document.getElementById('selectedTagihanIds').value = '';
    document.getElementById('paymentFormSection').style.display = 'none';
    
    // Clear payment amount input
    const paidAmountInput = document.getElementById('paidAmount');
    if (paidAmountInput) {
        paidAmountInput.value = '';
    }
    
    const rawInput = document.getElementById('paidAmountRaw');
    if (rawInput) {
        rawInput.value = '';
    }
}

function selectAllBills() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllCheckboxMobile = document.getElementById('selectAllCheckboxMobile');
    
    if (selectAllCheckbox) selectAllCheckbox.checked = true;
    if (selectAllCheckboxMobile) selectAllCheckboxMobile.checked = true;
    
    // Only select visible checkboxes
    const visibleCheckboxes = getVisibleBillCheckboxes();
    visibleCheckboxes.forEach(cb => cb.checked = true);
    updateSelectedBills();
}

function clearAllBills() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllCheckboxMobile = document.getElementById('selectAllCheckboxMobile');
    
    if (selectAllCheckbox) selectAllCheckbox.checked = false;
    if (selectAllCheckboxMobile) selectAllCheckboxMobile.checked = false;
    
    // Only clear visible checkboxes
    const visibleCheckboxes = getVisibleBillCheckboxes();
    visibleCheckboxes.forEach(cb => cb.checked = false);
    updateSelectedBills();
}

function payIndividualBill(billId) {
    // Find the bill data
    const bill = studentBills.find(b => b.id == billId);
    if (!bill) {
        alert('Tagihan tidak ditemukan');
        return;
    }

    // Calculate remaining amount (for installment bills) and ensure integer
    const rawRemainingAmount = bill.remaining_amount || bill.amount;
    const remainingAmount = Math.floor(parseFloat(rawRemainingAmount) || 0);

    // Set modal data
    document.getElementById('modalStudentName').textContent = selectedStudent.name;
    document.getElementById('modalStudentNim').textContent = selectedStudent.nim;
    document.getElementById('modalMahasiswaId').value = selectedStudent.id;
    document.getElementById('modalTagihanId').value = billId;
    document.getElementById('modalPaidAmount').value = remainingAmount;

    // Set bill info with remaining amount info
    let billInfoHtml = `
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>${bill.name}</strong>
                <br>
                <small class="text-muted">${bill.description || ''}</small>
                <br>
                <small class="text-muted">Jatuh tempo: ${bill.due_date || 'Tidak ada tempo'}</small>
                ${bill.is_overdue ? '<br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Terlambat</small>' : ''}
    `;
    
    // Add installment info if applicable
    if (bill.is_installment) {
        billInfoHtml += `
                <br><small class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    Sudah dibayar: ${bill.formatted_paid_amount} (${bill.payment_percentage}%)
                </small>
        `;
    }
    
    billInfoHtml += `
            </div>
            <div class="text-end">
                <strong class="text-primary">${bill.formatted_remaining_amount || bill.formatted_amount}</strong>
                ${bill.is_installment ? '<br><small class="text-muted">Sisa pembayaran</small>' : ''}
            </div>
        </div>
    `;

    document.getElementById('modalBillInfo').innerHTML = billInfoHtml;

    // Reset modal form
    document.getElementById('modalPaymentForm').reset();
    document.getElementById('modalMahasiswaId').value = selectedStudent.id;
    document.getElementById('modalTagihanId').value = billId;
    
    // Set the amount with proper formatting
    const modalPaidAmountInput = document.getElementById('modalPaidAmount');
    const formattedAmount = formatCurrency(remainingAmount.toString());
    modalPaidAmountInput.value = formattedAmount;
    
    // Trigger formatting for the modal input to ensure consistency
    const event = new Event('blur', { bubbles: true });
    modalPaidAmountInput.dispatchEvent(event);

    // Set default payment method to "Tunai" (Cash)
    const paymentMethodSelect = document.querySelector('#paymentModal select[name="payment_method_id"]');
    if (paymentMethodSelect) {
        // Find the "Tunai" option and select it
        const tunaiOption = Array.from(paymentMethodSelect.options).find(option => 
            option.text.toLowerCase().includes('tunai') || 
            option.text.toLowerCase().includes('cash')
        );
        if (tunaiOption) {
            paymentMethodSelect.value = tunaiOption.value;
        }
    }

    // Show modal using our enhanced function
    showPaymentModal();
}

function processPayment() {
    const formData = new FormData(document.getElementById('paymentForm'));
    
    // Add selected tagihan IDs as array
    const selectedTagihanIds = selectedBills.map(b => b.id);
    formData.delete('tagihan_ids'); // Remove the hidden field value
    selectedTagihanIds.forEach(id => {
        formData.append('tagihan_ids[]', id);
    });
    
    // Show loading state
    const submitBtn = document.querySelector('#paymentForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    submitBtn.disabled = true;

    fetch('/keuangan/pembayaran-manual', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            document.getElementById('successMessage').textContent = data.message;
            if (data.receipt_url) {
                document.getElementById('downloadReceiptBtn').onclick = () => {
                    window.open(data.receipt_url, '_blank');
                };
                document.getElementById('downloadReceiptBtn').style.display = 'block';
            } else {
                document.getElementById('downloadReceiptBtn').style.display = 'none';
            }
            
            showSuccessModal();
            
            // Reload student bills
            loadStudentBills(selectedStudent.id);
            resetPaymentForm();
        } else {
            alert(data.message || 'Terjadi kesalahan saat memproses pembayaran');
        }
    })
    .catch(error => {
        console.error('Error processing payment:', error);
        alert('Terjadi kesalahan saat memproses pembayaran');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function resetPaymentForm() {
    document.getElementById('paymentForm').reset();
    document.getElementById('paymentDate').value = '{{ date("Y-m-d") }}';
    document.getElementById('paymentFormSection').style.display = 'none';
    clearAllBills();
}

// Load all students function - shows all students when dropdown opens
function loadAllStudents() {
    fetch('/keuangan/pembayaran-manual/ajax/search-mahasiswa?q=', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            displayStudentResults(data.results || []);
        })
        .catch(error => {
            console.error('Error loading all students:', error);
        });
}

// Reset student selection function
function resetStudentSelection() {
    selectedStudent = null;
    studentBills = [];
    selectedBills = [];
    
    // Hide sections
    document.getElementById('studentInfoSection').style.display = 'none';
    document.getElementById('billsSection').style.display = 'none';
    document.getElementById('paymentFormSection').style.display = 'none';
    document.getElementById('emptyState').style.display = 'block';
    
    // Clear form data
    document.getElementById('selectedMahasiswaId').value = '';
    document.getElementById('selectedTagihanIds').value = '';
    
    // Reset search input attributes
    const searchInput = document.getElementById('studentSearch');
    searchInput.dataset.selected = 'false';
}

// Enhanced selectStudent function with better UX
function selectStudent(id, name, nim, prodi) {
    const searchInput = document.getElementById('studentSearch');
    
    // Set flag to prevent clearing when selecting from dropdown
    isSelectingFromDropdown = true;
    
    selectedStudent = { id, name, nim, prodi };
    
    // Update search input and mark as selected
    searchInput.value = name;
    searchInput.dataset.selected = 'true';
    document.getElementById('studentDropdown').style.display = 'none';
    
    // Update student info
    document.getElementById('studentName').textContent = name;
    document.getElementById('studentId').textContent = id;
    document.getElementById('studentNim').textContent = nim;
    document.getElementById('studentProdi').textContent = prodi;
    document.getElementById('selectedMahasiswaId').value = id;
    
    // Show student info section
    document.getElementById('studentInfoSection').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    
    // Load student bills
    loadStudentBills(id);
    
    // Reset flag after a short delay
    setTimeout(() => {
        isSelectingFromDropdown = false;
    }, 100);
}

// Initialize Bootstrap modals with proper configuration
function initializeModals() {
    try {
        // Initialize payment modal
        const paymentModalElement = document.getElementById('paymentModal');
        if (paymentModalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            paymentModalInstance = new bootstrap.Modal(paymentModalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        }

        // Initialize success modal
        const successModalElement = document.getElementById('successModal');
        if (successModalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            successModalInstance = new bootstrap.Modal(successModalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        }

        console.log('Modals initialized successfully');
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
}

// Setup comprehensive modal close handlers
function setupModalCloseHandlers() {
    // Payment Modal close handlers
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        // Close button in header
        const paymentCloseBtn = paymentModal.querySelector('.btn-close');
        if (paymentCloseBtn) {
            paymentCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closePaymentModal();
            });
        }

        // Cancel button in footer
        const cancelBtn = paymentModal.querySelector('[data-bs-dismiss="modal"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closePaymentModal();
            });
        }

        // Backdrop click handler
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                closePaymentModal();
            }
        });

        // ESC key handler
        paymentModal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePaymentModal();
            }
        });
    }

    // Success Modal close handlers
    const successModal = document.getElementById('successModal');
    if (successModal) {
        // Close button
        const successCloseBtn = successModal.querySelector('[data-bs-dismiss="modal"]');
        if (successCloseBtn) {
            successCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeSuccessModal();
            });
        }

        // Backdrop click handler
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // ESC key handler
        successModal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuccessModal();
            }
        });
    }

    // Global ESC key handler for all modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });

    console.log('Modal close handlers setup complete');
}

// Close payment modal function
function closePaymentModal() {
    try {
        const paymentModal = document.getElementById('paymentModal');
        
        // Try Bootstrap method first
        if (paymentModalInstance) {
            paymentModalInstance.hide();
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = bootstrap.Modal.getInstance(paymentModal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
        
        // Fallback manual close
        if (paymentModal) {
            paymentModal.classList.remove('show');
            paymentModal.style.display = 'none';
            paymentModal.setAttribute('aria-hidden', 'true');
            paymentModal.removeAttribute('aria-modal');
            paymentModal.removeAttribute('role');
            
            // Remove backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Remove body classes
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        
        console.log('Payment modal closed');
    } catch (error) {
        console.error('Error closing payment modal:', error);
    }
}

// Close success modal function
function closeSuccessModal() {
    try {
        const successModal = document.getElementById('successModal');
        
        // Try Bootstrap method first
        if (successModalInstance) {
            successModalInstance.hide();
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = bootstrap.Modal.getInstance(successModal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
        
        // Fallback manual close
        if (successModal) {
            successModal.classList.remove('show');
            successModal.style.display = 'none';
            successModal.setAttribute('aria-hidden', 'true');
            successModal.removeAttribute('aria-modal');
            successModal.removeAttribute('role');
            
            // Remove backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Remove body classes
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        
        console.log('Success modal closed');
    } catch (error) {
        console.error('Error closing success modal:', error);
    }
}

// Close all modals function
function closeAllModals() {
    closePaymentModal();
    closeSuccessModal();
}

// Enhanced modal show functions with proper initialization
function showPaymentModal() {
    try {
        const paymentModal = document.getElementById('paymentModal');
        
        if (paymentModalInstance) {
            paymentModalInstance.show();
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(paymentModal);
            modal.show();
        } else {
            // Fallback manual show
            paymentModal.classList.add('show');
            paymentModal.style.display = 'block';
            paymentModal.setAttribute('aria-modal', 'true');
            paymentModal.setAttribute('role', 'dialog');
            paymentModal.removeAttribute('aria-hidden');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
            
            // Add body classes
            document.body.classList.add('modal-open');
        }
        
        console.log('Payment modal shown');
    } catch (error) {
        console.error('Error showing payment modal:', error);
    }
}

function showSuccessModal() {
    try {
        const successModal = document.getElementById('successModal');
        
        if (successModalInstance) {
            successModalInstance.show();
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(successModal);
            modal.show();
        } else {
            // Fallback manual show
            successModal.classList.add('show');
            successModal.style.display = 'block';
            successModal.setAttribute('aria-modal', 'true');
            successModal.setAttribute('role', 'dialog');
            successModal.removeAttribute('aria-hidden');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
            
            // Add body classes
            document.body.classList.add('modal-open');
        }
        
        console.log('Success modal shown');
    } catch (error) {
        console.error('Error showing success modal:', error);
    }
}
</script>
@endsection
