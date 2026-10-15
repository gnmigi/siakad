@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Dashboard Pembayaran - {{ config('app.name') }}
@endsection

@section('menu')
    Dashboard Pembayaran
@endsection

@section('submenu')
    Kelola Pembayaran Akademik
@endsection

@section('subdesc')
    Halaman untuk mengelola pembayaran tagihan akademik
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

    /* Stats Cards */
    .stats-card {
        background: var(--tblr-card-bg, #fff);
        border-radius: 16px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        border-color: rgba(99, 102, 241, 0.2);
    }

    .stats-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: transparent;
    }

    .stats-card.card-purple::after { background: var(--accent-indigo); }
    .stats-card.card-success::after { background: var(--accent-success); }
    .stats-card.card-danger::after { background: var(--accent-danger); }
    .stats-card.card-info::after { background: var(--accent-info); }

    .stats-card-body {
        padding: 1.5rem !important;
    }

    .stats-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .stats-card:hover .stats-icon-wrapper {
        transform: scale(1.08) rotate(5deg);
    }

    .stats-icon-wrapper.purple-theme {
        background: rgba(99, 102, 241, 0.1);
        color: var(--accent-indigo);
    }

    .stats-icon-wrapper.success-theme {
        background: rgba(16, 185, 129, 0.1);
        color: var(--accent-success);
    }

    .stats-icon-wrapper.danger-theme {
        background: rgba(239, 68, 68, 0.1);
        color: var(--accent-danger);
    }

    .stats-icon-wrapper.info-theme {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-info);
    }

    .stats-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: var(--tblr-muted, #6b7280);
        margin-bottom: 0.25rem;
    }

    .stats-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--tblr-body-color, #1f2937);
        line-height: 1.2;
    }

    /* Payment Card Container */
    .payment-card {
        background: var(--tblr-card-bg, #fff);
        border-radius: 16px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .payment-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
        padding: 1.25rem 1.5rem;
    }

    .payment-card .card-header .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--tblr-body-color, #1f2937);
        margin: 0;
    }

    /* Table Style Overrides */
    .table-responsive {
        border-radius: 12px;
    }

    .table thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: var(--tblr-muted, #6b7280);
        border-bottom: 2px solid var(--tblr-border-color, #e2e8f0);
        padding: 1rem 0.75rem;
        background: rgba(0,0,0,0.01);
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.02) !important;
    }

    .table tbody td {
        padding: 1.25rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    }

    /* Icon for Tagihan name */
    .tagihan-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.06);
        color: var(--accent-indigo);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* Timeline Activity Feed */
    .timeline-container {
        position: relative;
        padding-left: 1.5rem;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: var(--tblr-border-color, #e2e8f0);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-badge {
        position: absolute;
        left: -1.5rem;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--tblr-card-bg, #fff);
        border: 3px solid #6366f1;
        box-shadow: 0 0 0 3px var(--tblr-card-bg, #fff);
        z-index: 1;
        transition: transform 0.2s ease;
    }

    .timeline-item:hover .timeline-badge {
        transform: scale(1.2);
    }

    .timeline-item.verified .timeline-badge { border-color: var(--accent-success); }
    .timeline-item.pending .timeline-badge { border-color: var(--accent-warning); }
    .timeline-item.paid .timeline-badge { border-color: var(--accent-indigo); }
    .timeline-item.failed .timeline-badge { border-color: var(--accent-danger); }

    .timeline-content {
        background: rgba(99, 102, 241, 0.02);
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .timeline-item:hover .timeline-content {
        background: rgba(99, 102, 241, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
        transform: translateX(4px);
    }

    /* Badges Style Override */
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

    .badge.bg-dark {
        background-color: rgba(107, 114, 128, 0.1) !important;
        color: #6b7280 !important;
        border-color: rgba(107, 114, 128, 0.25) !important;
    }

    .badge.bg-light {
        background-color: rgba(243, 244, 246, 0.1) !important;
        color: #f3f4f6 !important;
        border-color: rgba(243, 244, 246, 0.25) !important;
    }

    /* Pay Button */
    .btn-pay {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white !important;
        border: none;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
        background: linear-gradient(135deg, #4f46e5, #4338ca);
    }

    .btn-pay:active {
        transform: translateY(0);
    }

    /* Refresh Button */
    .btn-refresh {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        background: var(--tblr-card-bg, #fff);
        color: var(--tblr-body-color, #1f2937);
        transition: all 0.2s ease;
    }

    .btn-refresh:hover {
        background: rgba(99, 102, 241, 0.05);
        border-color: var(--accent-indigo);
        color: var(--accent-indigo);
    }

    /* Mobile View Optimized Card Styles */
    .mobile-tagihan-card {
        background: var(--tblr-card-bg, #fff);
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }

    .mobile-tagihan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .mobile-tagihan-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    }

    .mobile-tagihan-title {
        flex: 1;
        margin-right: 1rem;
    }

    .mobile-tagihan-title h6 {
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--tblr-body-color, #1f2937);
    }

    .mobile-tagihan-title small {
        color: var(--tblr-muted, #6b7280);
        font-size: 0.8rem;
    }

    .mobile-tagihan-details {
        margin-bottom: 1rem;
    }

    .mobile-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .mobile-detail-row:last-child {
        margin-bottom: 0;
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
    }

    .mobile-detail-value.amount {
        color: var(--accent-danger);
        font-size: 0.95rem;
    }

    .mobile-tagihan-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--tblr-border-color, #e2e8f0);
    }

    .mobile-tagihan-actions .btn {
        flex: 1;
        width: 100%;
    }

    .mobile-cicilan-info {
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 8px;
        padding: 0.4rem 0.6rem;
        margin-top: 0.5rem;
    }

    .mobile-cicilan-info small {
        color: var(--accent-info) !important;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Visibility Control */
    .mobile-payment-card { display: none; }
    .desktop-payment-table { display: block; }

    @media (max-width: 768px) {
        .desktop-payment-table { display: none; }
        .mobile-payment-card { display: block; }
    }

    @media (max-width: 575.98px) {
        .stats-card-body {
            padding: 1rem !important;
        }
        .stats-icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin-right: 0.75rem !important;
        }
        .stats-label {
            font-size: 0.7rem;
        }
        .stats-number {
            font-size: 1.15rem;
        }
        .payment-card .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.75rem;
        }
        .payment-card .card-header .d-flex {
            width: 100%;
            justify-content: space-between;
        }
        .payment-card .card-header .btn-refresh {
            width: 100%;
            justify-content: center;
        }
    }

    /* Dark Mode Overrides */
    [data-bs-theme="dark"] .stats-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
    }
    [data-bs-theme="dark"] .timeline-container::before {
        background: var(--tblr-border-color-dark, #2b303c);
    }
    [data-bs-theme="dark"] .timeline-content {
        background: rgba(99, 102, 241, 0.04);
    }
    [data-bs-theme="dark"] .timeline-item:hover .timeline-content {
        background: rgba(99, 102, 241, 0.08);
    }
    [data-bs-theme="dark"] .table thead th {
        background: rgba(255,255,255,0.01);
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <section class="row">
        <!-- Statistics Cards -->
        <div class="col-12 col-lg-12 mb-4">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card stats-card card-purple">
                        <div class="card-body stats-card-body d-flex align-items-center">
                            <div class="stats-icon-wrapper purple-theme me-3">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="stats-label">Total Tagihan</div>
                                <div class="stats-number">{{ $stats['total_tagihan'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card stats-card card-success">
                        <div class="card-body stats-card-body d-flex align-items-center">
                            <div class="stats-icon-wrapper success-theme me-3">
                                <i class="fa-solid fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stats-label">Sudah Dibayar</div>
                                <div class="stats-number">{{ $stats['paid_tagihan'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card stats-card card-danger">
                        <div class="card-body stats-card-body d-flex align-items-center">
                            <div class="stats-icon-wrapper danger-theme me-3">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="stats-label">Belum Dibayar</div>
                                <div class="stats-number text-danger">Rp {{ number_format($stats['pending_amount'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card stats-card card-info">
                        <div class="card-body stats-card-body d-flex align-items-center">
                            <div class="stats-icon-wrapper info-theme me-3">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="stats-label">Total Dibayar</div>
                                <div class="stats-number text-success">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Tagihan -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card payment-card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0">
                            <i class="fa-solid fa-file-invoice-dollar text-primary me-1"></i> Tagihan Belum Dibayar
                        </h5>
                        <span class="badge bg-warning">{{ $unpaidTagihan->count() }} Tagihan</span>
                    </div>
                    <button id="refreshPaymentStatus" class="btn btn-refresh">
                        <i class="fa-solid fa-rotate"></i> Refresh Status
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($unpaidTagihan->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="desktop-payment-table">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Tagihan</th>
                                            <th>Jumlah</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Status</th>
                                            <th class="pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unpaidTagihan as $tagihan)
                                        <tr>
                                            <td class="ps-4">
                                                @php
                                                    $tagihanName = 'Tagihan Akademik';
                                                    if (isset($tagihan->desc) && !empty($tagihan->desc)) {
                                                        $tagihanName = $tagihan->desc;
                                                    } elseif (isset($tagihan->group->name) && !empty($tagihan->group->name)) {
                                                        $tagihanName = $tagihan->group->name;
                                                    } elseif (isset($tagihan->name) && !empty($tagihan->name)) {
                                                        $tagihanName = $tagihan->name;
                                                    }
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="tagihan-icon-wrapper me-3">
                                                        @if(str_contains(strtolower($tagihanName), 'spp'))
                                                            <i class="fa-solid fa-university"></i>
                                                        @elseif(str_contains(strtolower($tagihanName), 'pendaftaran'))
                                                            <i class="fa-solid fa-user-plus"></i>
                                                        @else
                                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold" style="font-size: 0.9rem;">{{ $tagihanName }}</h6>
                                                        <small class="text-muted">{{ $tagihan->code }}</small>
                                                        @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                                            <div class="text-info mt-1" style="font-size: 0.75rem; font-weight: 600;">
                                                                <i class="fa-solid fa-coins me-1"></i>Pembayaran Cicilan
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                                    @php
                                                        $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                                                    @endphp
                                                    <div>
                                                        <span class="text-danger font-weight-bold" style="font-size: 0.95rem;">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                                        <div class="text-muted mt-1" style="font-size: 0.75rem;">Sisa dari Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>
                                                        @if($tagihan->paid_amount > 0)
                                                            <div class="text-success" style="font-size: 0.75rem;"><i class="fa-solid fa-circle-check me-1"></i>Terbayar: Rp {{ number_format($tagihan->paid_amount, 0, ',', '.') }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="font-weight-bold" style="font-size: 0.95rem; color: var(--accent-danger);">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tagihan->due_date)
                                                    <span class="badge {{ $tagihan->due_date->isPast() ? 'bg-danger' : ($tagihan->due_date->diffInDays() <= 3 ? 'bg-warning' : 'bg-info') }}">
                                                        <i class="fa-solid fa-calendar-day me-1"></i>{{ $tagihan->due_date->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark">Tidak ada tempo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                                    <span class="badge bg-info payment-status" data-transaction-id="{{ $tagihan->id }}">Cicilan</span>
                                                @else
                                                    <span class="badge bg-warning payment-status" data-transaction-id="{{ $tagihan->id }}">Belum Dibayar</span>
                                                @endif
                                            </td>
                                            <td class="pe-4">
                                                <a href="{{ route('mahasiswa.payment.create', $tagihan->id) }}" class="btn-pay btn-sm">
                                                    <i class="fa-solid fa-credit-card"></i> Bayar
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-payment-card p-3">
                            @foreach($unpaidTagihan as $tagihan)
                                <div class="mobile-tagihan-card">
                                    <div class="mobile-tagihan-header">
                                        <div class="mobile-tagihan-title">
                                            @php
                                                $tagihanName = 'Tagihan Akademik';
                                                if (isset($tagihan->desc) && !empty($tagihan->desc)) {
                                                    $tagihanName = $tagihan->desc;
                                                } elseif (isset($tagihan->group->name) && !empty($tagihan->group->name)) {
                                                    $tagihanName = $tagihan->group->name;
                                                } elseif (isset($tagihan->name) && !empty($tagihan->name)) {
                                                    $tagihanName = $tagihan->name;
                                                }
                                            @endphp
                                            <h6>{{ $tagihanName }}</h6>
                                            <small>{{ $tagihan->code }}</small>
                                            @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                                <div class="mobile-cicilan-info">
                                                    <small><i class="fa-solid fa-coins"></i> Pembayaran Cicilan</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mobile-tagihan-status">
                                            @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                                <span class="badge bg-info payment-status" data-transaction-id="{{ $tagihan->id }}">Cicilan</span>
                                            @else
                                                <span class="badge bg-warning payment-status" data-transaction-id="{{ $tagihan->id }}">Belum Dibayar</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mobile-tagihan-details">
                                        @if(in_array(strtolower($tagihan->status), ['cicilan', 'CICILAN']))
                                            @php
                                                $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                                            @endphp
                                            <div class="mobile-detail-row">
                                                <span class="mobile-detail-label">Sisa Tagihan:</span>
                                                <span class="mobile-detail-value amount">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="mobile-detail-row">
                                                <span class="mobile-detail-label">Total Tagihan:</span>
                                                <span class="mobile-detail-value">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</span>
                                            </div>
                                            @if($tagihan->paid_amount > 0)
                                                <div class="mobile-detail-row">
                                                    <span class="mobile-detail-label">Sudah Dibayar:</span>
                                                    <span class="mobile-detail-value text-success">Rp {{ number_format($tagihan->paid_amount, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        @else
                                            <div class="mobile-detail-row">
                                                <span class="mobile-detail-label">Jumlah Tagihan:</span>
                                                <span class="mobile-detail-value amount">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="mobile-detail-row">
                                            <span class="mobile-detail-label">Jatuh Tempo:</span>
                                            <span class="mobile-detail-value">
                                                @if($tagihan->due_date)
                                                    <span class="badge {{ $tagihan->due_date->isPast() ? 'bg-danger' : ($tagihan->due_date->diffInDays() <= 3 ? 'bg-warning' : 'bg-info') }}">
                                                        {{ $tagihan->due_date->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark">Tidak ada tempo</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mobile-tagihan-actions">
                                        <a href="{{ route('mahasiswa.payment.create', $tagihan->id) }}" class="btn btn-pay w-100">
                                            <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 empty-state">
                            <i class="fa-solid fa-circle-check text-success" style="font-size: 3rem; opacity: 0.8;"></i>
                            <h5 class="mt-3 font-weight-bold">Semua Tagihan Lunas</h5>
                            <p class="text-muted mb-0">Tidak ada tagihan tertunda yang perlu dibayar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card payment-card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-history text-primary me-1"></i> Pembayaran Terbaru
                    </h5>
                    <a href="{{ route('mahasiswa.payment.history') }}" class="btn btn-refresh btn-sm">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($paymentHistory->count() > 0)
                        <div class="timeline-container">
                            @foreach($paymentHistory->take(5) as $payment)
                            <div class="timeline-item {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending' ? 'pending' : ($payment->status === 'paid' ? 'paid' : 'failed')) }}">
                                <div class="timeline-badge"></div>
                                <div class="timeline-content">
                                    @php
                                        $paymentName = 'Tagihan';
                                        if (isset($payment->tagihan)) {
                                            if (isset($payment->tagihan->desc) && !empty($payment->tagihan->desc)) {
                                                $paymentName = $payment->tagihan->desc;
                                            } elseif (isset($payment->tagihan->group->name) && !empty($payment->tagihan->group->name)) {
                                                $paymentName = $payment->tagihan->group->name;
                                            } elseif (isset($payment->tagihan->name) && !empty($payment->tagihan->name)) {
                                                $paymentName = $payment->tagihan->name;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="font-weight-bold mb-1" style="font-size: 0.85rem;">{{ $paymentName }}</h6>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-clock me-1"></i>{{ $payment->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <span class="font-weight-bold d-block" style="font-size: 0.85rem; color: var(--tblr-body-color);">
                                                Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                            </span>
                                            @if($payment->status === 'verified')
                                                <small class="text-success font-weight-bold" style="font-size: 0.7rem;">LUNAS</small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($payment->status === 'verified' && $payment->verified_at)
                                        <div class="text-success mb-2" style="font-size: 0.75rem; font-weight: 600;">
                                            <i class="fa-solid fa-shield-check me-1"></i>Verifikasi: {{ $payment->verified_at->locale('id')->translatedFormat('d M Y H:i') }}
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <span class="payment-status" data-transaction-id="{{ $payment->id }}">
                                            @if($payment->status === 'verified')
                                                <span class="badge bg-success">✅ Terverifikasi</span>
                                            @elseif($payment->status === 'paid')
                                                <span class="badge bg-primary">Sudah Dibayar</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($payment->status === 'sukses')
                                                <span class="badge bg-success">✅ Sukses</span>
                                            @else
                                                <span class="badge bg-dark">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 empty-state">
                            <i class="fa-solid fa-clock-rotate-left text-muted" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada riwayat pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Manual refresh button functionality
    $('#refreshPaymentStatus').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.html();
        
        // Show loading state
        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Memuat...');
        $btn.prop('disabled', true);
        
        // Check all payment statuses
        var promises = [];
        $('.payment-status').each(function() {
            var transactionId = $(this).data('transaction-id');
            if (transactionId) {
                promises.push(checkPaymentStatus(transactionId));
            }
        });
        
        // Wait for all status checks to complete
        Promise.all(promises).then(function() {
            // Show success notification
            showNotification('Status pembayaran berhasil diperbarui', 'success');
        }).catch(function() {
            // Show error notification
            showNotification('Gagal memperbarui status pembayaran', 'error');
        }).finally(function() {
            // Reset button
            $btn.html(originalText);
            $btn.prop('disabled', false);
        });
    });
    
    // Check status when user returns to page (no auto-refresh)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // User returned to page, check status once
            refreshPaymentStatuses();
        }
    });
    
    // Check status when page loads
    refreshPaymentStatuses();
});

function refreshPaymentStatuses() {
    $('.payment-status').each(function() {
        var transactionId = $(this).data('transaction-id');
        if (transactionId) {
            checkPaymentStatus(transactionId);
        }
    });
}

function checkPaymentStatus(transactionId) {
    return $.get(`/mahasiswa/payment/${transactionId}/status`)
        .done(function(response) {
            if (response.success) {
                $(`.payment-status[data-transaction-id="${transactionId}"]`).html(response.status_badge);
                
                // If payment is verified, show success notification
                if (response.verified) {
                    showNotification(`Pembayaran ${response.transaction_code || transactionId} telah terverifikasi!`, 'success');
                }
            }
        })
        .fail(function() {
            console.log('Failed to check status for transaction:', transactionId);
        });
}

function showNotification(message, type = 'info') {
    // Create notification element
    var notification = $('<div class="alert alert-' + (type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info') + ' alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>');
    
    $('body').append(notification);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        notification.remove();
    }, 5000);
}
</script>
@endpush
