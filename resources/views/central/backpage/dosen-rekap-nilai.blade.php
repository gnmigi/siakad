@extends('central.backpage.core-backpage-dosen')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Breadcrumb Navigation -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb breadcrumb-arrows">
                        <li class="breadcrumb-item"><a href="{{ route($spref.'mata-kuliah-render') }}">Mata Kuliah</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Rekap Nilai</li>
                    </ol>
                </nav>
                <div class="page-pretitle">
                    {{ $menus }}
                </div>
                <h2 class="page-title">
                    <i class="fas fa-chart-bar me-2"></i>{{ $pages }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route($spref.'mata-kuliah-render') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-check-circle alert-icon"></i>
                    </div>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-exclamation-triangle alert-icon"></i>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Mata Kuliah Cards -->
        <div class="row mb-4">
            @forelse($mataKuliah as $mk)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card mata-kuliah-card h-100" data-mata-kuliah-id="{{ $mk->id }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-lg" style="background-color: {{ $mk->color ?? '#206bc4' }}20; color: {{ $mk->color ?? '#206bc4' }};">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card-title mb-1">{{ $mk->name }}</div>
                                <div class="text-muted small">
                                    {{ $mk->bsks ?? $mk->sks }} SKS • {{ $mk->programStudi->name ?? 'Program Studi' }}
                                </div>
                                <div class="mt-2">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-muted small">Total Mahasiswa</div>
                                            <div class="fw-bold">{{ $mk->total_mahasiswa ?? 0 }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Belum Dinilai</div>
                                            <div class="fw-bold text-warning">{{ $mk->belum_dinilai ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="btn-list">
                                <button class="btn btn-primary btn-sm flex-fill lihat-rekap-btn" data-mata-kuliah-id="{{ $mk->id }}">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Lihat Rekap
                                </button>
                                <a href="{{ route($spref.'nilai.input-render') }}?mata_kuliah_id={{ $mk->id }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    Input Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty">
                            <div class="empty-img">
                                <img src="{{ asset('dashboard/static/illustrations/undraw_teaching_f1cm.svg') }}" height="128" alt="">
                            </div>
                            <p class="empty-title">Belum ada mata kuliah</p>
                            <p class="empty-subtitle text-secondary">
                                Anda belum memiliki mata kuliah yang diampu. Silakan hubungi admin untuk penugasan mata kuliah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Rekap Detail Section -->
        <div id="rekap-detail-section" style="display: none;">
            <!-- Selected Course Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary-lt border-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg bg-primary text-white">
                                        <i class="fas fa-book"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h3 class="mb-1" id="selected-course-name">-</h3>
                                    <div class="text-muted" id="selected-course-info">-</div>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-outline-primary" id="back-to-courses">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali ke Daftar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Summary (Only visible when printing) -->
            <div class="d-none d-print-block mb-4" style="border-bottom: 1px solid #000; padding-bottom: 15px;">
                <div class="row">
                    <div class="col-3 text-center">
                        <div class="text-secondary small fw-bold">Total Mahasiswa</div>
                        <div class="h3 mb-0 mt-1 fw-bold" id="print-stat-total">0</div>
                    </div>
                    <div class="col-3 text-center">
                        <div class="text-secondary small fw-bold">Lulus (≥60)</div>
                        <div class="h3 mb-0 mt-1 fw-bold text-success" id="print-stat-lulus">0</div>
                    </div>
                    <div class="col-3 text-center">
                        <div class="text-secondary small fw-bold">Tidak Lulus (&lt;60)</div>
                        <div class="h3 mb-0 mt-1 fw-bold text-danger" id="print-stat-tidak-lulus">0</div>
                    </div>
                    <div class="col-3 text-center">
                        <div class="text-secondary small fw-bold">Rata-rata</div>
                        <div class="h3 mb-0 mt-1 fw-bold text-info" id="print-stat-rata-rata">0</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4 d-print-none">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-list">
                                <button class="btn btn-success" id="input-nilai-btn">
                                    <i class="fas fa-plus me-2"></i>
                                    Input Nilai Baru
                                </button>
                                <button class="btn btn-info" id="export-excel-btn">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Export Excel
                                </button>
                                <button class="btn btn-secondary" id="print-rekap-btn">
                                    <i class="fas fa-print me-2"></i>
                                    Print Rekap
                                </button>
                                <button class="btn btn-warning" id="show-belum-dinilai-btn">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Lihat Belum Dinilai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4" id="statistics-cards">
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 mb-1 text-primary" id="stat-total">0</div>
                            <div class="text-muted small">Total Mahasiswa</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 mb-1 text-success" id="stat-lulus">0</div>
                            <div class="text-muted small">Lulus (≥60)</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 mb-1 text-danger" id="stat-tidak-lulus">0</div>
                            <div class="text-muted small">Tidak Lulus (<60)</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 mb-1 text-info" id="stat-rata-rata">0</div>
                            <div class="text-muted small">Rata-rata</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#tab-semua" class="nav-link active" data-bs-toggle="tab">
                                        <i class="fas fa-list me-1"></i>
                                        Semua Mahasiswa
                                        <span class="badge bg-secondary ms-1" id="badge-semua">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-sudah-dinilai" class="nav-link" data-bs-toggle="tab">
                                        <i class="fas fa-check me-1"></i>
                                        Sudah Dinilai
                                        <span class="badge bg-success ms-1" id="badge-sudah">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-belum-dinilai" class="nav-link" data-bs-toggle="tab">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Belum Dinilai
                                        <span class="badge bg-warning ms-1" id="badge-belum">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-lulus" class="nav-link" data-bs-toggle="tab">
                                        <i class="fas fa-graduation-cap me-1"></i>
                                        Lulus
                                        <span class="badge bg-success ms-1" id="badge-lulus">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-tidak-lulus" class="nav-link" data-bs-toggle="tab">
                                        <i class="fas fa-times me-1"></i>
                                        Tidak Lulus
                                        <span class="badge bg-danger ms-1" id="badge-tidak-lulus">0</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Filter Bar -->
                        <div class="card-body border-bottom py-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                <path d="M21 21l-6 -6" />
                                            </svg>
                                        </span>
                                        <input type="text" id="filter-search" class="form-control" placeholder="Cari nama atau NIM mahasiswa...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter-kelas" class="form-select">
                                        <option value="">Semua Kelas</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter-semester" class="form-select">
                                        <option value="">Semua Semester</option>
                                    </select>
                                </div>
                                <div class="col text-end">
                                    <button id="btn-reset-filter" class="btn btn-outline-secondary btn-icon" title="Reset Filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 14l-4 -4l4 -4" />
                                            <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Tab Semua -->
                                <div class="tab-pane active" id="tab-semua">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter" id="table-semua">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Kelas</th>
                                                    <th class="text-center">Status Nilai</th>
                                                    <th class="text-center">Nilai Akhir</th>
                                                    <th class="text-center">Huruf</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center d-print-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-semua">
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <div class="mt-2">Memuat data...</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="card-footer d-flex align-items-center justify-content-between py-2 border-top" id="pagination-semua" style="display: none !important;">
                                        <p class="m-0 text-secondary small">
                                            Showing <span id="pag-start-semua">0</span> to <span id="pag-end-semua">0</span> of <span id="pag-total-semua">0</span> entries
                                        </p>
                                        <ul class="pagination m-0 ms-auto" id="pag-list-semua"></ul>
                                    </div>
                                </div>

                                <!-- Tab Sudah Dinilai -->
                                <div class="tab-pane" id="tab-sudah-dinilai">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter" id="table-sudah-dinilai">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    @foreach($bobotNilaiSettings ?? [] as $setting)
                                                        <th class="text-center">{{ $setting->component_name }}</th>
                                                    @endforeach
                                                    <th class="text-center">Nilai Akhir</th>
                                                    <th class="text-center">Huruf</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center d-print-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-sudah-dinilai">
                                                <!-- Data will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="card-footer d-flex align-items-center justify-content-between py-2 border-top" id="pagination-sudah-dinilai" style="display: none !important;">
                                        <p class="m-0 text-secondary small">
                                            Showing <span id="pag-start-sudah-dinilai">0</span> to <span id="pag-end-sudah-dinilai">0</span> of <span id="pag-total-sudah-dinilai">0</span> entries
                                        </p>
                                        <ul class="pagination m-0 ms-auto" id="pag-list-sudah-dinilai"></ul>
                                    </div>
                                </div>

                                <!-- Tab Belum Dinilai -->
                                <div class="tab-pane" id="tab-belum-dinilai">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter" id="table-belum-dinilai">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Kelas</th>
                                                    <th>Program Studi</th>
                                                    <th class="text-center d-print-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-belum-dinilai">
                                                <!-- Data will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="card-footer d-flex align-items-center justify-content-between py-2 border-top" id="pagination-belum-dinilai" style="display: none !important;">
                                        <p class="m-0 text-secondary small">
                                            Showing <span id="pag-start-belum-dinilai">0</span> to <span id="pag-end-belum-dinilai">0</span> of <span id="pag-total-belum-dinilai">0</span> entries
                                        </p>
                                        <ul class="pagination m-0 ms-auto" id="pag-list-belum-dinilai"></ul>
                                    </div>
                                </div>

                                <!-- Tab Lulus -->
                                <div class="tab-pane" id="tab-lulus">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter" id="table-lulus">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th class="text-center">Nilai Akhir</th>
                                                    <th class="text-center">Huruf</th>
                                                    <th class="text-center d-print-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-lulus">
                                                <!-- Data will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="card-footer d-flex align-items-center justify-content-between py-2 border-top" id="pagination-lulus" style="display: none !important;">
                                        <p class="m-0 text-secondary small">
                                            Showing <span id="pag-start-lulus">0</span> to <span id="pag-end-lulus">0</span> of <span id="pag-total-lulus">0</span> entries
                                        </p>
                                        <ul class="pagination m-0 ms-auto" id="pag-list-lulus"></ul>
                                    </div>
                                </div>

                                <!-- Tab Tidak Lulus -->
                                <div class="tab-pane" id="tab-tidak-lulus">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter" id="table-tidak-lulus">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th class="text-center">Nilai Akhir</th>
                                                    <th class="text-center">Huruf</th>
                                                    <th class="text-center d-print-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-tidak-lulus">
                                                <!-- Data will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="card-footer d-flex align-items-center justify-content-between py-2 border-top" id="pagination-tidak-lulus" style="display: none !important;">
                                        <p class="m-0 text-secondary small">
                                            Showing <span id="pag-start-tidak-lulus">0</span> to <span id="pag-end-tidak-lulus">0</span> of <span id="pag-total-tidak-lulus">0</span> entries
                                        </p>
                                        <ul class="pagination m-0 ms-auto" id="pag-list-tidak-lulus"></ul>
                                    </div>
                                </div>
                            </div>
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
/* Mobile-First Responsive Design */
.mata-kuliah-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.mata-kuliah-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.mata-kuliah-card.active {
    border-color: var(--tblr-primary);
    background-color: var(--tblr-primary-lt);
}

/* Status badges */
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Grade badges with better contrast */
.badge {
    font-weight: 600 !important;
    color: white !important;
}

.badge.bg-info {
    background-color: #0ea5e9 !important;
    color: white !important;
}

.badge.bg-success {
    background-color: #10b981 !important;
    color: white !important;
}

.badge.bg-warning {
    background-color: #f59e0b !important;
    color: white !important;
}

.badge.bg-danger {
    background-color: #ef4444 !important;
    color: white !important;
}

.badge.bg-secondary {
    background-color: #6b7280 !important;
    color: white !important;
}

.badge.bg-orange {
    background-color: #f97316 !important;
    color: white !important;
}

/* Table responsive improvements */
.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--tblr-muted);
    border-bottom: 2px solid var(--tblr-border-color);
}

/* Tab styling */
.nav-tabs .nav-link {
    border: none;
    color: var(--tblr-muted);
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: var(--tblr-primary);
    border-bottom: 2px solid var(--tblr-primary);
    background: none;
}

.nav-tabs .nav-link:hover {
    border: none;
    color: var(--tblr-primary);
}

/* Loading state */
.loading-row {
    height: 200px;
}

/* Alert Icons */
.alert-icon {
    width: 24px;
    height: 24px;
    margin-right: 0.5rem;
}

/* Breadcrumb Styling */
.breadcrumb-arrows .breadcrumb-item + .breadcrumb-item::before {
    content: "→";
    color: var(--tblr-muted);
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-list .btn {
        margin-bottom: 0.5rem;
    }
    
    .nav-tabs {
        flex-wrap: wrap;
    }
    
    .nav-tabs .nav-link {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .h1 {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .container-xl {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .mata-kuliah-card .btn-list {
        flex-direction: column;
    }
    
    .mata-kuliah-card .btn-list .btn {
        width: 100%;
        margin-bottom: 0.25rem;
    }
    
    .statistics-cards .card-body {
        padding: 1rem;
    }
}

/* Dark Mode Support */
[data-bs-theme="dark"] .mata-kuliah-card {
    background-color: var(--tblr-dark-bg-surface);
    border-color: var(--tblr-dark-border-color);
}

[data-bs-theme="dark"] .mata-kuliah-card:hover {
    box-shadow: 0 4px 12px rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .mata-kuliah-card.active {
    border-color: var(--tblr-primary);
    background-color: var(--tblr-primary-lt);
}

/* Print Styles */
@media print {
    /* Hide layout elements, buttons, pagination, sidebars, and desktop widgets */
    .d-print-none,
    .btn,
    .nav-tabs,
    .card-actions,
    .card-footer,
    .card-body.border-bottom,
    #statistics-cards,
    .avatar,
    th.d-print-none,
    td.d-print-none {
        display: none !important;
    }
    
    /* Reset background and padding for printing */
    body, .page, .page-wrapper, .page-body {
        background: #fff !important;
        color: #000 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .container-xl {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    
    /* Card layout normalization for print */
    .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }
    
    .card-body {
        padding: 0 !important;
    }

    /* Print Header layout */
    #rekap-detail-section .card.bg-primary-lt {
        background-color: transparent !important;
        border: none !important;
        border-bottom: 2px solid #000 !important;
        border-radius: 0 !important;
        padding: 10px 0 !important;
        margin-bottom: 15px !important;
    }
    
    #selected-course-name {
        font-size: 1.5rem !important;
        font-weight: bold !important;
        color: #000 !important;
    }
    
    #selected-course-info {
        font-size: 0.85rem !important;
        color: #333 !important;
        margin-top: 5px;
    }

    /* Table formatting for standard print paper width */
    .table-responsive {
        overflow: visible !important;
        width: 100% !important;
    }
    
    .table {
        width: 100% !important;
        table-layout: auto !important;
        font-size: 0.75rem !important;
        border-collapse: collapse !important;
        color: #000 !important;
    }
    
    .table th {
        border-bottom: 2px solid #000 !important;
        color: #000 !important;
        font-weight: bold !important;
        padding: 6px 4px !important;
    }
    
    .table td {
        border-bottom: 1px solid #ddd !important;
        padding: 6px 4px !important;
    }

    /* Print badge optimization */
    .badge {
        border: 1px solid #6b7280 !important;
        color: #000 !important;
        background: transparent !important;
        font-weight: normal !important;
        padding: 2px 4px !important;
    }
    
    .badge.bg-success {
        border-color: #10b981 !important;
    }
    
    .badge.bg-warning {
        border-color: #f59e0b !important;
    }
    
    .badge.bg-danger {
        border-color: #ef4444 !important;
    }
    
    /* Force page breaks to be clean */
    tr {
        page-break-inside: avoid !important;
    }
}
</style>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentMataKuliahId = null;
    let allData = [];
    let isPrinting = false;
    let currentPage = {
        'semua': 1,
        'sudah-dinilai': 1,
        'belum-dinilai': 1,
        'lulus': 1,
        'tidak-lulus': 1
    };
    const rowsPerPage = 25;
    
    // Initialize mata kuliah cards
    initializeMataKuliahCards();
    
    // Event listeners
    document.addEventListener('click', handleButtonClicks);
    
    // Filter Event Listeners
    const searchInput = document.getElementById('filter-search');
    const kelasSelect = document.getElementById('filter-kelas');
    const semesterSelect = document.getElementById('filter-semester');
    const resetBtn = document.getElementById('btn-reset-filter');
    
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (kelasSelect) kelasSelect.addEventListener('change', applyFilters);
    if (semesterSelect) semesterSelect.addEventListener('change', applyFilters);
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (kelasSelect) kelasSelect.value = '';
            if (semesterSelect) semesterSelect.value = '';
            applyFilters();
        });
    }
    
    function initializeMataKuliahCards() {
        // Data sudah ditampilkan dari controller, tidak perlu load lagi
        console.log('Mata kuliah cards initialized with real data from controller');
        
        // Auto show detail if mata_kuliah_id is in URL on page load
        const urlParams = new URLSearchParams(window.location.search);
        const matkulId = urlParams.get('mata_kuliah_id');
        if (matkulId) {
            showRekapDetail(matkulId);
        }
    }
    
    function handleButtonClicks(e) {
        if (e.target.closest('.lihat-rekap-btn')) {
            const btn = e.target.closest('.lihat-rekap-btn');
            const mataKuliahId = btn.dataset.mataKuliahId;
            showRekapDetail(mataKuliahId);
        }
        
        if (e.target.closest('#back-to-courses')) {
            hideRekapDetail();
        }
        
        if (e.target.closest('#input-nilai-btn')) {
            if (currentMataKuliahId) {
                window.location.href = `{{ route($spref.'nilai.input-render') }}?mata_kuliah_id=${currentMataKuliahId}`;
            }
        }
        
        if (e.target.closest('#export-excel-btn')) {
            exportToExcel();
        }
        
        if (e.target.closest('#print-rekap-btn')) {
            printRekap();
        }
        
        if (e.target.closest('#show-belum-dinilai-btn')) {
            showBelumDinilaiTab();
        }
    }
    
    function showRekapDetail(mataKuliahId) {
        currentMataKuliahId = mataKuliahId;
        
        // Update URL query parameter without page reload
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?mata_kuliah_id=' + mataKuliahId;
        window.history.replaceState({ path: newUrl }, '', newUrl);
        
        // Hide mata kuliah cards
        document.querySelector('.row.mb-4').style.display = 'none';
        
        // Show rekap detail
        document.getElementById('rekap-detail-section').style.display = 'block';
        
        // Update selected course info
        updateSelectedCourseInfo(mataKuliahId);
        
        // Load rekap data
        loadRekapData(mataKuliahId);
    }
    
    function hideRekapDetail() {
        // Show mata kuliah cards
        document.querySelector('.row.mb-4').style.display = 'block';
        
        // Hide rekap detail
        document.getElementById('rekap-detail-section').style.display = 'none';
        
        // Clear URL query parameter without page reload
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({ path: newUrl }, '', newUrl);
        
        currentMataKuliahId = null;
    }
    
    function updateSelectedCourseInfo(mataKuliahId) {
        // Find the selected mata kuliah card
        const card = document.querySelector(`[data-mata-kuliah-id="${mataKuliahId}"]`);
        if (card) {
            const courseName = card.querySelector('.card-title').textContent;
            const courseInfo = card.querySelector('.text-muted.small').textContent;
            
            document.getElementById('selected-course-name').textContent = courseName;
            document.getElementById('selected-course-info').textContent = courseInfo;
        }
    }
    
    function loadRekapData(mataKuliahId) {
        // Show loading state
        showLoadingState();
        
        // Reset filter inputs
        const searchInput = document.getElementById('filter-search');
        const kelasSelect = document.getElementById('filter-kelas');
        const semesterSelect = document.getElementById('filter-semester');
        if (searchInput) searchInput.value = '';
        if (kelasSelect) kelasSelect.value = '';
        if (semesterSelect) semesterSelect.value = '';
        
        // Make AJAX call to get optimized data from controller (single query with all data)
        fetch('{{ route($spref.'nilai.mahasiswa-by-matkul') }}?mata_kuliah_id=' + mataKuliahId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.mahasiswa) {
                    // Data already includes nilai information from optimized query
                    const allData = data.mahasiswa.map(mahasiswa => ({
                        id: mahasiswa.id,
                        nim: mahasiswa.nim,
                        nama: mahasiswa.name,
                        kelas: mahasiswa.kelas_name || '-',
                        prodi: mahasiswa.program_studi_name || '-',
                        semester: mahasiswa.semester || '-',
                        tugas: mahasiswa.tugas,
                        quiz: mahasiswa.quiz,
                        uts: mahasiswa.uts,
                        uas: mahasiswa.uas,
                        nilai_akhir: mahasiswa.nilai_akhir,
                        huruf: mahasiswa.huruf_nilai,
                        status: mahasiswa.status,
                        hasNilai: mahasiswa.has_nilai == 1
                    }));
                    
                    window.allData = allData;
                    populateFilterDropdowns(allData);
                    displayRekapData(allData);
                    updateStatistics(allData);
                    updateTabBadges(allData);
                } else {
                    const allData = [];
                    window.allData = allData;
                    populateFilterDropdowns(allData);
                    displayRekapData(allData);
                    updateStatistics(allData);
                    updateTabBadges(allData);
                }
            })
            .catch(error => {
                console.error('Error loading rekap data:', error);
                showErrorState();
            });
    }
    
    function showLoadingState() {
        const tables = ['semua', 'sudah-dinilai', 'belum-dinilai', 'lulus', 'tidak-lulus'];
        tables.forEach(table => {
            const tbody = document.getElementById(`tbody-${table}`);
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Memuat data...</div>
                    </td>
                </tr>
            `;
        });
    }
    
    function showErrorState() {
        const tables = ['semua', 'sudah-dinilai', 'belum-dinilai', 'lulus', 'tidak-lulus'];
        tables.forEach(table => {
            const tbody = document.getElementById(`tbody-${table}`);
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <div>Gagal memuat data. Silakan coba lagi.</div>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    
    function displayRekapData(data) {
        displaySemuaData(data);
        displaySudahDinilaiData(data.filter(item => item.hasNilai));
        displayBelumDinilaiData(data.filter(item => !item.hasNilai));
        displayLulusData(data.filter(item => item.hasNilai && item.status === 'Lulus'));
        displayTidakLulusData(data.filter(item => item.hasNilai && item.status === 'Tidak Lulus'));
    }
    
    function resetPaginationPages() {
        currentPage = {
            'semua': 1,
            'sudah-dinilai': 1,
            'belum-dinilai': 1,
            'lulus': 1,
            'tidak-lulus': 1
        };
    }

    function refreshTabTable(tabKey, fullData) {
        if (tabKey === 'semua') displaySemuaData(fullData);
        else if (tabKey === 'sudah-dinilai') displaySudahDinilaiData(fullData);
        else if (tabKey === 'belum-dinilai') displayBelumDinilaiData(fullData);
        else if (tabKey === 'lulus') displayLulusData(fullData);
        else if (tabKey === 'tidak-lulus') displayTidakLulusData(fullData);
    }

    function renderPagination(tabKey, totalRows, fullData) {
        const page = currentPage[tabKey];
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        const pagContainer = document.getElementById(`pagination-${tabKey}`);
        
        if (!pagContainer) return;
        
        if (totalRows === 0) {
            pagContainer.setAttribute('style', 'display: none !important;');
            return;
        }
        
        pagContainer.removeAttribute('style');
        
        const start = totalRows > 0 ? (page - 1) * rowsPerPage + 1 : 0;
        const end = Math.min(page * rowsPerPage, totalRows);
        
        document.getElementById(`pag-start-${tabKey}`).textContent = start;
        document.getElementById(`pag-end-${tabKey}`).textContent = end;
        document.getElementById(`pag-total-${tabKey}`).textContent = totalRows;
        
        const listContainer = document.getElementById(`pag-list-${tabKey}`);
        listContainer.innerHTML = '';
        
        // Prev button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" tabindex="-1" aria-disabled="${page === 1}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M15 6l-6 6l6 6" />
                </svg>
            </a>
        `;
        if (page > 1) {
            prevLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage[tabKey]--;
                refreshTabTable(tabKey, fullData);
            });
        }
        listContainer.appendChild(prevLi);
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, page - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === page ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage[tabKey] = i;
                refreshTabTable(tabKey, fullData);
            });
            listContainer.appendChild(pageLi);
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 6l6 6l-6 6" />
                </svg>
            </a>
        `;
        if (page < totalPages) {
            nextLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage[tabKey]++;
                refreshTabTable(tabKey, fullData);
            });
        }
        listContainer.appendChild(nextLi);
    }

    function displaySemuaData(data) {
        const tbody = document.getElementById('tbody-semua');
        tbody.innerHTML = '';
        
        const totalRows = data.length;
        const page = currentPage['semua'];
        const startIdx = isPrinting ? 0 : (page - 1) * rowsPerPage;
        const endIdx = isPrinting ? totalRows : Math.min(startIdx + rowsPerPage, totalRows);
        const paginatedData = data.slice(startIdx, endIdx);
        
        if (totalRows === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="text-muted">Tidak ada data mahasantri</div>
                    </td>
                </tr>
            `;
            renderPagination('semua', 0, data);
            return;
        }

        paginatedData.forEach((item, index) => {
            const actualIndex = startIdx + index + 1;
            const statusNilai = item.hasNilai ? 
                '<span class="badge bg-success">Sudah Dinilai</span>' : 
                '<span class="badge bg-warning">Belum Dinilai</span>';
            
            const nilaiAkhir = item.hasNilai && item.nilai_akhir !== null ? 
                `<strong>${parseFloat(item.nilai_akhir).toFixed(1)}</strong>` : 
                '<span class="text-muted">-</span>';
            
            const huruf = item.hasNilai ? 
                `<span class="badge ${getHurufBadgeClass(item.huruf)}">${item.huruf}</span>` : 
                '<span class="text-muted">-</span>';
            
            const status = item.hasNilai ? 
                `<span class="badge ${item.status === 'Lulus' ? 'bg-success' : 'bg-danger'}">${item.status}</span>` : 
                '<span class="text-muted">-</span>';
            
            const aksi = item.hasNilai ? 
                `<button class="btn btn-sm btn-outline-primary" onclick="editNilai(${item.id})">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>` : 
                `<button class="btn btn-sm btn-success" onclick="inputNilai(${item.id})">
                    <i class="fas fa-plus me-1"></i>Input
                </button>`;
            
            tbody.innerHTML += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><span class="badge bg-secondary">${item.nim}</span></td>
                    <td>${item.nama}</td>
                    <td>${item.kelas}</td>
                    <td class="text-center">${statusNilai}</td>
                    <td class="text-center">${nilaiAkhir}</td>
                    <td class="text-center">${huruf}</td>
                    <td class="text-center">${status}</td>
                    <td class="text-center d-print-none">${aksi}</td>
                </tr>
            `;
        });
        
        if (!isPrinting) {
            renderPagination('semua', totalRows, data);
        } else {
            const pagContainer = document.getElementById('pagination-semua');
            if (pagContainer) pagContainer.setAttribute('style', 'display: none !important;');
        }
    }
    
    function displaySudahDinilaiData(data) {
        const tbody = document.getElementById('tbody-sudah-dinilai');
        tbody.innerHTML = '';
        
        const totalRows = data.length;
        const page = currentPage['sudah-dinilai'];
        const startIdx = isPrinting ? 0 : (page - 1) * rowsPerPage;
        const endIdx = isPrinting ? totalRows : Math.min(startIdx + rowsPerPage, totalRows);
        const paginatedData = data.slice(startIdx, endIdx);
        
        if (totalRows === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="text-muted">Belum ada mahasiswa yang dinilai</div>
                    </td>
                </tr>
            `;
            renderPagination('sudah-dinilai', 0, data);
            return;
        }
        
        paginatedData.forEach((item, index) => {
            const actualIndex = startIdx + index + 1;
            tbody.innerHTML += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><span class="badge bg-secondary">${item.nim}</span></td>
                    <td>${item.nama}</td>
                    <td class="text-center">${item.tugas}</td>
                    <td class="text-center">${item.quiz}</td>
                    <td class="text-center">${item.uts}</td>
                    <td class="text-center">${item.uas}</td>
                    <td class="text-center"><strong>${parseFloat(item.nilai_akhir || 0).toFixed(1)}</strong></td>
                    <td class="text-center"><span class="badge ${getHurufBadgeClass(item.huruf)}">${item.huruf}</span></td>
                    <td class="text-center"><span class="badge ${item.status === 'Lulus' ? 'bg-success' : 'bg-danger'}">${item.status}</span></td>
                    <td class="text-center d-print-none">
                        <button class="btn btn-sm btn-outline-primary" onclick="editNilai(${item.id})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if (!isPrinting) {
            renderPagination('sudah-dinilai', totalRows, data);
        } else {
            const pagContainer = document.getElementById('pagination-sudah-dinilai');
            if (pagContainer) pagContainer.setAttribute('style', 'display: none !important;');
        }
    }
    
    function displayBelumDinilaiData(data) {
        const tbody = document.getElementById('tbody-belum-dinilai');
        tbody.innerHTML = '';
        
        const totalRows = data.length;
        const page = currentPage['belum-dinilai'];
        const startIdx = isPrinting ? 0 : (page - 1) * rowsPerPage;
        const endIdx = isPrinting ? totalRows : Math.min(startIdx + rowsPerPage, totalRows);
        const paginatedData = data.slice(startIdx, endIdx);
        
        if (totalRows === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <div>Semua mahasiswa sudah dinilai!</div>
                        </div>
                    </td>
                </tr>
            `;
            renderPagination('belum-dinilai', 0, data);
            return;
        }
        
        paginatedData.forEach((item, index) => {
            const actualIndex = startIdx + index + 1;
            tbody.innerHTML += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><span class="badge bg-secondary">${item.nim}</span></td>
                    <td>${item.nama}</td>
                    <td>${item.kelas}</td>
                    <td>${item.prodi}</td>
                    <td class="text-center d-print-none">
                        <button class="btn btn-sm btn-success" onclick="inputNilai(${item.id})">
                            <i class="fas fa-plus me-1"></i>Input Nilai
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if (!isPrinting) {
            renderPagination('belum-dinilai', totalRows, data);
        } else {
            const pagContainer = document.getElementById('pagination-belum-dinilai');
            if (pagContainer) pagContainer.setAttribute('style', 'display: none !important;');
        }
    }
    
    function displayLulusData(data) {
        const tbody = document.getElementById('tbody-lulus');
        tbody.innerHTML = '';
        
        const totalRows = data.length;
        const page = currentPage['lulus'];
        const startIdx = isPrinting ? 0 : (page - 1) * rowsPerPage;
        const endIdx = isPrinting ? totalRows : Math.min(startIdx + rowsPerPage, totalRows);
        const paginatedData = data.slice(startIdx, endIdx);
        
        if (totalRows === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">Belum ada mahasiswa yang lulus</div>
                    </td>
                </tr>
            `;
            renderPagination('lulus', 0, data);
            return;
        }
        
        paginatedData.forEach((item, index) => {
            const actualIndex = startIdx + index + 1;
            tbody.innerHTML += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><span class="badge bg-secondary">${item.nim}</span></td>
                    <td>${item.nama}</td>
                    <td class="text-center"><strong>${parseFloat(item.nilai_akhir || 0).toFixed(1)}</strong></td>
                    <td class="text-center"><span class="badge ${getHurufBadgeClass(item.huruf)}">${item.huruf}</span></td>
                    <td class="text-center d-print-none">
                        <button class="btn btn-sm btn-outline-primary" onclick="editNilai(${item.id})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if (!isPrinting) {
            renderPagination('lulus', totalRows, data);
        } else {
            const pagContainer = document.getElementById('pagination-lulus');
            if (pagContainer) pagContainer.setAttribute('style', 'display: none !important;');
        }
    }
    
    function displayTidakLulusData(data) {
        const tbody = document.getElementById('tbody-tidak-lulus');
        tbody.innerHTML = '';
        
        const totalRows = data.length;
        const page = currentPage['tidak-lulus'];
        const startIdx = isPrinting ? 0 : (page - 1) * rowsPerPage;
        const endIdx = isPrinting ? totalRows : Math.min(startIdx + rowsPerPage, totalRows);
        const paginatedData = data.slice(startIdx, endIdx);
        
        if (totalRows === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-success">
                            <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                            <div>Semua mahasiswa lulus!</div>
                        </div>
                    </td>
                </tr>
            `;
            renderPagination('tidak-lulus', 0, data);
            return;
        }
        
        paginatedData.forEach((item, index) => {
            const actualIndex = startIdx + index + 1;
            tbody.innerHTML += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><span class="badge bg-secondary">${item.nim}</span></td>
                    <td>${item.nama}</td>
                    <td class="text-center"><strong>${parseFloat(item.nilai_akhir || 0).toFixed(1)}</strong></td>
                    <td class="text-center"><span class="badge ${getHurufBadgeClass(item.huruf)}">${item.huruf}</span></td>
                    <td class="text-center d-print-none">
                        <button class="btn btn-sm btn-outline-primary" onclick="editNilai(${item.id})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if (!isPrinting) {
            renderPagination('tidak-lulus', totalRows, data);
        } else {
            const pagContainer = document.getElementById('pagination-tidak-lulus');
            if (pagContainer) pagContainer.setAttribute('style', 'display: none !important;');
        }
    }
    
    function populateFilterDropdowns(data) {
        const filterKelas = document.getElementById('filter-kelas');
        const filterSemester = document.getElementById('filter-semester');
        if (!filterKelas || !filterSemester) return;
        
        const selectedKelas = filterKelas.value;
        const selectedSemester = filterSemester.value;
        
        filterKelas.innerHTML = '<option value="">Semua Kelas</option>';
        filterSemester.innerHTML = '<option value="">Semua Semester</option>';
        
        // Populate unique kelas
        const uniqueKelas = [...new Set(data.map(item => item.kelas))].filter(val => val && val !== '-').sort();
        uniqueKelas.forEach(kelas => {
            const option = document.createElement('option');
            option.value = kelas;
            option.textContent = kelas;
            if (kelas === selectedKelas) option.selected = true;
            filterKelas.appendChild(option);
        });
        
        // Populate unique semesters
        const uniqueSemester = [...new Set(data.map(item => item.semester))].filter(val => val && val !== '-').sort((a, b) => a - b);
        uniqueSemester.forEach(sem => {
            const option = document.createElement('option');
            option.value = sem;
            option.textContent = 'Semester ' + sem;
            if (sem.toString() === selectedSemester) option.selected = true;
            filterSemester.appendChild(option);
        });
    }

    function applyFilters() {
        resetPaginationPages();
        const searchQuery = (document.getElementById('filter-search')?.value || '').toLowerCase().trim();
        const selectedKelas = document.getElementById('filter-kelas')?.value || '';
        const selectedSemester = document.getElementById('filter-semester')?.value || '';
        
        let filtered = window.allData || [];
        
        if (searchQuery) {
            filtered = filtered.filter(item => 
                (item.nama && item.nama.toLowerCase().includes(searchQuery)) || 
                (item.nim && item.nim.toLowerCase().includes(searchQuery))
            );
        }
        
        if (selectedKelas) {
            filtered = filtered.filter(item => item.kelas === selectedKelas);
        }
        
        if (selectedSemester) {
            filtered = filtered.filter(item => item.semester.toString() === selectedSemester);
        }
        
        displayRekapData(filtered);
        updateStatistics(filtered);
        updateTabBadges(filtered);
    }

    function updateStatistics(data) {
        const total = data.length;
        const sudahDinilai = data.filter(item => item.hasNilai).length;
        const lulus = data.filter(item => item.hasNilai && item.status === 'Lulus').length;
        const tidakLulus = data.filter(item => item.hasNilai && item.status === 'Tidak Lulus').length;
        const rataRata = sudahDinilai > 0 ? 
            data.filter(item => item.hasNilai).reduce((sum, item) => sum + (parseFloat(item.nilai_akhir) || 0), 0) / sudahDinilai : 0;
        
        document.getElementById('stat-total').textContent = total;
        document.getElementById('stat-lulus').textContent = lulus;
        document.getElementById('stat-tidak-lulus').textContent = tidakLulus;
        document.getElementById('stat-rata-rata').textContent = rataRata.toFixed(1);

        // Also update print summary stats
        const printTotal = document.getElementById('print-stat-total');
        if (printTotal) printTotal.textContent = total;
        const printLulus = document.getElementById('print-stat-lulus');
        if (printLulus) printLulus.textContent = lulus;
        const printTidakLulus = document.getElementById('print-stat-tidak-lulus');
        if (printTidakLulus) printTidakLulus.textContent = tidakLulus;
        const printRataRata = document.getElementById('print-stat-rata-rata');
        if (printRataRata) printRataRata.textContent = rataRata.toFixed(1);
    }
    
    function updateTabBadges(data) {
        const total = data.length;
        const sudahDinilai = data.filter(item => item.hasNilai).length;
        const belumDinilai = data.filter(item => !item.hasNilai).length;
        const lulus = data.filter(item => item.hasNilai && item.status === 'Lulus').length;
        const tidakLulus = data.filter(item => item.hasNilai && item.status === 'Tidak Lulus').length;
        
        document.getElementById('badge-semua').textContent = total;
        document.getElementById('badge-sudah').textContent = sudahDinilai;
        document.getElementById('badge-belum').textContent = belumDinilai;
        document.getElementById('badge-lulus').textContent = lulus;
        document.getElementById('badge-tidak-lulus').textContent = tidakLulus;
    }
    
    function getHurufBadgeClass(huruf) {
        // Dynamic badge class based on grade letter
        if (!huruf || huruf === 'N/A' || huruf === '-') return 'bg-secondary';
        
        // Use first character to determine general category
        const firstChar = huruf.charAt(0).toUpperCase();
        switch(firstChar) {
            case 'A': return 'bg-success';
            case 'B': return 'bg-info';
            case 'C': return 'bg-warning';
            case 'D': return 'bg-orange';
            case 'E': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
    
    function showBelumDinilaiTab() {
        // Switch to belum dinilai tab
        const belumDinilaiTab = document.querySelector('a[href="#tab-belum-dinilai"]');
        const tab = new bootstrap.Tab(belumDinilaiTab);
        tab.show();
    }
    
    function exportToExcel() {
        if (!currentMataKuliahId) return;
        window.location.href = `{{ route($spref.'nilai.upload-template') }}?mata_kuliah_id=${currentMataKuliahId}`;
    }
    
    function printRekap() {
        // Set isPrinting to true and re-render to display all matching data
        isPrinting = true;
        applyFilters();
        
        // Give the browser 150ms to reflow and repaint the DOM
        setTimeout(function() {
            window.print();
            
            // Revert isPrinting to false and restore paginated layout
            isPrinting = false;
            applyFilters();
        }, 150);
    }

    // Intercept Ctrl+P (or Cmd+P on Mac) to use our custom print logic
    window.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
            e.preventDefault();
            printRekap();
        }
    });
    
    // Global functions for button actions
    window.editNilai = function(mahasiswaId) {
        if (currentMataKuliahId) {
            window.location.href = `{{ route($spref.'nilai.input-render') }}?mata_kuliah_id=${currentMataKuliahId}&mahasiswa_id=${mahasiswaId}`;
        }
    };
    
    window.inputNilai = function(mahasiswaId) {
        if (currentMataKuliahId) {
            window.location.href = `{{ route($spref.'nilai.input-render') }}?mata_kuliah_id=${currentMataKuliahId}&mahasiswa_id=${mahasiswaId}`;
        }
    };
});
</script>
@endsection
