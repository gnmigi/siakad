@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Stats cards */
        .bg-light-primary {
            background-color: rgba(67, 94, 190, 0.1);
        }
        
        .bg-light-success {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .bg-light-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        /* Card styling */
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table th.text-center, .table td.text-center {
            text-align: center !important;
        }

        /* Button styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Form styling */
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }

        /* Badge styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            /* Keep table in card structure */
            .table-responsive {
                border: none;
                box-shadow: none;
            }
            
            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
                width: 100%;
            }
            .table-responsive thead {
                display: none;
            }
            .table-responsive tr {
                background: #f8f9fa;
                margin-bottom: 1rem;
                border-radius: 8px;
                padding: 1rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                border: 1px solid #e9ecef;
            }
            .table-responsive td {
                position: relative;
                padding: 0.5rem 0;
                text-align: left !important;
                border: none;
                border-bottom: 1px solid #e9ecef;
                padding-left: 40%;
                min-height: 2.5rem;
                display: flex;
                align-items: center;
            }
            .table-responsive td:last-child {
                border-bottom: none;
            }
            .table-responsive td:before {
                position: absolute;
                top: 0.5rem;
                left: 0;
                width: 38%;
                padding-right: 0.5rem;
                white-space: nowrap;
                font-weight: 600;
                color: #495057;
                content: attr(data-label);
                font-size: 0.875rem;
            }
            
            /* Special styling for checkbox and actions */
            .table-responsive td:first-child {
                padding-left: 0;
                justify-content: center;
            }
            .table-responsive td:first-child:before {
                display: none;
            }
            
            /* Action buttons in mobile */
            .table-responsive td:last-child {
                padding-left: 0;
                justify-content: center;
            }
            .table-responsive td:last-child:before {
                display: none;
            }
            
            .table-responsive .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
                justify-content: center;
            }
            
            .table-responsive .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            /* Status badges mobile styling */
            .table-responsive .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            /* Profile image mobile styling */
            .table-responsive img.rounded-circle {
                width: 32px;
                height: 32px;
            }
            
            /* Mobile Button Responsive Styles */
            .mobile-btn-container {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }
            
            .mobile-btn-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
                justify-content: flex-start;
            }
            
            .mobile-btn-row .btn {
                flex: 1;
                min-width: 0;
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .mobile-btn-row .btn i {
                margin-right: 0.25rem;
            }
            
            /* Hide text on very small screens, show only icons */
            @media (max-width: 480px) {
                .mobile-btn-row .btn .btn-text {
                    display: none;
                }
                .mobile-btn-row .btn {
                    min-width: 40px;
                    flex: 0 0 auto;
                }
            }
            
            /* Bulk actions mobile styling */
            .bulk-actions-mobile {
                background: rgba(255, 193, 7, 0.1);
                border: 1px solid rgba(255, 193, 7, 0.3);
                border-radius: 0.375rem;
                padding: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            .bulk-actions-mobile .btn {
                width: 100%;
                margin-bottom: 0.25rem;
            }
            
            .bulk-actions-mobile .btn:last-child {
                margin-bottom: 0;
            }
        }
        
        /* Desktop styles for button groups */
        @media (min-width: 769px) {
            .desktop-btn-container {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
                margin-top: 0.5rem;
            }
            
            /* Add proper spacing for desktop card header */
            .card-header .d-flex.flex-column.flex-md-row {
                gap: 1rem;
            }
            
            /* Ensure proper spacing between title and buttons */
            .card-header h5 {
                margin-right: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9 col-12 mb-2">
            <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <h5 class="mb-2 mb-md-0">{{ $pages }}</h5>
                            
                            <!-- Desktop Button Layout -->
                            <div class="d-none d-md-flex ms-auto">
                                <div class="desktop-btn-container">
                                    <!-- Bulk Actions Button Group (initially hidden) -->
                            <div class="btn-group me-2" id="bulkActionsGroup" style="display: none;">
                                <button type="button" class="btn btn-warning" id="bulkTerminalBtn" title="Terminal Terpilih">
                                    <i class="fas fa-ban me-1"></i>Terminal Terpilih (<span id="selectedCount">0</span>)
                                </button>
                                <button type="button" class="btn btn-success" id="bulkGraduateBtn" title="Luluskan Terpilih">
                                    <i class="fas fa-graduation-cap me-1"></i>Luluskan Terpilih (<span id="selectedGraduateCount">0</span>)
                                </button>
                            </div>
                            
                            <!-- Auto-Terminal Check Button -->
                            <div class="btn-group me-2" role="group">
                                <button type="button" class="btn btn-warning" id="autoTerminalCheckBtn" title="Cek Status Tagihan & Auto-Terminal">
                                    <i class="fas fa-robot me-1"></i>Auto-Terminal Check
                                </button>
                                <button type="button" class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="dryRunBtn"><i class="fas fa-eye me-2"></i>Preview (Dry Run)</a></li>
                                    <li><a class="dropdown-item" href="#" id="executeBtn"><i class="fas fa-play me-2"></i>Jalankan Sekarang</a></li>
                                </ul>
                            </div>
                            
                            <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-success me-2" title="Export Excel">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                            <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="btn btn-danger me-2" title="Export PDF">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal" title="Import Excel">
                                <i class="fas fa-file-import"></i> Import Excel
                            </button>
                                </div>
                            </div>
                            
                            <!-- Mobile Button Layout -->
                            <div class="d-block d-md-none w-100">
                                <div class="mobile-btn-container">
                                    <!-- Bulk Actions (Mobile) - initially hidden -->
                                    <div class="bulk-actions-mobile" id="bulkActionsMobile" style="display: none;">
                                        <div class="text-center mb-2">
                                            <small class="text-muted fw-bold">Aksi Massal</small>
                                        </div>
                                        <button type="button" class="btn btn-warning btn-sm" id="bulkTerminalBtnMobile" title="Terminal Terpilih">
                                            <i class="fas fa-ban me-1"></i><span class="btn-text">Terminal Terpilih</span> (<span id="selectedCountMobile">0</span>)
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" id="bulkGraduateBtnMobile" title="Luluskan Terpilih">
                                            <i class="fas fa-graduation-cap me-1"></i><span class="btn-text">Luluskan Terpilih</span> (<span id="selectedGraduateCountMobile">0</span>)
                                        </button>
                                    </div>
                                    
                                    <!-- Auto-Terminal Row -->
                                    <div class="mobile-btn-row">
                                        <button type="button" class="btn btn-warning btn-sm" id="autoTerminalCheckBtnMobile" title="Auto-Terminal Check">
                                            <i class="fas fa-robot"></i><span class="btn-text"> Auto-Terminal</span>
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Opsi Auto-Terminal">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" id="dryRunBtnMobile"><i class="fas fa-eye me-2"></i>Preview</a></li>
                                                <li><a class="dropdown-item" href="#" id="executeBtnMobile"><i class="fas fa-play me-2"></i>Jalankan</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <!-- Export/Import Row -->
                                    <div class="mobile-btn-row">
                                        <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-success btn-sm" title="Export Excel">
                                            <i class="fas fa-file-excel"></i><span class="btn-text"> Excel</span>
                                        </a>
                                        <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="btn btn-danger btn-sm" title="Export PDF">
                                            <i class="fas fa-file-pdf"></i><span class="btn-text"> PDF</span>
                                        </a>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal" title="Import Excel">
                                            <i class="fas fa-file-import"></i><span class="btn-text"> Import</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Mahasantri (Excel)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Pilih file Excel (.xlsx)</label>
                            <input class="form-control" type="file" id="import_file" name="import_file" accept=".xlsx" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="replace_existing" name="replace_existing">
                            <label class="form-check-label" for="replace_existing">
                                Ganti data yang sudah ada jika ditemukan duplikat (berdasarkan nama, email, atau NIM)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="importBtn">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Results Modal -->
    <div class="modal fade" id="importResultsModal" tabindex="-1" aria-labelledby="importResultsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importResultsModalLabel">
                        <i class="fas fa-file-import me-2"></i>Hasil Import Data Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Import Summary -->
                    <div class="row mb-4" id="importSummary">
                        <div class="col-md-3">
                            <div class="card bg-light-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="mb-1" id="summaryImported">0</h4>
                                    <small>Data Diimport</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-sync-alt fa-2x text-info mb-2"></i>
                                    <h4 class="mb-1" id="summaryUpdated">0</h4>
                                    <small>Data Diperbarui</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-skip-forward fa-2x text-warning mb-2"></i>
                                    <h4 class="mb-1" id="summarySkipped">0</h4>
                                    <small>Data Dilewati</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h4 class="mb-1" id="summaryFailed">0</h4>
                                    <small>Data Gagal</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Import Info -->
                    <div class="alert alert-light border mb-4" id="importInfo">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-file me-1"></i>File:</strong> <span id="importFilename">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock me-1"></i>Waktu:</strong> <span id="importTimestamp">-</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong><i class="fas fa-cog me-1"></i>Mode:</strong> <span id="importMode">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-list me-1"></i>Total Diproses:</strong> <span id="importTotalProcessed">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs for different result types -->
                    <ul class="nav nav-tabs" id="importResultsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="success-tab" data-bs-toggle="tab" data-bs-target="#success-pane" type="button" role="tab">
                                <i class="fas fa-check text-success me-1"></i>Berhasil (<span id="successTabCount">0</span>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="errors-tab" data-bs-toggle="tab" data-bs-target="#errors-pane" type="button" role="tab">
                                <i class="fas fa-times text-danger me-1"></i>Error (<span id="errorsTabCount">0</span>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="warnings-tab" data-bs-toggle="tab" data-bs-target="#warnings-pane" type="button" role="tab">
                                <i class="fas fa-exclamation-triangle text-warning me-1"></i>Peringatan (<span id="warningsTabCount">0</span>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="importResultsTabContent">
                        <!-- Success Tab -->
                        <div class="tab-pane fade show active" id="success-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data yang Berhasil Diproses</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="successSearch" placeholder="Cari..." style="width: 200px;">
                                    <select class="form-select form-select-sm" id="successFilter" style="width: 150px;">
                                        <option value="">Semua Status</option>
                                        <option value="success">Diimport</option>
                                        <option value="info">Diperbarui</option>
                                        <option value="warning">Dilewati</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Baris</th>
                                            <th width="200">Nama Mahasiswa</th>
                                            <th width="120">NIM</th>
                                            <th width="120">Status</th>
                                            <th>Aksi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="successTableBody">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">Menampilkan <span id="successShowingStart">0</span> - <span id="successShowingEnd">0</span> dari <span id="successTotal">0</span> data</small>
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="successPagination">
                                        <!-- Will be populated by JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Errors Tab -->
                        <div class="tab-pane fade" id="errors-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data yang Gagal Diproses</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="errorsSearch" placeholder="Cari..." style="width: 200px;">
                                    <select class="form-select form-select-sm" id="errorsFilter" style="width: 150px;">
                                        <option value="">Semua Jenis</option>
                                        <option value="validation">Validasi</option>
                                        <option value="duplicate_email">Duplikat Email</option>
                                        <option value="duplicate_phone">Duplikat Telepon</option>
                                        <option value="duplicate_nim">Duplikat NIM</option>
                                        <option value="system_error">Error Sistem</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Baris</th>
                                            <th width="150">Kolom</th>
                                            <th>Error</th>
                                            <th width="120">Jenis</th>
                                        </tr>
                                    </thead>
                                    <tbody id="errorsTableBody">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">Menampilkan <span id="errorsShowingStart">0</span> - <span id="errorsShowingEnd">0</span> dari <span id="errorsTotal">0</span> error</small>
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="errorsPagination">
                                        <!-- Will be populated by JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Warnings Tab -->
                        <div class="tab-pane fade" id="warnings-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Peringatan Import</h6>
                                <input type="text" class="form-control form-control-sm" id="warningsSearch" placeholder="Cari..." style="width: 200px;">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Baris</th>
                                            <th>Peringatan</th>
                                            <th width="120">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="warningsTableBody">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">Menampilkan <span id="warningsShowingStart">0</span> - <span id="warningsShowingEnd">0</span> dari <span id="warningsTotal">0</span> peringatan</small>
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="warningsPagination">
                                        <!-- Will be populated by JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="exportResultsBtn">
                        <i class="fas fa-download me-1"></i>Export Hasil
                    </button>
                    <button type="button" class="btn btn-success" id="refreshPageBtn">
                        <i class="fas fa-refresh me-1"></i>Refresh Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-filter me-2"></i>Filter & Pencarian Data
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route($spref . 'akademik.data-akademik-mahasiswa-render') }}" id="filterForm">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="search" class="form-label">Pencarian</label>
                                            <input type="text" class="form-control" id="search" name="search" 
                                                   value="{{ $current_filters['search'] ?? '' }}" 
                                                   placeholder="Nama, Email, atau NIM...">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="prodi_filter" class="form-label">Program Studi</label>
                                            <select class="form-select" id="prodi_filter" name="prodi_filter">
                                                <option value="">Semua Program Studi</option>
                                                @foreach ($program_studi as $prodi)
                                                    <option value="{{ $prodi->id }}" 
                                                        {{ ($current_filters['prodi_filter'] ?? '') == $prodi->id ? 'selected' : '' }}>
                                                        {{ $prodi->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="kelas_filter" class="form-label">Kelas</label>
                                            <select class="form-select" id="kelas_filter" name="kelas_filter">
                                                <option value="">Semua Kelas</option>
                                                @foreach ($kelas as $k)
                                                    <option value="{{ $k->id }}" 
                                                        {{ ($current_filters['kelas_filter'] ?? '') == $k->id ? 'selected' : '' }}>
                                                        {{ $k->name }} - {{ $k->jenisKelas->name ?? 'Reguler' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="semester_filter" class="form-label">Semester</label>
                                            <select class="form-select" id="semester_filter" name="semester_filter">
                                                <option value="">Semua Semester</option>
                                                @foreach ($semester_list as $semester)
                                                    <option value="{{ $semester }}" 
                                                        {{ ($current_filters['semester_filter'] ?? '') == $semester ? 'selected' : '' }}>
                                                        Semester {{ $semester }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="takhasus_filter" class="form-label">Takhasus</label>
                                            <select class="form-select" id="takhasus_filter" name="takhasus_filter">
                                                <option value="">Semua Takhasus</option>
                                                @foreach ($takhasus_list as $takhasus)
                                                    <option value="{{ $takhasus }}" 
                                                        {{ ($current_filters['takhasus_filter'] ?? '') == $takhasus ? 'selected' : '' }}>
                                                        {{ $takhasus }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="status_filter" class="form-label">Status Mahasiswa</label>
                                            <select class="form-select" id="status_filter" name="status_filter">
                                                <option value="">Semua Status</option>
                                                <option value="0" {{ ($current_filters['status_filter'] ?? '') == '0' ? 'selected' : '' }}>
                                                    Calon Mahasiswa
                                                </option>
                                                <option value="1" {{ ($current_filters['status_filter'] ?? '') == '1' ? 'selected' : '' }}>
                                                    Mahasiswa Aktif
                                                </option>
                                                <option value="2" {{ ($current_filters['status_filter'] ?? '') == '2' ? 'selected' : '' }}>
                                                    Mahasiswa Non-Aktif
                                                </option>
                                                <option value="3" {{ ($current_filters['status_filter'] ?? '') == '3' ? 'selected' : '' }}>
                                                    Alumni
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="terminal_filter" class="form-label">Status Terminal</label>
                                            <select class="form-select" id="terminal_filter" name="terminal_filter">
                                                <option value="">Semua Status</option>
                                                <option value="1" {{ ($current_filters['terminal_filter'] ?? '') == '1' ? 'selected' : '' }}>
                                                    Terminal
                                                </option>
                                                <option value="0" {{ ($current_filters['terminal_filter'] ?? '') == '0' ? 'selected' : '' }}>
                                                    Tidak Terminal
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="btn-group w-100" role="group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search me-1"></i>Filter
                                                </button>
                                                <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-render') }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-times me-1"></i>Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <!-- Auto-Terminal Schedule Info Card -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Jadwal Auto-Terminal Check
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-info mb-1">Jadwal Harian</h5>
                                        <p class="mb-0">Setiap hari jam <strong>02:00 WIB</strong></p>
                                        <small class="text-muted">Cek otomatis tunggakan SPP >60 hari</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-warning mb-1">Cek Berikutnya</h5>
                                        <p class="mb-0" id="nextCheckTime">-</p>
                                        <small class="text-muted">Waktu cek otomatis berikutnya</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-success mb-1">Countdown</h5>
                                        <p class="mb-0" id="countdownTimer">-</p>
                                        <small class="text-muted">Sisa waktu hingga cek otomatis</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-success me-2">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <div>
                                            <strong>Real-time Auto-Reactivation</strong>
                                            <br>
                                            <small class="text-muted">Mahasiswa otomatis direaktivasi saat pembayaran berhasil</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-info me-2">
                                            <i class="fas fa-sync"></i>
                                        </div>
                                        <div>
                                            <strong>Manual Check Available</strong>
                                            <br>
                                            <small class="text-muted">Gunakan button "Auto-Terminal Check" untuk cek manual</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Mahasantri</h6>
                                <h3 class="mb-0">{{ count($mahasiswa) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Sudah Ada NIM</h6>
                                <h3 class="mb-0">{{ $mahasiswa->whereNotNull('numb_nim')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Belum Ada NIM</h6>
                                <h3 class="mb-0">{{ $mahasiswa->whereNull('numb_nim')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-info rounded">
                                <h6 class="mb-2">Sudah Ada Kelas</h6>
                                <h3 class="mb-0">{{ $mahasiswa->whereNotNull('kelas_id')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" title="Pilih Semua">
                                        </div>
                                    </th>
                                    <th class="text-center">No</th>
                                    <th>Nama Mahasantri</th>
                                    <th>NIM</th>
                                    <th>Program Studi</th>
                                    <th>Kelas</th>
                                    <th>Semester</th>
                                    <th>Takhasus</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mahasiswa as $key => $item)
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input student-checkbox" type="checkbox" 
                                                       value="{{ $item->code }}" 
                                                       data-name="{{ $item->name }}"
                                                       data-terminal="{{ $item->isTerminal() ? '1' : '0' }}"
                                                       data-semester="{{ $item->semester ?? 0 }}"
                                                       data-status="{{ $item->getRawTypeAttribute() }}">
                                            </div>
                                        </td>
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Nama Mahasantri">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->photo }}" alt="{{ $item->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e9ecef;">
                                                <div>
                                                    <span class="fw-bold">{{ $item->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $item->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="NIM">
                                            @if($item->numb_nim)
                                                <span class="badge bg-light-success text-success">{{ $item->numb_nim }}</span>
                                            @else
                                                <span class="badge bg-light-warning text-warning">Belum Ada</span>
                                            @endif
                                        </td>
                                        <td data-label="Program Studi">{{ $item->prodi->name ?? '-' }}</td>
                                        <td data-label="Kelas">
                                            @if($item->kelas)
                                                {{ $item->kelas->name }} - {{ $item->kelas->jenisKelas->name ?? 'Reguler' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td data-label="Semester">{{ $item->semester ?? '-' }}</td>
                                        <td data-label="Takhasus">{{ $item->takhasusOption->name ?? $item->takhasus ?? '-' }}</td>
                                        <td data-label="Status" class="text-center">
                                            @php
                                                $statusClass = '';
                                                $statusIcon = '';
                                                $statusText = '';
                                                
                                                switch($item->getRawTypeAttribute()) {
                                                    case 0:
                                                        $statusClass = 'bg-info text-white';
                                                        $statusIcon = 'fas fa-user-plus';
                                                        $statusText = 'Calon Mahasiswa';
                                                        break;
                                                    case 1:
                                                        $statusClass = 'bg-success text-white';
                                                        $statusIcon = 'fas fa-check';
                                                        $statusText = 'Aktif';
                                                        break;
                                                    case 2:
                                                        $statusClass = 'bg-warning text-dark';
                                                        $statusIcon = 'fas fa-pause';
                                                        $statusText = 'Non-Aktif';
                                                        break;
                                                    case 3:
                                                        $statusClass = 'bg-primary text-white';
                                                        $statusIcon = 'fas fa-graduation-cap';
                                                        $statusText = 'Alumni';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-secondary text-white';
                                                        $statusIcon = 'fas fa-question';
                                                        $statusText = 'Unknown';
                                                }
                                            @endphp
                                            
                                            <!-- Status Mahasiswa -->
                                            <div class="mb-1">
                                                <span class="badge {{ $statusClass }}">
                                                    <i class="{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                </span>
                                            </div>
                                            
                                            <!-- Status Terminal -->
                                            @if($item->isTerminal())
                                                <div class="mb-1">
                                                    <span class="badge bg-danger text-white">
                                                        <i class="fas fa-ban me-1"></i>Terminal
                                                    </span>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted" title="Terminal sejak: {{ $item->terminated_at ? $item->terminated_at->format('d/m/Y H:i') : '-' }}">
                                                        <i class="fas fa-clock me-1"></i>{{ $item->terminated_at ? $item->terminated_at->format('d/m/Y') : '-' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" title="Edit Data Akademik">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editProfileData{{ $item->code }}" class="btn btn-sm btn-success" title="Edit Profile Lengkap">
                                                    <i class="fas fa-user-edit"></i>
                                                </a>
                                                @if($item->getRawTypeAttribute() == 3)
                                                    <!-- Already Alumni -->
                                                    <button type="button" class="btn btn-sm btn-info" disabled title="Sudah Alumni">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </button>
                                                @elseif($item->getRawTypeAttribute() == 1 && $item->semester >= 7)
                                                    <!-- Can Graduate -->
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#graduateModal{{ $item->code }}" title="Luluskan Mahasiswa">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </button>
                                                @endif
                                                @if($item->isTerminal())
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#terminalModal{{ $item->code }}" title="Nonaktifkan Terminal">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#terminalModal{{ $item->code }}" title="Aktifkan Terminal">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="col-lg-3 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini untuk mengelola data akademik mahasiswa seperti NIM, Program Studi, Kelas, dan Semester.</p>
                    
                    <div class="alert alert-light-info">
                        <h6 class="">Petunjuk:</h6>
                        <ul class="mb-0">
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data akademik mahasiswa</li>
                            <li>Data login mahasiswa dikelola di Master Pengguna</li>
                            <li>Data akademik hanya bisa diubah oleh admin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modals -->
    @foreach ($mahasiswa as $item)
        <div class="modal fade" id="editProfileData{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-profile-update', $item->code) }}" method="POST" enctype="multipart/form-data">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProfileModalLabel{{ $item->code }}">Edit Profile Lengkap - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div class="row">
                                <!-- Data Pribadi -->
                                <div class="col-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user me-2"></i>Data Pribadi</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_name{{ $item->code }}" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="profile_name{{ $item->code }}" value="{{ $item->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_email{{ $item->code }}" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="profile_email{{ $item->code }}" value="{{ $item->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_phone{{ $item->code }}" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="profile_phone{{ $item->code }}" value="{{ $item->phone }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_bio_gender{{ $item->code }}" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="bio_gender" id="profile_bio_gender{{ $item->code }}">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ $item->bio_gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ $item->bio_gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_bio_placebirth{{ $item->code }}" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" name="bio_placebirth" id="profile_bio_placebirth{{ $item->code }}" value="{{ $item->bio_placebirth }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_bio_datebirth{{ $item->code }}" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="bio_datebirth" id="profile_bio_datebirth{{ $item->code }}" value="{{ $item->bio_datebirth }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_bio_religion{{ $item->code }}" class="form-label">Agama</label>
                                    <select class="form-select" name="bio_religion" id="profile_bio_religion{{ $item->code }}">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ $item->bio_religion == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ $item->bio_religion == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ $item->bio_religion == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ $item->bio_religion == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ $item->bio_religion == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ $item->bio_religion == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_bio_nationality{{ $item->code }}" class="form-label">Kewarganegaraan</label>
                                    <input type="text" class="form-control" name="bio_nationality" id="profile_bio_nationality{{ $item->code }}" value="{{ $item->bio_nationality ?? 'Indonesia' }}">
                                </div>

                                <!-- Nomor Identitas -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-id-card me-2"></i>Nomor Identitas</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_numb_ktp{{ $item->code }}" class="form-label">No. KTP/NIK</label>
                                    <input type="text" class="form-control" name="numb_ktp" id="profile_numb_ktp{{ $item->code }}" value="{{ $item->numb_ktp }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_numb_kk{{ $item->code }}" class="form-label">No. Kartu Keluarga</label>
                                    <input type="text" class="form-control" name="numb_kk" id="profile_numb_kk{{ $item->code }}" value="{{ $item->numb_kk }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_numb_nisn{{ $item->code }}" class="form-label">NISN</label>
                                    <input type="text" class="form-control" name="numb_nisn" id="profile_numb_nisn{{ $item->code }}" value="{{ $item->numb_nisn }}">
                                </div>

                                <!-- Alamat KTP -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-home me-2"></i>Alamat Sesuai KTP</h6>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="profile_ktp_addres{{ $item->code }}" class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" name="ktp_addres" id="profile_ktp_addres{{ $item->code }}" rows="3">{{ $item->ktp_addres }}</textarea>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="profile_ktp_rt{{ $item->code }}" class="form-label">RT</label>
                                    <input type="text" class="form-control" name="ktp_rt" id="profile_ktp_rt{{ $item->code }}" value="{{ $item->ktp_rt }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="profile_ktp_rw{{ $item->code }}" class="form-label">RW</label>
                                    <input type="text" class="form-control" name="ktp_rw" id="profile_ktp_rw{{ $item->code }}" value="{{ $item->ktp_rw }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_ktp_village{{ $item->code }}" class="form-label">Kelurahan/Desa</label>
                                    <input type="text" class="form-control" name="ktp_village" id="profile_ktp_village{{ $item->code }}" value="{{ $item->ktp_village }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_ktp_subdistrict{{ $item->code }}" class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" name="ktp_subdistrict" id="profile_ktp_subdistrict{{ $item->code }}" value="{{ $item->ktp_subdistrict }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_ktp_city{{ $item->code }}" class="form-label">Kota/Kabupaten</label>
                                    <input type="text" class="form-control" name="ktp_city" id="profile_ktp_city{{ $item->code }}" value="{{ $item->ktp_city }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_ktp_province{{ $item->code }}" class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" name="ktp_province" id="profile_ktp_province{{ $item->code }}" value="{{ $item->ktp_province }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_ktp_poscode{{ $item->code }}" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" name="ktp_poscode" id="profile_ktp_poscode{{ $item->code }}" value="{{ $item->ktp_poscode }}">
                                </div>

                                <!-- Data Orang Tua -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-users me-2"></i>Data Orang Tua</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_father_name{{ $item->code }}" class="form-label">Nama Ayah</label>
                                    <input type="text" class="form-control" name="father_name" id="profile_father_name{{ $item->code }}" value="{{ $item->father_name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_mother_name{{ $item->code }}" class="form-label">Nama Ibu</label>
                                    <input type="text" class="form-control" name="mother_name" id="profile_mother_name{{ $item->code }}" value="{{ $item->mother_name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_father_phone{{ $item->code }}" class="form-label">No. Telepon Ayah</label>
                                    <input type="text" class="form-control" name="father_phone" id="profile_father_phone{{ $item->code }}" value="{{ $item->father_phone }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_mother_phone{{ $item->code }}" class="form-label">No. Telepon Ibu</label>
                                    <input type="text" class="form-control" name="mother_phone" id="profile_mother_phone{{ $item->code }}" value="{{ $item->mother_phone }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_father_occupation{{ $item->code }}" class="form-label">Pekerjaan Ayah</label>
                                    <input type="text" class="form-control" name="father_occupation" id="profile_father_occupation{{ $item->code }}" value="{{ $item->father_occupation }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_mother_occupation{{ $item->code }}" class="form-label">Pekerjaan Ibu</label>
                                    <input type="text" class="form-control" name="mother_occupation" id="profile_mother_occupation{{ $item->code }}" value="{{ $item->mother_occupation }}">
                                </div>

                                <!-- Pendidikan Terakhir -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-graduation-cap me-2"></i>Pendidikan Terakhir</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_edu1_type{{ $item->code }}" class="form-label">Jenjang Pendidikan</label>
                                    <select class="form-select" name="edu1_type" id="profile_edu1_type{{ $item->code }}">
                                        <option value="SMA/SMK" {{ $item->edu1_type == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                        <option value="Pesantren" {{ $item->edu1_type == 'Pesantren' ? 'selected' : '' }}>Pesantren</option>
                                        <option value="Diploma" {{ $item->edu1_type == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                        <option value="Sarjana" {{ $item->edu1_type == 'Sarjana' ? 'selected' : '' }}>Sarjana</option>
                                        <option value="Magister" {{ $item->edu1_type == 'Magister' ? 'selected' : '' }}>Magister</option>
                                        <option value="Doktor" {{ $item->edu1_type == 'Doktor' ? 'selected' : '' }}>Doktor</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_edu1_place{{ $item->code }}" class="form-label">Nama Sekolah/Universitas</label>
                                    <input type="text" class="form-control" name="edu1_place" id="profile_edu1_place{{ $item->code }}" value="{{ $item->edu1_place }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_edu1_major{{ $item->code }}" class="form-label">Jurusan</label>
                                    <input type="text" class="form-control" name="edu1_major" id="profile_edu1_major{{ $item->code }}" value="{{ $item->edu1_major }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile_edu1_graduate_year{{ $item->code }}" class="form-label">Tahun Lulus</label>
                                    <input type="text" class="form-control" name="edu1_graduate_year" id="profile_edu1_graduate_year{{ $item->code }}" value="{{ $item->edu1_graduate_year }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Simpan Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Edit Academic Modals -->
    @foreach ($mahasiswa as $item)
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-update', $item->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Data Akademik - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_numb_nim{{ $item->code }}" class="form-label">NIM <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="numb_nim" id="edit_numb_nim{{ $item->code }}" value="{{ $item->numb_nim }}" placeholder="Contoh: 2024001001">
                                    @error('numb_nim')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_prodi_id{{ $item->code }}" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                    <select class="form-select" name="prodi_id" id="edit_prodi_id{{ $item->code }}">
                                        <option value="">Pilih Program Studi</option>
                                        @foreach ($program_studi as $p)
                                            <option value="{{ $p->id }}" {{ $item->prodi_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_kelas_id{{ $item->code }}" class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select" name="kelas_id" id="edit_kelas_id{{ $item->code }}">
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" {{ $item->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->name }} - {{ $k->jenisKelas->name ?? 'Reguler' }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_semester{{ $item->code }}" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="semester" id="edit_semester{{ $item->code }}" min="1" max="14" value="{{ $item->semester }}" placeholder="Contoh: 1">
                                    @error('semester')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_takhasus_id{{ $item->code }}" class="form-label">Takhasus (Opsional)</label>
                                    <select class="form-select" name="takhasus_id" id="edit_takhasus_id{{ $item->code }}">
                                        <option value="">Pilih Takhasus</option>
                                        @if($item->prodi_id)
                                            @foreach($item->prodi->takhasusOptions ?? [] as $takhasus)
                                                <option value="{{ $takhasus->id }}" {{ $item->takhasus_id == $takhasus->id ? 'selected' : '' }}>
                                                    {{ $takhasus->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('takhasus_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" onclick="return validateForm{{ $item->code }}()">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Terminal Modals -->
    @foreach ($mahasiswa as $item)
        <div class="modal fade" id="terminalModal{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="terminalModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-terminal-toggle', $item->code) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="terminalModalLabel{{ $item->code }}">
                                @if($item->isTerminal())
                                    <i class="fas fa-unlock text-warning me-2"></i>Nonaktifkan Terminal - {{ $item->name }}
                                @else
                                    <i class="fas fa-ban text-danger me-2"></i>Aktifkan Terminal - {{ $item->name }}
                                @endif
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($item->isTerminal())
                                <!-- Deactivate Terminal -->
                                <input type="hidden" name="action" value="deactivate">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Nonaktifkan Terminal</h6>
                                    <p class="mb-2">Anda akan menonaktifkan status terminal untuk mahasiswa <strong>{{ $item->name }}</strong>.</p>
                                    
                                    @php $terminalInfo = $item->getTerminalInfo(); @endphp
                                    @if($terminalInfo)
                                        <div class="mt-3">
                                            <h6>Informasi Terminal Saat Ini:</h6>
                                            <ul class="mb-0">
                                                <li><strong>Alasan:</strong> {{ $terminalInfo['reason'] ?? '-' }}</li>
                                                <li><strong>Tanggal:</strong> {{ $terminalInfo['date'] ?? '-' }}</li>
                                                <li><strong>Oleh:</strong> {{ $terminalInfo['by'] ?? '-' }}</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <p>Setelah terminal dinonaktifkan, mahasiswa akan dapat:</p>
                                <ul>
                                    <li><i class="fas fa-check text-success me-1"></i> Mengisi KRS (Kartu Rencana Studi)</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Mengakses KHS (Kartu Hasil Studi)</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Mengakses semua fitur akademik</li>
                                </ul>
                            @else
                                <!-- Activate Terminal -->
                                <input type="hidden" name="action" value="activate">
                                <div class="alert alert-danger">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Peringatan: Aktifkan Terminal</h6>
                                    <p class="mb-0">Anda akan mengaktifkan status terminal untuk mahasiswa <strong>{{ $item->name }}</strong>.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="terminal_reason{{ $item->code }}" class="form-label">Alasan Terminal <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="terminal_reason" id="terminal_reason{{ $item->code }}" rows="3" placeholder="Masukkan alasan mengapa mahasiswa ini di-terminal..." required></textarea>
                                    <small class="text-muted">Alasan ini akan dicatat dalam sistem untuk keperluan audit.</small>
                                </div>
                                
                                <p>Setelah terminal diaktifkan, mahasiswa <strong>TIDAK AKAN DAPAT</strong>:</p>
                                <ul>
                                    <li><i class="fas fa-times text-danger me-1"></i> Mengisi KRS (Kartu Rencana Studi)</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Mengakses KHS (Kartu Hasil Studi)</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Mengakses fitur akademik lainnya</li>
                                </ul>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            @if($item->isTerminal())
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-unlock me-1"></i> Nonaktifkan Terminal
                                </button>
                            @else
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban me-1"></i> Aktifkan Terminal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Auto-Terminal Check Modal -->
    <div class="modal fade" id="autoTerminalModal" tabindex="-1" role="dialog" aria-labelledby="autoTerminalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="autoTerminalModalLabel">
                        <i class="fas fa-robot text-warning me-2"></i>Auto-Terminal Check - Cek Status Tagihan SPP
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="autoTerminalLoading" class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-3">
                            <h6>Sedang menganalisis status tagihan mahasiswa...</h6>
                            <p class="text-muted">Mohon tunggu, proses ini mungkin memakan waktu beberapa detik.</p>
                        </div>
                    </div>

                    <!-- Results -->
                    <div id="autoTerminalResults" style="display: none;">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light-danger">
                                    <div class="card-body text-center">
                                        <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                                        <h4 class="mb-1" id="willTerminateCount">0</h4>
                                        <small>Akan Di-Terminal</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-unlock fa-2x text-success mb-2"></i>
                                        <h4 class="mb-1" id="willReactivateCount">0</h4>
                                        <small>Akan Direaktivasi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check fa-2x text-info mb-2"></i>
                                        <h4 class="mb-1" id="alreadyProcessedCount">0</h4>
                                        <small>Sudah Diproses</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                        <h4 class="mb-1" id="totalProcessedCount">0</h4>
                                        <small>Total Diproses</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for different results -->
                        <ul class="nav nav-tabs" id="autoTerminalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="terminate-tab" data-bs-toggle="tab" data-bs-target="#terminate-pane" type="button" role="tab">
                                    <i class="fas fa-ban text-danger me-1"></i>Akan Di-Terminal (<span id="terminateTabCount">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reactivate-tab" data-bs-toggle="tab" data-bs-target="#reactivate-pane" type="button" role="tab">
                                    <i class="fas fa-unlock text-success me-1"></i>Akan Direaktivasi (<span id="reactivateTabCount">0</span>)
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="autoTerminalTabContent">
                            <!-- Students to be terminated -->
                            <div class="tab-pane fade show active" id="terminate-pane" role="tabpanel">
                                <div class="alert alert-danger">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Mahasiswa Berikut Akan Di-Terminal</h6>
                                    <p class="mb-0">Mahasiswa dengan tunggakan SPP lebih dari 60 hari akan di-terminal otomatis.</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIM</th>
                                                <th>Program Studi</th>
                                                <th>Semester</th>
                                                <th>Tunggakan</th>
                                                <th>Total Tunggakan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="terminateList">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Students to be reactivated -->
                            <div class="tab-pane fade" id="reactivate-pane" role="tabpanel">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-check me-2"></i>Mahasiswa Berikut Akan Direaktivasi</h6>
                                    <p class="mb-0">Mahasiswa yang sudah melunasi tunggakan SPP akan direaktivasi otomatis.</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIM</th>
                                                <th>Program Studi</th>
                                                <th>Semester</th>
                                                <th>Alasan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reactivateList">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Execution Results -->
                    <div id="executionResults" style="display: none;">
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>Auto-Terminal Check Selesai!</h6>
                            <p class="mb-0">Proses auto-terminal telah selesai dijalankan.</p>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light-danger">
                                    <div class="card-body text-center">
                                        <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                                        <h4 class="mb-1" id="actualTerminatedCount">0</h4>
                                        <small>Mahasiswa Di-Terminal</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-unlock fa-2x text-success mb-2"></i>
                                        <h4 class="mb-1" id="actualReactivatedCount">0</h4>
                                        <small>Mahasiswa Direaktivasi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p id="executionMessage" class="text-center"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning" id="executeAutoTerminalBtn" style="display: none;">
                        <i class="fas fa-play me-1"></i>Jalankan Auto-Terminal
                    </button>
                    <button type="button" class="btn btn-primary" id="refreshPageBtn" style="display: none;">
                        <i class="fas fa-refresh me-1"></i>Refresh Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Terminal Modal -->
    <div class="modal fade" id="bulkTerminalModal" tabindex="-1" role="dialog" aria-labelledby="bulkTerminalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form id="bulkTerminalForm" method="POST" action="{{ route($spref . 'akademik.data-akademik-mahasiswa-bulk-terminal') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkTerminalModalLabel">
                            <i class="fas fa-ban text-danger me-2"></i>Terminal Massal Mahasiswa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Terminal Massal</h6>
                            <p class="mb-2">Anda akan melakukan operasi terminal pada <strong><span id="bulkSelectedCount">0</span> mahasiswa</strong> yang dipilih.</p>
                        </div>

                        <!-- Action Selection -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Aksi Terminal:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bulk_action" id="bulkActivate" value="activate" checked>
                                <label class="form-check-label" for="bulkActivate">
                                    <i class="fas fa-ban text-danger me-1"></i> Aktifkan Terminal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bulk_action" id="bulkDeactivate" value="deactivate">
                                <label class="form-check-label" for="bulkDeactivate">
                                    <i class="fas fa-unlock text-warning me-1"></i> Nonaktifkan Terminal
                                </label>
                            </div>
                        </div>

                        <!-- Reason (for activation) -->
                        <div class="mb-3" id="bulkReasonGroup">
                            <label for="bulk_terminal_reason" class="form-label">Alasan Terminal <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="bulk_terminal_reason" id="bulk_terminal_reason" rows="3" 
                                      placeholder="Masukkan alasan mengapa mahasiswa ini di-terminal..." required></textarea>
                            <small class="text-muted">Alasan ini akan dicatat dalam sistem untuk keperluan audit.</small>
                        </div>

                        <!-- Selected Students List -->
                        <div class="mb-3">
                            <label class="form-label">Mahasiswa yang Dipilih:</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <div id="selectedStudentsList">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Warning Messages -->
                        <div id="bulkActivateWarning" class="alert alert-danger">
                            <p class="mb-2">Setelah terminal diaktifkan, mahasiswa yang dipilih <strong>TIDAK AKAN DAPAT</strong>:</p>
                            <ul class="mb-0">
                                <li><i class="fas fa-times text-danger me-1"></i> Mengisi KRS (Kartu Rencana Studi)</li>
                                <li><i class="fas fa-times text-danger me-1"></i> Mengakses KHS (Kartu Hasil Studi)</li>
                                <li><i class="fas fa-times text-danger me-1"></i> Mengakses fitur akademik lainnya</li>
                            </ul>
                        </div>

                        <div id="bulkDeactivateWarning" class="alert alert-success" style="display: none;">
                            <p class="mb-2">Setelah terminal dinonaktifkan, mahasiswa yang dipilih akan dapat:</p>
                            <ul class="mb-0">
                                <li><i class="fas fa-check text-success me-1"></i> Mengisi KRS (Kartu Rencana Studi)</li>
                                <li><i class="fas fa-check text-success me-1"></i> Mengakses KHS (Kartu Hasil Studi)</li>
                                <li><i class="fas fa-check text-success me-1"></i> Mengakses semua fitur akademik</li>
                            </ul>
                        </div>

                        <!-- Hidden input for selected students -->
                        <input type="hidden" name="selected_students" id="selectedStudentsInput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="bulkTerminalSubmitBtn">
                            <i class="fas fa-ban me-1"></i> Aktifkan Terminal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Individual Graduation Modals -->
    @foreach ($mahasiswa as $item)
        @if($item->getRawTypeAttribute() == 1 && $item->semester >= 7)
        <div class="modal fade" id="graduateModal{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="graduateModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-graduate', $item->code) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="graduateModalLabel{{ $item->code }}">
                                <i class="fas fa-graduation-cap text-info me-2"></i>Luluskan Mahasiswa - {{ $item->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Konfirmasi Kelulusan</h6>
                                <p class="mb-2">Anda akan meluluskan mahasiswa <strong>{{ $item->name }}</strong> ({{ $item->numb_nim }}).</p>
                                <ul class="mb-0">
                                    <li><strong>Program Studi:</strong> {{ $item->prodi->name ?? 'N/A' }}</li>
                                    <li><strong>Semester:</strong> {{ $item->semester }}</li>
                                    <li><strong>Status:</strong> {{ $item->type }}</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <label for="graduation_date{{ $item->code }}" class="form-label">Tanggal Kelulusan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="graduation_date" id="graduation_date{{ $item->code }}" value="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="graduation_note{{ $item->code }}" class="form-label">Catatan Kelulusan</label>
                                <textarea class="form-control" name="graduation_note" id="graduation_note{{ $item->code }}" rows="3" 
                                          placeholder="Catatan tambahan untuk kelulusan (opsional)..."></textarea>
                                <small class="text-muted">Catatan ini akan disimpan dalam sistem untuk keperluan dokumentasi.</small>
                            </div>
                            
                            <div class="alert alert-success">
                                <p class="mb-2">Setelah diluluskan, mahasiswa akan:</p>
                                <ul class="mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Status berubah menjadi <strong>Alumni</strong></li>
                                    <li><i class="fas fa-check text-success me-1"></i> Mendapat akses ke portal alumni</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Dapat mengunduh transkrip nilai</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-graduation-cap me-1"></i> Luluskan Mahasiswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <!-- Bulk Graduation Modal -->
    <div class="modal fade" id="bulkGraduationModal" tabindex="-1" role="dialog" aria-labelledby="bulkGraduationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form id="bulkGraduationForm" method="POST" action="{{ route($spref . 'akademik.data-akademik-mahasiswa-bulk-graduate') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkGraduationModalLabel">
                            <i class="fas fa-graduation-cap text-info me-2"></i>Kelulusan Massal Mahasiswa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Konfirmasi Kelulusan Massal</h6>
                            <p class="mb-2">Anda akan meluluskan <strong><span id="bulkGraduateSelectedCount">0</span> mahasiswa</strong> yang dipilih.</p>
                        </div>

                        <div class="mb-3">
                            <label for="bulk_graduation_date" class="form-label">Tanggal Kelulusan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="bulk_graduation_date" id="bulk_graduation_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bulk_graduation_note" class="form-label">Catatan Kelulusan</label>
                            <textarea class="form-control" name="bulk_graduation_note" id="bulk_graduation_note" rows="3" 
                                      placeholder="Catatan tambahan untuk kelulusan massal (opsional)..."></textarea>
                            <small class="text-muted">Catatan ini akan disimpan untuk semua mahasiswa yang diluluskan.</small>
                        </div>

                        <!-- Selected Students List -->
                        <div class="mb-3">
                            <label class="form-label">Mahasiswa yang Akan Diluluskan:</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <div id="selectedGraduateStudentsList">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <p class="mb-2">Setelah diluluskan, mahasiswa yang dipilih akan:</p>
                            <ul class="mb-0">
                                <li><i class="fas fa-check text-success me-1"></i> Status berubah menjadi <strong>Alumni</strong></li>
                                <li><i class="fas fa-check text-success me-1"></i> Mendapat akses ke portal alumni</li>
                                <li><i class="fas fa-check text-success me-1"></i> Dapat mengunduh transkrip nilai</li>
                            </ul>
                        </div>

                        <!-- Hidden input for selected students -->
                        <input type="hidden" name="selected_students" id="selectedGraduateStudentsInput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-graduation-cap me-1"></i> Luluskan Mahasiswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/graduation-management.js') }}"></script>
    
    <!-- Import Results JavaScript -->
    <script>
        // Configuration for import results
        window.importResultsRoute = '{{ route($spref . "akademik.data-akademik-mahasiswa-import-results") }}';
        window.checkImportResults = @json(session('import_success') || session('import_error') ? true : false);
    </script>
    <script src="{{ asset('js/import-results.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Auto-submit form when filter changes
            $('#prodi_filter, #kelas_filter, #semester_filter, #takhasus_filter, #status_filter, #terminal_filter').on('change', function() {
                $('#filterForm').submit();
            });

            // Real-time search - simple approach
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(function() {
                    // Submit form automatically when user types
                    $('#filterForm').submit();
                }, 500); // 500ms delay
            });

            // Checkbox functionality
            let selectedStudents = [];

            // Select All checkbox
            $('#selectAll').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.student-checkbox').prop('checked', isChecked);
                updateSelectedStudents();
            });

            // Individual checkbox change
            $(document).on('change', '.student-checkbox', function() {
                updateSelectedStudents();
                
                // Update select all checkbox state
                const totalCheckboxes = $('.student-checkbox').length;
                const checkedCheckboxes = $('.student-checkbox:checked').length;
                
                if (checkedCheckboxes === 0) {
                    $('#selectAll').prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    $('#selectAll').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#selectAll').prop('indeterminate', true);
                }
            });

            // Update selected students array and UI
            function updateSelectedStudents() {
                selectedStudents = [];
                let graduationEligibleCount = 0;
                
                $('.student-checkbox:checked').each(function() {
                    const semester = parseInt($(this).data('semester')) || 0;
                    const status = parseInt($(this).data('status'));
                    
                    selectedStudents.push({
                        code: $(this).val(),
                        name: $(this).data('name'),
                        terminal: $(this).data('terminal'),
                        semester: semester,
                        status: status
                    });
                    
                    // Check if eligible for graduation (status = 1 (active) and semester >= 7)
                    if (status === 1 && semester >= 7) {
                        graduationEligibleCount++;
                    }
                });

                // Update UI
                const count = selectedStudents.length;
                $('#selectedCount').text(count);
                $('#selectedCountMobile').text(count);
                $('#selectedGraduateCount').text(graduationEligibleCount);
                $('#selectedGraduateCountMobile').text(graduationEligibleCount);
                
                if (count > 0) {
                    $('#bulkActionsGroup').show();
                    $('#bulkActionsMobile').show();
                } else {
                    $('#bulkActionsGroup').hide();
                    $('#bulkActionsMobile').hide();
                }
            }

            // Bulk Terminal Button Click (Desktop and Mobile)
            $('#bulkTerminalBtn, #bulkTerminalBtnMobile').on('click', function() {
                if (selectedStudents.length === 0) {
                    alert('Pilih minimal satu mahasiswa untuk melakukan terminal massal.');
                    return;
                }

                // Update modal content
                $('#bulkSelectedCount').text(selectedStudents.length);
                
                // Populate selected students list
                let studentsList = '';
                selectedStudents.forEach(function(student, index) {
                    const terminalStatus = student.terminal == '1' ? 
                        '<span class="badge bg-danger text-white ms-2">Terminal</span>' : 
                        '<span class="badge bg-success text-white ms-2">Aktif</span>';
                    
                    studentsList += `
                        <div class="d-flex justify-content-between align-items-center py-1 ${index > 0 ? 'border-top' : ''}">
                            <span>${student.name}</span>
                            ${terminalStatus}
                        </div>
                    `;
                });
                $('#selectedStudentsList').html(studentsList);

                // Set selected students in hidden input
                $('#selectedStudentsInput').val(JSON.stringify(selectedStudents.map(s => s.code)));

                // Show modal
                $('#bulkTerminalModal').modal('show');
            });

            // Bulk Graduate Button Click (Desktop and Mobile)
            $('#bulkGraduateBtn, #bulkGraduateBtnMobile').on('click', function() {
                // Filter only eligible students (status = 1 and semester >= 7)
                const eligibleStudents = selectedStudents.filter(function(student) {
                    return student.status === 1 && student.semester >= 7;
                });

                if (eligibleStudents.length === 0) {
                    alert('Tidak ada mahasiswa yang memenuhi syarat kelulusan (harus mahasiswa aktif semester 7 ke atas).');
                    return;
                }

                // Update modal content
                $('#bulkGraduateSelectedCount').text(eligibleStudents.length);
                
                // Populate selected students list
                let studentsList = '';
                eligibleStudents.forEach(function(student, index) {
                    studentsList += `
                        <div class="d-flex justify-content-between align-items-center py-1 ${index > 0 ? 'border-top' : ''}">
                            <span>${student.name}</span>
                            <span class="badge bg-info text-white ms-2">Semester ${student.semester}</span>
                        </div>
                    `;
                });
                $('#selectedGraduateStudentsList').html(studentsList);

                // Set selected students in hidden input
                $('#selectedGraduateStudentsInput').val(JSON.stringify(eligibleStudents.map(s => s.code)));

                // Show modal
                $('#bulkGraduationModal').modal('show');
            });

            // Bulk action radio button change
            $('input[name="bulk_action"]').on('change', function() {
                const action = $(this).val();
                
                if (action === 'activate') {
                    $('#bulkReasonGroup').show();
                    $('#bulk_terminal_reason').prop('required', true);
                    $('#bulkActivateWarning').show();
                    $('#bulkDeactivateWarning').hide();
                    $('#bulkTerminalSubmitBtn').removeClass('btn-warning').addClass('btn-danger')
                        .html('<i class="fas fa-ban me-1"></i> Aktifkan Terminal');
                } else {
                    $('#bulkReasonGroup').hide();
                    $('#bulk_terminal_reason').prop('required', false);
                    $('#bulkActivateWarning').hide();
                    $('#bulkDeactivateWarning').show();
                    $('#bulkTerminalSubmitBtn').removeClass('btn-danger').addClass('btn-warning')
                        .html('<i class="fas fa-unlock me-1"></i> Nonaktifkan Terminal');
                }
            });

            // Reset modal when closed
            $('#bulkTerminalModal').on('hidden.bs.modal', function() {
                $('#bulkActivate').prop('checked', true);
                $('#bulk_terminal_reason').val('');
                $('#bulkReasonGroup').show();
                $('#bulkActivateWarning').show();
                $('#bulkDeactivateWarning').hide();
                $('#bulkTerminalSubmitBtn').removeClass('btn-warning').addClass('btn-danger')
                    .html('<i class="fas fa-ban me-1"></i> Aktifkan Terminal');
            });

            // Initialize DataTable
            $('.table').DataTable({
                searching: false, // Disable DataTable search since we have custom filter
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });

            // Function to load takhasus options
            function loadTakhasusOptions(prodiId, takhasusSelect, currentTakhasusId) {
                takhasusSelect.html('<option value="">Pilih Takhasus</option>');
                
                if (!prodiId) {
                    return;
                }
                
                takhasusSelect.html('<option value="">Loading...</option>');
                
                $.ajax({
                    url: '{{ route($spref . "akademik.get-takhasus-options") }}',
                    type: 'GET',
                    data: { prodi_id: prodiId },
                    success: function(data) {
                        takhasusSelect.html('<option value="">Pilih Takhasus</option>');
                        
                        if (data && data.length > 0) {
                            $.each(data, function(index, option) {
                                const selected = option.id == currentTakhasusId ? 'selected' : '';
                                takhasusSelect.append(`<option value="${option.id}" ${selected}>${option.name}</option>`);
                            });
                        } else {
                            takhasusSelect.append('<option value="" disabled>Tidak ada takhasus tersedia</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading takhasus:', error);
                        takhasusSelect.html('<option value="">Error loading takhasus</option>');
                    }
                });
            }

            // Auto-Terminal Check functionality (Desktop and Mobile)
            $('#autoTerminalCheckBtn, #autoTerminalCheckBtnMobile, #dryRunBtn, #dryRunBtnMobile').on('click', function() {
                performAutoTerminalCheck(true); // Dry run
            });

            $('#executeBtn, #executeBtnMobile').on('click', function() {
                performAutoTerminalCheck(false); // Actual execution
            });

            $('#executeAutoTerminalBtn').on('click', function() {
                performAutoTerminalCheck(false); // Execute from modal
            });

            $('#refreshPageBtn').on('click', function() {
                location.reload();
            });

            function performAutoTerminalCheck(dryRun = true) {
                // Show modal
                $('#autoTerminalModal').modal('show');
                
                // Reset modal state
                $('#autoTerminalLoading').show();
                $('#autoTerminalResults').hide();
                $('#executionResults').hide();
                $('#executeAutoTerminalBtn').hide();
                $('#refreshPageBtn').hide();

                // Make AJAX request
                $.ajax({
                    url: '{{ route($spref . "akademik.data-akademik-mahasiswa-auto-terminal") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        dry_run: dryRun
                    },
                    success: function(response) {
                        $('#autoTerminalLoading').hide();
                        
                        if (response.success) {
                            if (response.dry_run) {
                                // Show preview results
                                showPreviewResults(response);
                            } else {
                                // Show execution results
                                showExecutionResults(response);
                            }
                        } else {
                            showError(response.message || 'Terjadi kesalahan saat melakukan auto-terminal check.');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#autoTerminalLoading').hide();
                        let errorMessage = 'Terjadi kesalahan sistem.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showError(errorMessage);
                    }
                });
            }

            function showPreviewResults(response) {
                // Update counts
                $('#willTerminateCount').text(response.terminate_count || 0);
                $('#willReactivateCount').text(response.reactivate_count || 0);
                $('#alreadyProcessedCount').text(0);
                $('#totalProcessedCount').text((response.terminate_count || 0) + (response.reactivate_count || 0));
                
                // Update tab counts
                $('#terminateTabCount').text(response.terminate_count || 0);
                $('#reactivateTabCount').text(response.reactivate_count || 0);

                // Populate terminate list
                let terminateHtml = '';
                if (response.would_terminate && response.would_terminate.length > 0) {
                    response.would_terminate.forEach(function(student) {
                        terminateHtml += `
                            <tr>
                                <td>${student.name}</td>
                                <td>${student.nim}</td>
                                <td>${student.prodi}</td>
                                <td>${student.semester}</td>
                                <td>${student.unpaid_count} tagihan</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(student.unpaid_amount)}</td>
                            </tr>
                        `;
                    });
                } else {
                    terminateHtml = '<tr><td colspan="6" class="text-center text-muted">Tidak ada mahasiswa yang akan di-terminal</td></tr>';
                }
                $('#terminateList').html(terminateHtml);

                // Populate reactivate list
                let reactivateHtml = '';
                if (response.would_reactivate && response.would_reactivate.length > 0) {
                    response.would_reactivate.forEach(function(student) {
                        reactivateHtml += `
                            <tr>
                                <td>${student.name}</td>
                                <td>${student.nim}</td>
                                <td>${student.prodi}</td>
                                <td>${student.semester}</td>
                                <td>${student.reason}</td>
                            </tr>
                        `;
                    });
                } else {
                    reactivateHtml = '<tr><td colspan="5" class="text-center text-muted">Tidak ada mahasiswa yang akan direaktivasi</td></tr>';
                }
                $('#reactivateList').html(reactivateHtml);

                // Show results and execute button
                $('#autoTerminalResults').show();
                if ((response.terminate_count || 0) > 0 || (response.reactivate_count || 0) > 0) {
                    $('#executeAutoTerminalBtn').show();
                }
            }

            function showExecutionResults(response) {
                // Update counts
                $('#actualTerminatedCount').text(response.terminated_count || 0);
                $('#actualReactivatedCount').text(response.reactivated_count || 0);
                
                // Update message
                $('#executionMessage').text(response.message || 'Auto-terminal check selesai.');

                // Show results and refresh button
                $('#executionResults').show();
                $('#refreshPageBtn').show();

                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Auto-Terminal Selesai!',
                    text: response.message || 'Proses auto-terminal telah selesai.',
                    confirmButtonText: 'OK'
                });
            }

            function showError(message) {
                $('#autoTerminalLoading').hide();
                $('#autoTerminalResults').hide();
                $('#executionResults').hide();
                
                // Show error in modal body
                const errorHtml = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan</h6>
                        <p class="mb-0">${message}</p>
                    </div>
                `;
                $('#autoTerminalModal .modal-body').html(errorHtml);

                // Show error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonText: 'OK'
                });
            }

            // Handle program studi change and modal events for each mahasiswa
            @foreach ($mahasiswa as $item)
            $('#edit_prodi_id{{ $item->code }}').on('change', function() {
                const prodiId = $(this).val();
                const takhasusSelect = $('#edit_takhasus_id{{ $item->code }}');
                const currentTakhasusId = '{{ $item->takhasus_id ?? "" }}';
                loadTakhasusOptions(prodiId, takhasusSelect, currentTakhasusId);
            });

            $('#editData{{ $item->code }}').on('shown.bs.modal', function() {
                const prodiId = $('#edit_prodi_id{{ $item->code }}').val();
                const takhasusSelect = $('#edit_takhasus_id{{ $item->code }}');
                const currentTakhasusId = '{{ $item->takhasus_id ?? "" }}';
                
                if (prodiId) {
                    loadTakhasusOptions(prodiId, takhasusSelect, currentTakhasusId);
                }
            });
            @endforeach

            // Auto-Terminal Schedule Countdown Timer
            function updateAutoTerminalCountdown() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const currentSecond = now.getSeconds();
                
                // Next check is at 02:00 WIB daily
                let nextCheck = new Date();
                nextCheck.setHours(2, 0, 0, 0);
                
                // If current time is past 02:00, set next check to tomorrow
                if (currentHour >= 2) {
                    nextCheck.setDate(nextCheck.getDate() + 1);
                }
                
                // Calculate time difference
                const timeDiff = nextCheck.getTime() - now.getTime();
                
                if (timeDiff > 0) {
                    const hours = Math.floor(timeDiff / (1000 * 60 * 60));
                    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                    
                    // Update next check time
                    const nextCheckFormatted = nextCheck.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    $('#nextCheckTime').text(nextCheckFormatted);
                    
                    // Update countdown
                    const countdownText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    $('#countdownTimer').text(countdownText);
                    
                    // Add visual indicators based on time remaining
                    if (hours < 1) {
                        $('#countdownTimer').removeClass('text-success text-warning').addClass('text-danger');
                    } else if (hours < 6) {
                        $('#countdownTimer').removeClass('text-success text-danger').addClass('text-warning');
                    } else {
                        $('#countdownTimer').removeClass('text-warning text-danger').addClass('text-success');
                    }
                } else {
                    $('#nextCheckTime').text('Sedang berjalan...');
                    $('#countdownTimer').text('00:00:00').addClass('text-danger');
                }
            }
            
            // Initialize countdown and update every second
            updateAutoTerminalCountdown();
            setInterval(updateAutoTerminalCountdown, 1000);
            
            // Add notification when auto-check is imminent (within 5 minutes)
            function checkImminentAutoCheck() {
                const now = new Date();
                const nextCheck = new Date();
                nextCheck.setHours(2, 0, 0, 0);
                
                if (now.getHours() >= 2) {
                    nextCheck.setDate(nextCheck.getDate() + 1);
                }
                
                const timeDiff = nextCheck.getTime() - now.getTime();
                const minutesRemaining = Math.floor(timeDiff / (1000 * 60));
                
                // Show notification when 5 minutes remaining
                if (minutesRemaining === 5 && !sessionStorage.getItem('autoCheckNotified')) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Auto-Terminal Check Segera Dimulai',
                        text: 'Sistem akan melakukan pengecekan otomatis status terminal mahasiswa dalam 5 menit.',
                        timer: 10000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    sessionStorage.setItem('autoCheckNotified', 'true');
                }
                
                // Reset notification flag after auto-check time passes
                if (minutesRemaining > 5) {
                    sessionStorage.removeItem('autoCheckNotified');
                }
            }
            
            // Check for imminent auto-check every minute
            setInterval(checkImminentAutoCheck, 60000);
            
            // Add real-time status indicator
            function updateSystemStatus() {
                const now = new Date();
                const currentTime = now.getHours() * 100 + now.getMinutes();
                
                // Check if we're in auto-check window (02:00 - 02:05)
                if (currentTime >= 200 && currentTime <= 205) {
                    // Show running status
                    if (!$('#systemStatusIndicator').length) {
                        const statusHtml = `
                            <div id="systemStatusIndicator" class="alert alert-info alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm text-info me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div>
                                        <strong>Auto-Terminal Check Sedang Berjalan</strong><br>
                                        <small>Sistem sedang melakukan pengecekan otomatis status terminal mahasiswa. Proses ini biasanya memakan waktu 2-3 menit.</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(statusHtml);
                    }
                } else {
                    // Remove status indicator if exists
                    $('#systemStatusIndicator').remove();
                }
            }
            
            // Update system status every 30 seconds
            setInterval(updateSystemStatus, 30000);
            updateSystemStatus(); // Initial check
        });
    </script>
@endsection
