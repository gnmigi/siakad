@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Riwayat Pembayaran - {{ config('app.name') }}
@endsection

@section('custom-css')
<style>
    /* Custom CSS for modern premium look */
    :root {
        --accent-indigo: #6366f1;
        --accent-success: #10b981;
        --accent-danger: #ef4444;
        --accent-warning: #f59e0b;
        --accent-info: #3b82f6;
    }

    .payment-history-card {
        background: var(--tblr-card-bg, #fff);
        border-radius: 16px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .payment-history-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
        padding: 1.25rem 1.5rem;
    }
    
    .payment-history-card .card-header .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--tblr-body-color, #1f2937);
        margin: 0;
    }
    
    /* Header Form Controls */
    .payment-history-card .card-header .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        color: white !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .payment-history-card .card-header .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }
    
    .payment-history-card .card-header .form-select {
        border-radius: 8px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        background-color: var(--tblr-card-bg, #fff);
        color: var(--tblr-body-color, #1f2937);
        font-weight: 500;
    }
    .payment-history-card .card-header .form-select:focus {
        border-color: var(--accent-indigo);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    
    .font-monospace {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-weight: 600;
        background: rgba(99, 102, 241, 0.05);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        color: var(--accent-indigo) !important;
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
        width: 100%;
    }
    .table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: var(--tblr-muted, #6b7280);
        border-bottom: 2px solid var(--tblr-border-color, #e2e8f0);
        padding: 1rem 0.75rem;
        background: rgba(0,0,0,0.01);
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
        padding: 1.25rem 0.75rem;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
        color: var(--tblr-body-color, #1f2937);
    }
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.02) !important;
    }
    
    /* Column Widths */
    @media (min-width: 769px) {
        .table { table-layout: fixed; }
        .table th:nth-child(1), .table td:nth-child(1) { width: 13%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 17%; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 23%; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 13%; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 12%; }
        .table th:nth-child(6), .table td:nth-child(6) { width: 10%; text-align: center; }
        .table th:nth-child(7), .table td:nth-child(7) { width: 12%; text-align: center; }
    }

    /* Badges Override */
    .badge {
        padding: 0.35rem 0.75rem !important;
        border-radius: 30px !important;
        font-weight: 700 !important;
        font-size: 0.7rem !important;
        display: inline-flex !important;
        align-items: center;
        gap: 0.25rem;
        border: 1px solid transparent !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        box-shadow: none !important;
    }
    
    .badge.bg-warning {
        background-color: rgba(245, 158, 11, 0.1) !important;
        color: var(--accent-warning) !important;
        border-color: rgba(245, 158, 11, 0.25) !important;
    }
    .badge.bg-success {
        background-color: rgba(16, 185, 129, 0.1) !important;
        color: var(--accent-success) !important;
        border-color: rgba(16, 185, 129, 0.25) !important;
    }
    .badge.bg-info {
        background-color: rgba(59, 130, 246, 0.1) !important;
        color: var(--accent-info) !important;
        border-color: rgba(59, 130, 246, 0.25) !important;
    }
    .badge.bg-danger {
        background-color: rgba(239, 68, 68, 0.1) !important;
        color: var(--accent-danger) !important;
        border-color: rgba(239, 68, 68, 0.25) !important;
    }
    .badge.bg-primary {
        background-color: rgba(99, 102, 241, 0.1) !important;
        color: var(--accent-indigo) !important;
        border-color: rgba(99, 102, 241, 0.25) !important;
    }
    .badge.bg-secondary {
        background-color: rgba(108, 117, 125, 0.1) !important;
        color: #6c757d !important;
        border-color: rgba(108, 117, 125, 0.25) !important;
    }

    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-state h5 { 
        color: var(--tblr-body-color, #1f2937); 
        font-weight: 700; 
    }

    /* Filter Info */
    .filter-info {
        background: rgba(99, 102, 241, 0.06);
        border: 1px solid rgba(99, 102, 241, 0.15);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin: 1.5rem;
        display: none;
        color: var(--accent-indigo);
        font-weight: 600;
    }
    .filter-info.show { display: block; }

    /* Action Buttons Redesign */
    .action-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }
    .action-btn-solid {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        text-align: center;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent !important;
        box-shadow: none;
    }
    .action-btn-solid:hover {
        transform: translateY(-2px);
    }
    
    .btn-view-data {
        background-color: rgba(99, 102, 241, 0.08) !important;
        color: var(--accent-indigo) !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
    }
    .btn-view-data:hover {
        background-color: var(--accent-indigo) !important;
        color: white !important;
    }
    
    .btn-receipt-data {
        background-color: rgba(16, 185, 129, 0.08) !important;
        color: var(--accent-success) !important;
        border-color: rgba(16, 185, 129, 0.2) !important;
    }
    .btn-receipt-data:hover {
        background-color: var(--accent-success) !important;
        color: white !important;
    }
    
    .btn-pdf-data {
        background-color: rgba(59, 130, 246, 0.08) !important;
        color: var(--accent-info) !important;
        border-color: rgba(59, 130, 246, 0.2) !important;
    }
    .btn-pdf-data:hover {
        background-color: var(--accent-info) !important;
        color: white !important;
    }
    
    .btn-acc-data {
        background-color: rgba(107, 114, 128, 0.08) !important;
        color: #6b7280 !important;
        border-color: rgba(107, 114, 128, 0.2) !important;
    }
    .btn-acc-data:hover {
        background-color: #6b7280 !important;
        color: white !important;
    }

    /* Mobile Design System */
    .mobile-history-card { display: none; }
    .desktop-history-table { display: block; }

    .mobile-payment-card {
        background: var(--tblr-card-bg, #fff);
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .mobile-payment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .mobile-payment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed var(--tblr-border-color, #e2e8f0);
    }
    .mobile-payment-title h6 {
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--tblr-body-color, #1f2937);
    }
    .mobile-payment-title small { 
        color: var(--tblr-muted, #6b7280); 
        font-size: 0.8rem; 
    }
    
    .mobile-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.6rem;
    }
    .mobile-detail-label {
        font-size: 0.8rem;
        color: var(--tblr-muted, #6b7280);
        font-weight: 600;
    }
    .mobile-detail-value {
        font-size: 0.85rem;
        color: var(--tblr-body-color, #1f2937);
        font-weight: 700;
        text-align: right;
    }
    .mobile-detail-value.amount {
        color: var(--accent-indigo);
        font-size: 0.95rem;
        font-weight: 800;
    }
    
    .mobile-payment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--tblr-border-color, #e2e8f0);
    }
    .mobile-payment-actions .action-btn-solid {
        flex: 1 1 calc(50% - 6px);
        min-width: 80px;
        padding: 0.5rem 0.25rem;
        font-size: 0.75rem;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .desktop-history-table { display: none; }
        .mobile-history-card { display: block; padding: 1rem; }
        .payment-history-card .card-header { padding: 1rem; }
        .payment-history-card .card-header .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.75rem;
        }
    }

    @media (max-width: 575.98px) {
        .payment-history-card .card-header .d-flex .d-flex {
            flex-direction: column !important;
            gap: 0.5rem !important;
            width: 100%;
        }
        .payment-history-card .card-header select,
        .payment-history-card .card-header a {
            width: 100% !important;
        }
        
        .mobile-payment-card {
            padding: 1rem;
        }
        .mobile-payment-title {
            flex: 1;
            padding-right: 0.5rem;
        }
        .mobile-payment-title h6 {
            font-size: 0.85rem;
            word-break: break-word;
        }
        .mobile-detail-value {
            word-break: break-all;
            max-width: 60%;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card payment-history-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <h5 class="card-title mb-0">Riwayat Pembayaran</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="statusFilter" style="min-width: 150px;">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="processing">Diproses</option>
                                <option value="paid">Dibayar</option>
                                <option value="verified">Terverifikasi</option>
                                <option value="sukses">Sukses</option>
                                <option value="failed">Gagal</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                            <a href="{{ route('mahasiswa.payment.index') }}" class="btn btn-primary btn-sm text-nowrap">
                                Bayar Tagihan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="filterInfo" class="filter-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="filterText">Menampilkan semua pembayaran</span>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="clearFilter()">
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    @if($payments->count() > 0)
                        <div class="desktop-history-table">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kode Transaksi</th>
                                            <th>Tagihan</th>
                                            <th>Metode</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentsTableBody">
                                        @foreach($payments as $payment)
                                        <tr class="payment-row" data-status="{{ $payment->status }}">
                                            <td>
                                                <div class="fw-semibold">{{ $payment->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="font-monospace">{{ $payment->transaction_code ?? $payment->id }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $payment->tagihan->desc ?? $payment->tagihan->name ?? 'Tagihan Akademik' }}</div>
                                                @if($payment->tagihan && $payment->tagihan->code)
                                                    <small class="text-muted">{{ $payment->tagihan->code }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $payment->paymentMethod->name ?? 'Transfer Bank' }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ ucfirst($payment->paymentMethod->type ?? 'System') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                                                @if($payment->fee_amount > 0)
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        +Rp {{ number_format($payment->fee_amount, 0, ',', '.') }} admin
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusStyles = [
                                                        'pending' => 'bg-warning text-dark', 
                                                        'processing' => 'bg-info text-white',
                                                        'paid' => 'bg-primary text-white', 
                                                        'verified' => 'bg-success text-white',
                                                        'sukses' => 'bg-success text-white', 
                                                        'failed' => 'bg-danger text-white', 
                                                        'cancelled' => 'bg-secondary text-white'
                                                    ];
                                                    $badgeStyle = $statusStyles[$payment->status] ?? 'bg-secondary text-white';
                                                    
                                                    $labels = [
                                                        'pending' => 'Menunggu', 'processing' => 'Diproses',
                                                        'paid' => 'Dibayar', 'verified' => 'Terverifikasi',
                                                        'sukses' => 'Sukses', 'failed' => 'Gagal', 'cancelled' => 'Dibatalkan'
                                                    ];
                                                    $label = $labels[$payment->status] ?? ucfirst($payment->status);
                                                @endphp
                                                <span class="badge rounded-pill {{ $badgeStyle }} px-3 py-1">{{ $label }}</span>
                                            </td>
                                            <td>
                                                <div class="action-wrapper">
                                                    <a href="{{ route('mahasiswa.payment.show', $payment->id) }}" class="action-btn-solid btn-view-data">
                                                        Lihat
                                                    </a>
                                                    @if(in_array($payment->status, ['verified', 'sukses']))
                                                        <a href="{{ route('mahasiswa.payment.view-receipt', $payment->id) }}" class="action-btn-solid btn-receipt-data">
                                                            Kwitansi
                                                        </a>
                                                        @if($payment->receipts->count() > 0)
                                                            <a href="{{ route('mahasiswa.payment.receipt', $payment->id) }}" class="action-btn-solid btn-pdf-data">
                                                                PDF
                                                            </a>
                                                        @endif
                                                        @if($payment->tagihan_id)
                                                            <a href="{{ route('mahasiswa.payment.view-accumulated-receipt', $payment->tagihan_id) }}" class="action-btn-solid btn-acc-data">
                                                                Akumulasi
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mobile-history-card">
                            @foreach($payments as $payment)
                                <div class="mobile-payment-card payment-row" data-status="{{ $payment->status }}">
                                    <div class="mobile-payment-header">
                                        <div class="mobile-payment-title">
                                            <h6>{{ $payment->tagihan->desc ?? $payment->tagihan->name ?? 'Tagihan Akademik' }}</h6>
                                            <small>{{ $payment->created_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <div>
                                            @php
                                                $badgeStyle = $statusStyles[$payment->status] ?? 'bg-secondary text-white';
                                                $label = $labels[$payment->status] ?? ucfirst($payment->status);
                                            @endphp
                                            <span class="badge rounded-pill {{ $badgeStyle }}">{{ $label }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="mobile-detail-row">
                                            <span class="mobile-detail-label">Kode Transaksi</span>
                                            <span class="mobile-detail-value font-monospace">{{ $payment->transaction_code ?? $payment->id }}</span>
                                        </div>
                                        <div class="mobile-detail-row">
                                            <span class="mobile-detail-label">Metode</span>
                                            <span class="mobile-detail-value">{{ $payment->paymentMethod->name ?? 'Transfer Bank' }}</span>
                                        </div>
                                        @if($payment->fee_amount > 0)
                                            <div class="mobile-detail-row">
                                                <span class="mobile-detail-label">Biaya Admin</span>
                                                <span class="mobile-detail-value">Rp {{ number_format($payment->fee_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <div class="mobile-detail-row mt-2 pt-2 border-top">
                                            <span class="mobile-detail-label fw-bold text-dark">Total</span>
                                            <span class="mobile-detail-value amount">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="mobile-payment-actions">
                                        <a href="{{ route('mahasiswa.payment.show', $payment->id) }}" class="action-btn-solid btn-view-data">
                                            Lihat Detail
                                        </a>
                                        @if(in_array($payment->status, ['verified', 'sukses']))
                                            <a href="{{ route('mahasiswa.payment.view-receipt', $payment->id) }}" class="action-btn-solid btn-receipt-data">
                                                Kwitansi
                                            </a>
                                            @if($payment->receipts->count() > 0)
                                                <a href="{{ route('mahasiswa.payment.receipt', $payment->id) }}" class="action-btn-solid btn-pdf-data">
                                                    PDF
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="paginationContainer" class="d-flex justify-content-center py-4 border-top">
                            {{ $payments->links() }}
                        </div>

                        <div id="noResultsMessage" class="empty-state" style="display: none;">
                            <h5 class="mt-2">Tidak Ada Transaksi</h5>
                            <p class="text-muted">Tidak ada riwayat pembayaran yang cocok dengan filter yang dipilih.</p>
                            <button class="btn btn-outline-primary mt-2" onclick="clearFilter()">Reset Filter</button>
                        </div>
                    @else
                        <div class="empty-state">
                            <h5 class="mt-2">Belum Ada Riwayat</h5>
                            <p class="text-muted mb-4">Anda belum memiliki riwayat pembayaran tagihan apapun di sistem ini.</p>
                            <a href="{{ route('mahasiswa.payment.index') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                                Bayar Tagihan Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const filterInfo = document.getElementById('filterInfo');
    const filterText = document.getElementById('filterText');
    const paginationContainer = document.getElementById('paginationContainer');
    const noResultsMessage = document.getElementById('noResultsMessage');
    
    if (!statusFilter) return;
    
    const statusMap = {
        'pending': 'Menunggu', 'processing': 'Diproses',
        'paid': 'Dibayar', 'verified': 'Terverifikasi',
        'sukses': 'Sukses', 'failed': 'Gagal', 'cancelled': 'Dibatalkan'
    };
    
    function filterPayments() {
        const selectedStatus = statusFilter.value;
        const rows = document.querySelectorAll('.payment-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (selectedStatus === '' || rowStatus === selectedStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (selectedStatus === '') {
            filterInfo.classList.remove('show');
            filterText.innerHTML = 'Menampilkan semua pembayaran';
        } else {
            filterInfo.classList.add('show');
            filterText.innerHTML = `Status Filter: <strong>${statusMap[selectedStatus] || selectedStatus}</strong>`;
        }
        
        if (visibleCount === 0 && selectedStatus !== '') {
            if(paginationContainer) paginationContainer.style.display = 'none';
            noResultsMessage.style.display = 'block';
        } else {
            if(paginationContainer) paginationContainer.style.display = 'flex';
            noResultsMessage.style.display = 'none';
        }
    }

    window.clearFilter = function() {
        statusFilter.value = '';
        filterPayments();
    };
    
    statusFilter.addEventListener('change', filterPayments);
    filterPayments(); // Run on init
});
</script>
@endsection