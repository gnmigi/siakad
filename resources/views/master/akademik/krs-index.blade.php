@extends('core-themes.core-backpage')

@section('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    /* ==========================================
       1. LAYOUT & RESPONSIVE FIXES
       ========================================== */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    
    .nav-tabs .nav-item {
        flex-shrink: 0;
    }
    
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 1rem;
    }
    
    .table th,
    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
    }
    
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    [data-bs-theme="dark"] .table thead th {
        background-color: #2d3748 !important;
        color: #e2e8f0 !important;
        border-color: #4a5568 !important;
    }
    
    [data-bs-theme="light"] .table thead th {
        background-color: #f8f9fa !important;
        color: #495057 !important;
        border-color: #dee2e6 !important;
    }
    
    [data-bs-theme="dark"] .table td .text-secondary small {
        color: #a0aec0 !important;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
    }
    
    .tab-content {
        margin-top: 0;
    }
    
    .btn-list .btn {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .btn-list .btn:last-child {
        margin-right: 0;
    }

    /* ==========================================
       2. FIX WARNA TEKS TAB & LABEL DI DARK MODE 
       ========================================== */
    [data-bs-theme="dark"] .nav-tabs .nav-link {
        color: #a0aec0 !important; 
    }
    
    [data-bs-theme="dark"] .nav-tabs .nav-link:hover {
        color: #f8f9fa !important; 
    }
    
    [data-bs-theme="dark"] .nav-tabs .nav-link.active {
        color: #ffffff !important; 
        background-color: #1e293b !important; 
        border-color: #334155 #334155 transparent !important;
    }

    [data-bs-theme="dark"] .dataTables_wrapper label {
        color: #e2e8f0 !important;
    }
    
    [data-bs-theme="dark"] .dataTables_info {
        color: #a0aec0 !important;
    }

    .nav-tabs .nav-link .badge {
        color: #ffffff !important;
    }

    /* ==========================================
       3. FIX WARNA TEKS TAB DI LIGHT MODE 
       ========================================== */
    [data-bs-theme="light"] .nav-tabs .nav-link.active,
    html:not([data-bs-theme="dark"]) .nav-tabs .nav-link.active {
        color: #1e293b !important; 
    }

    [data-bs-theme="light"] .nav-tabs .nav-link,
    html:not([data-bs-theme="dark"]) .nav-tabs .nav-link {
        color: #64748b !important; 
    }

    /* ==========================================
       4. FIX DROPDOWN EXPORT (HANYA UNTUK TOMBOL EXPORT)
       ========================================== */
    
    /* Batasi hanya untuk dropdown di dalam .card-actions agar tidak merusak sidebar */
    .card-actions .dropdown-menu {
        background-color: #ffffff !important; 
        border: 1px solid rgba(0,0,0,.15) !important;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        z-index: 9999 !important;
    }

    /* Pastikan warna teks gelap di Light Mode khusus area ini */
    [data-bs-theme="light"] .card-actions .dropdown-menu .dropdown-item,
    html:not([data-bs-theme="dark"]) .card-actions .dropdown-menu .dropdown-item {
        color: #1e293b !important; 
    }

    /* Hover effect untuk Light Mode */
    .card-actions .dropdown-menu .dropdown-item:hover,
    .card-actions .dropdown-menu .dropdown-item:focus {
        background-color: #f8f9fa !important;
        color: #1e293b !important;
    }

    /* Dark Mode Dropdown Fix khusus tombol export */
    [data-bs-theme="dark"] .card-actions .dropdown-menu {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    
    [data-bs-theme="dark"] .card-actions .dropdown-menu .dropdown-item {
        color: #e2e8f0 !important;
    }

    /* Hover effect untuk Dark Mode */
    [data-bs-theme="dark"] .card-actions .dropdown-menu .dropdown-item:hover,
    [data-bs-theme="dark"] .card-actions .dropdown-menu .dropdown-item:focus {
        background-color: #334155 !important;
        color: #ffffff !important;
    }
    
    /* Jika ada class text-white dari bawaan tema, netralkan */
    .card-actions .dropdown-menu .dropdown-item.text-white {
        color: inherit !important;
    }
</style>
@endsection

@section('content')
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $totalKrs }} Total KRS
                                </div>
                                <div class="text-secondary">
                                    Semua KRS mahasiswa
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 9v4" />
                                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                        <path d="M12 16h.01" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $pendingApproval }} Menunggu Persetujuan
                                </div>
                                <div class="text-secondary">
                                    KRS yang disubmit mahasiswa
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $approved }} Disetujui
                                </div>
                                <div class="text-secondary">
                                    KRS yang sudah disetujui
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-danger text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $rejected }} Ditolak
                                </div>
                                <div class="text-secondary">
                                    KRS yang ditolak
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-secondary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 8a3.5 3 0 0 1 3.5 -3h1a3.5 3 0 0 1 3.5 3a3 3 0 0 1 1 3v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a3 3 0 0 1 1 -3" />
                                        <path d="M12 17v1a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2v-1" />
                                        <path d="M12 12v5" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $draft }} Draft
                                </div>
                                <div class="text-secondary">
                                    KRS belum disubmit
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen KRS Mahasantri</h3>
                <div class="card-actions">
                    <div class="btn-list">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkApprovalModal" {{ $pendingApproval == 0 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Setujui Massal
                            <span class="badge bg-white text-primary ms-2" id="selectedCountBadge" style="display: none;">0</span>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 15l2 2l4 -4" />
                                </svg>
                                Export Excel
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="exportDropdown" style="z-index: 1060;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('akademik.krs-export', ['type' => 'all']) }}">
                                        <i class="fas fa-file-excel text-success me-2"></i> Semua Data (Gabung 1 Sheet)
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('akademik.krs-export', ['type' => 'kelas']) }}">
                                        <i class="fas fa-users text-primary me-2"></i> Ringkasan per Kelas (Multi-Sheet)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('akademik.krs-export', ['type' => 'semester']) }}">
                                        <i class="fas fa-calendar-alt text-info me-2"></i> Ringkasan per Semester (Multi-Sheet)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('akademik.krs-export', ['type' => 'matkul']) }}">
                                        <i class="fas fa-book-open text-warning me-2"></i> Detail per Mata Kuliah (Multi-Sheet)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column">
                    <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-all" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                <span class="d-none d-sm-inline">Semua</span>
                                <span class="d-sm-none">All</span>
                                <span class="badge bg-primary ms-1">{{ $totalKrs }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-pending" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                                <span class="d-none d-sm-inline">Menunggu Persetujuan</span>
                                <span class="d-sm-none">Pending</span>
                                <span class="badge bg-warning ms-1">{{ $pendingApproval }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-approved" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                                <span class="d-none d-sm-inline">Disetujui</span>
                                <span class="d-sm-none">Approved</span>
                                <span class="badge bg-success ms-1">{{ $approved }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-rejected" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                                <span class="d-none d-sm-inline">Ditolak</span>
                                <span class="d-sm-none">Rejected</span>
                                <span class="badge bg-danger ms-1">{{ $rejected }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-draft" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                                <span class="d-none d-sm-inline">Draft</span>
                                <span class="d-sm-none">Draft</span>
                                <span class="badge bg-secondary ms-1">{{ $draft }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tabs-all" role="tabpanel">
                            <div class="table-responsive">
                                @include('master.akademik.partials.krs-table', ['krsData' => $krsAll, 'showBulkActions' => true])
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-pending" role="tabpanel">
                            <div class="table-responsive">
                                @include('master.akademik.partials.krs-table', ['krsData' => $krsSubmitted, 'showBulkActions' => true])
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-approved" role="tabpanel">
                            <div class="table-responsive">
                                @include('master.akademik.partials.krs-table', ['krsData' => $krsApproved, 'showBulkActions' => false])
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-rejected" role="tabpanel">
                            <div class="table-responsive">
                                @include('master.akademik.partials.krs-table', ['krsData' => $krsRejected, 'showBulkActions' => false])
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-draft" role="tabpanel">
                            <div class="table-responsive">
                                @include('master.akademik.partials.krs-table', ['krsData' => $krsDraft, 'showBulkActions' => false])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="bulkApprovalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Setujui KRS Terpilih?</div>
                <div>Apakah Anda yakin ingin menyetujui semua KRS yang dipilih?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBulkApproval">Ya, Setujui</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="rejectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="rejectionForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak KRS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="4" placeholder="Masukkan alasan penolakan KRS..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak KRS</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Inisialisasi DataTables
    $('.table').each(function() {
        var currentTable = $(this);

        // Mencari posisi kolom yang punya class 'sort-tanggal'
        var dateColumnIndex = currentTable.find('thead th.sort-tanggal').index();

        // Jika ketemu, set agar urut terbaru (desc), jika tidak ada tetap kosong
        var initialOrder = (dateColumnIndex > -1) ? [[ dateColumnIndex, "desc" ]] : [];

        currentTable.DataTable({
            "pageLength": 10,
            "ordering": true,
            "order": initialOrder,
            "language": {
                "search": "Cari data:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari total _TOTAL_ data",
                "emptyTable": '<div class="empty"><div class="empty-img"><img src="{{ asset("dashboard/static/illustrations/undraw_quitting_time_dm8t.svg") }}" height="128" alt=""></div><p class="empty-title">Tidak ada data KRS</p><p class="empty-subtitle text-secondary">Belum ada KRS yang tersedia untuk ditampilkan.</p></div>',
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });

    // Paksa klik tab pertama dengan sedikit jeda (setTimeout) agar 
    // tidak bertabrakan dengan proses loading bawaan template (Bootstrap)
    setTimeout(function() {
        let firstTab = document.querySelector('.nav-tabs .nav-link');
        if (firstTab) {
            firstTab.click(); // Klik tabnya
        }
        // Pastikan juga tabel merespons ukuran layar dengan sempurna
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    }, 150);

    // ==========================================
    // FITUR DRAG TO SELECT CHECKBOX
    // ==========================================
    let isDragging = false;
    let dragCheckState = false;

    // 1. Saat klik mouse ditahan pada checkbox
    $(document).on('mousedown', '.bulk-approval-checkbox', function(e) {
        isDragging = true;
        dragCheckState = !$(this).prop('checked');
    });

    // 2. Saat mouse digeser melewati area kolom yang ada checkbox-nya
    $(document).on('mouseenter', 'td:has(.bulk-approval-checkbox)', function(e) {
        if (isDragging) {
            let checkbox = $(this).find('.bulk-approval-checkbox');
            checkbox.prop('checked', dragCheckState);
            checkbox.trigger('change');
        }
    });

    // 3. Saat klik mouse dilepas di mana saja di layar
    $(window).on('mouseup', function() {
        isDragging = false;
    });
    // ==========================================

    const bulkApprovalBtn = document.querySelector('[data-bs-target="#bulkApprovalModal"]');
    const confirmBulkApprovalBtn = document.getElementById('confirmBulkApproval');

    // 2. Fungsi sakti untuk mengecek checkbox di SELURUH HALAMAN DataTables
    function updateBulkApprovalButton() {
        let activeTable = $('.tab-pane.active .table').DataTable();
        let checkedBoxes = $('input[type="checkbox"].bulk-approval-checkbox:checked', activeTable.cells().nodes());
        let count = checkedBoxes.length;
        
        let badge = $('#selectedCountBadge'); 
        
        if (count > 0) {
            bulkApprovalBtn.disabled = false;
            badge.text(count).show();
        } else {
            bulkApprovalBtn.disabled = true;
            badge.hide();
        }
    }

    // 3. Event ketika "Select All" diklik
    $(document).on('change', '.select-all-krs', function() {
        let isChecked = $(this).prop('checked');
        let currentTable = $(this).closest('table').DataTable();
        
        $('input[type="checkbox"].bulk-approval-checkbox', currentTable.cells().nodes()).prop('checked', isChecked);
        updateBulkApprovalButton();
    });

    // 4. Event ketika checkbox individual diklik
    $(document).on('change', '.bulk-approval-checkbox', function() {
        updateBulkApprovalButton();
    });

    // 5. Reset tombol kalau user pindah Tab & Paksa DataTables Render Ulang
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        updateBulkApprovalButton();
    });

    // 6. Eksekusi pengiriman data (Bulk Approve)
    confirmBulkApprovalBtn.addEventListener('click', function() {
        let activeTable = $('.tab-pane.active .table').DataTable();
        let checkedBoxes = $('input[type="checkbox"].bulk-approval-checkbox:checked', activeTable.cells().nodes());
        
        let krsIds = [];
        checkedBoxes.each(function() {
            krsIds.push($(this).val());
        });
        
        if (krsIds.length > 0) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("akademik.krs-bulk-approve") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            krsIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'krs_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });

    // 7. Modal Penolakan (Reject)
    const rejectionForm = document.getElementById('rejectionForm');
    
    $(document).on('click', '.reject-krs-btn', function() {
        const krsId = $(this).data('krs-id');
        let urlTolak = "{{ route('akademik.krs-reject', ':id') }}";
        rejectionForm.action = urlTolak.replace(':id', krsId);
    });

}); 
</script>
@endsection