@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            color: #6c757d;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #435ebe;
            color: white;
            border-color: #435ebe;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stats-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        .quick-action-btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mobile table improvements */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .table th,
            .table td {
                white-space: nowrap;
                min-width: 120px;
            }
            
            .table .btn-group {
                display: flex;
                flex-wrap: nowrap;
            }
            
            .table .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e6ed;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }
    </style>
@endsection

@section('content')
    <!-- Statistics Dashboard -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center">
                <h3>{{ $stats['total_periode'] }}</h3>
                <p>Total Periode</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h3>{{ $stats['periode_aktif'] }}</h3>
                <p>Periode Aktif</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);">
                <h3>{{ $stats['total_pendaftar'] }}</h3>
                <p>Total Pendaftar</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);">
                <h3>{{ $stats['pendaftar_validated'] }}</h3>
                <p>Tervalidasi</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                <h3>{{ $stats['total_jalur'] }}</h3>
                <p>Total Jalur</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333;">
                <h3>{{ $stats['total_gelombang'] }}</h3>
                <p>Total Gelombang</p>
            </div>
        </div>
    </div>

    <!-- Main PMB Management Interface -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ $pages }}</h4>
            <p class="text-muted mb-0">Kelola seluruh aspek PMB dalam satu dashboard terpadu</p>
        </div>
        <div class="card-body">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="pmbTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="periode-tab" data-bs-toggle="tab" data-bs-target="#periode" type="button" role="tab">
                        <i class="fas fa-calendar-alt me-2"></i>Periode Pendaftaran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jalur-tab" data-bs-toggle="tab" data-bs-target="#jalur" type="button" role="tab">
                        <i class="fas fa-route me-2"></i>Jalur Pendaftaran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pendaftar-tab" data-bs-toggle="tab" data-bs-target="#pendaftar" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>Calon Mahasantri
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quick-actions-tab" data-bs-toggle="tab" data-bs-target="#quick-actions" type="button" role="tab">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="pmbTabContent">
                <!-- Periode Pendaftaran Tab -->
                <div class="tab-pane fade show active" id="periode" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Periode Pendaftaran</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodeModal">
                            <i class="fas fa-plus me-2"></i>Tambah Periode
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="periodeTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tahun Akademik</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodes as $key => $periode)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $periode->name }}</strong>
                                        @if($periode->desc)
                                            <br><small class="text-muted">{{ Str::limit($periode->desc, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $periode->taka->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($periode->ended_date)->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $now = now();
                                            $start = \Carbon\Carbon::parse($periode->start_date);
                                            $end = \Carbon\Carbon::parse($periode->ended_date);
                                        @endphp
                                        @if($now->between($start, $end))
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($now->lt($start))
                                            <span class="badge bg-warning">Belum Dimulai</span>
                                        @else
                                            <span class="badge bg-secondary">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editPeriode('{{ $periode->code }}', '{{ addslashes($periode->name) }}', '{{ $periode->taka_id }}', '{{ $periode->start_date }}', '{{ $periode->ended_date }}', '{{ addslashes($periode->desc ?? '') }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deletePeriode('{{ $periode->code }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Jalur Pendaftaran Tab -->
                <div class="tab-pane fade" id="jalur" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Jalur Pendaftaran</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJalurModal">
                            <i class="fas fa-plus me-2"></i>Tambah Jalur
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="jalurTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jalur</th>
                                    <th>Periode</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jalurs as $key => $jalur)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><strong>{{ $jalur->name }}</strong></td>
                                    <td>{{ $jalur->periode->name ?? '-' }}</td>
                                    <td>{{ Str::limit($jalur->desc ?? '-', 50) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editJalur('{{ $jalur->id }}', '{{ addslashes($jalur->name) }}', '{{ $jalur->periode_id }}', '{{ addslashes($jalur->desc ?? '') }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteJalur('{{ $jalur->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Calon Mahasantri Tab -->
                <div class="tab-pane fade" id="pendaftar" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Calon Mahasantri (50 Terbaru)</h5>
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="exportPendaftar('excel')">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                            <button class="btn btn-danger" onclick="exportPendaftar('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions untuk Pendaftar Terpilih -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-bolt me-2"></i>Quick Actions untuk Pendaftar Terpilih
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-success w-100" onclick="batchApprovePendaftar()">
                                        <i class="fas fa-check me-2"></i>Terima Terpilih
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning w-100" onclick="batchRejectPendaftar()">
                                        <i class="fas fa-times me-2"></i>Tolak Terpilih
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="batchConvertToMahasiswa()">
                                        <i class="fas fa-user-graduate me-2"></i>Convert Terpilih
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger w-100" onclick="batchDeletePendaftar()">
                                        <i class="fas fa-trash me-2"></i>Hapus Terpilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="pendaftarTable">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Jalur</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftars as $key => $pendaftar)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="pendaftar_ids[]" value="{{ $pendaftar->id }}" class="form-check-input pendaftar-checkbox">
                                    </td>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $pendaftar->name ?? $pendaftar->nama }}</strong>
                                        <br><small class="text-muted">{{ $pendaftar->phone ?? $pendaftar->no_hp }}</small>
                                    </td>
                                    <td>{{ $pendaftar->email }}</td>
                                    <td>{{ $pendaftar->jalur->name ?? '-' }}</td>
                                    <td>
                                        @if($pendaftar->status_validasi == 'validated')
                                            <span class="badge bg-success">Tervalidasi</span>
                                        @elseif($pendaftar->status_validasi == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $pendaftar->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info" title="Detail" onclick="viewPendaftar({{ $pendaftar->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" title="Upload Dokumen" onclick="viewPendaftarUploads({{ $pendaftar->id }})">
                                                <i class="fas fa-file-upload"></i>
                                            </button>
                                            
                                            @if($pendaftar->status_validasi == 'pending' || !$pendaftar->status_validasi)
                                                <button class="btn btn-sm btn-outline-success" title="Terima Pendaftaran" onclick="approvePendaftar({{ $pendaftar->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" title="Tolak Pendaftaran" onclick="rejectPendaftar({{ $pendaftar->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            
                                            @if($pendaftar->status_validasi == 'validated' && (!$pendaftar->mahasiswa_id || $pendaftar->mahasiswa_id == 0))
                                                <button class="btn btn-sm btn-outline-primary" title="Convert ke Mahasiswa" onclick="convertToMahasiswa({{ $pendaftar->id }})">
                                                    <i class="fas fa-user-graduate"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="deletePendaftar({{ $pendaftar->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions Tab -->
                <div class="tab-pane fade" id="quick-actions" role="tabpanel">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Quick Links</h5>
                                    <p class="text-muted">Akses cepat ke fitur PMB lainnya</p>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary quick-action-btn" onclick="window.location.href='#'">
                                            <i class="fas fa-money-bill me-2"></i>Kelola Biaya Pendaftaran
                                        </button>
                                        <button class="btn btn-outline-success quick-action-btn" onclick="window.location.href='{{ route((isset($spref) ? $spref : "") . "pmb.syarat-render") }}'">
                                            <i class="fas fa-list-check me-2"></i>Kelola Syarat Pendaftaran
                                        </button>
                                        <button class="btn btn-outline-info quick-action-btn" onclick="window.location.href='{{ route((isset($spref) ? $spref : "") . "pmb.syarat-uploads") }}'">
                                            <i class="fas fa-file-upload me-2"></i>Review Upload Dokumen
                                        </button>
                                        <button class="btn btn-outline-warning quick-action-btn" onclick="window.location.href='#'">
                                            <i class="fas fa-calendar me-2"></i>Kelola Jadwal PMB
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Periode Modal -->
    <div class="modal fade" id="addPeriodeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route((isset($spref) ? $spref : "") . "pmb.management.store-periode") }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Periode Pendaftaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Periode</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Akademik</label>
                                <select class="form-select" name="taka_id" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($takas as $taka)
                                        <option value="{{ $taka->id }}">{{ $taka->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="ended_date" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="desc" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Periode Modal -->
    <div class="modal fade" id="editPeriodeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPeriodeForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Periode Pendaftaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Periode</label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Akademik</label>
                                <select class="form-select" name="taka_id" id="edit_taka_id" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($takas as $taka)
                                        <option value="{{ $taka->id }}">{{ $taka->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="ended_date" id="edit_ended_date" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="desc" id="edit_desc" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Jalur Modal -->
    <div class="modal fade" id="editJalurModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editJalurForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Jalur Pendaftaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Jalur</label>
                            <input type="text" class="form-control" name="name" id="edit_jalur_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <select class="form-select" name="periode_id" id="edit_jalur_periode_id" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodes as $periode)
                                    <option value="{{ $periode->id }}">{{ $periode->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="desc" id="edit_jalur_desc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Jalur Modal -->
    <div class="modal fade" id="addJalurModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route((isset($spref) ? $spref : "") . "pmb.management.store-jalur") }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jalur Pendaftaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Jalur</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <select class="form-select" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodes as $periode)
                                    <option value="{{ $periode->id }}">{{ $periode->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="desc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#periodeTable, #jalurTable, #pendaftarTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Select All Checkbox
            $('#selectAll').change(function() {
                $('.pendaftar-checkbox').prop('checked', $(this).prop('checked'));
            });
        });

        function updateSelectedStatus() {
            const selectedIds = [];
            $('.pendaftar-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu pendaftar', 'warning');
                return;
            }

            const status = $('select[name="status"]').val();
            if (!status) {
                Swal.fire('Peringatan', 'Pilih status terlebih dahulu', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Update status ' + selectedIds.length + ' pendaftar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ url("pmb/management/update-status") }}'
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: 'status', value: status }));
                    
                    selectedIds.forEach(function(id) {
                        form.append($('<input>', { type: 'hidden', name: 'pendaftar_ids[]', value: id }));
                    });
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        function editJalur(id, name, periodeId, desc) {
            // Populate the edit form with current data
            $('#edit_jalur_name').val(name);
            $('#edit_jalur_periode_id').val(periodeId);
            $('#edit_jalur_desc').val(desc);

            // Set the form action URL
            $('#editJalurForm').attr('action', '{{ route((isset($spref) ? $spref : "") . "pmb.management.update-jalur", ["id" => ":id"]) }}'.replace(':id', id));

            // Show the modal
            $('#editJalurModal').modal('show');
        }

        function deleteJalur(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus jalur ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.delete-jalur", ["id" => ":id"]) }}'.replace(':id', id)
                    });

                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

                    $('body').append(form);
                    form.submit();
                }
            });
        }

        function exportPendaftar(type) {
            window.location.href = '{{ route((isset($spref) ? $spref : "") . "pmb.management.export") }}?type=' + type;
        }

        function editPeriode(code, name, takaId, startDate, endDate, desc) {
            // Populate the edit form with current data
            $('#edit_name').val(name);
            $('#edit_taka_id').val(takaId);
            $('#edit_start_date').val(startDate);
            $('#edit_ended_date').val(endDate);
            $('#edit_desc').val(desc);
            
            // Set the form action URL
            $('#editPeriodeForm').attr('action', '{{ route((isset($spref) ? $spref : "") . "pmb.management.update-periode", ["code" => ":code"]) }}'.replace(':code', code));
            
            // Show the modal
            $('#editPeriodeModal').modal('show');
        }

        function deletePeriode(code) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus periode ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.delete-periode", ["code" => ":code"]) }}'.replace(':code', code)
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        function viewPendaftar(id) {
            window.location.href = '{{ route((isset($spref) ? $spref : "") . "pmb.management.view-pendaftar", ["id" => ":id"]) }}'.replace(':id', id);
        }

        function viewPendaftarUploads(id) {
            // Redirect to pendaftar detail page to view uploads
            window.location.href = '{{ route((isset($spref) ? $spref : "") . "pmb.management.view-pendaftar", ["id" => ":id"]) }}'.replace(':id', id);
        }

        function deletePendaftar(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus data calon mahasiswa ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.delete-pendaftar", ["id" => ":id"]) }}'.replace(':id', id)
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        function approvePendaftar(id) {
            Swal.fire({
                title: 'Terima Pendaftaran',
                text: 'Yakin ingin menerima pendaftaran calon mahasiswa ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Terima',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.approve-pendaftar", ["id" => ":id"]) }}'.replace(':id', id)
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        function rejectPendaftar(id) {
            Swal.fire({
                title: 'Tolak Pendaftaran',
                text: 'Masukkan alasan penolakan:',
                input: 'textarea',
                inputPlaceholder: 'Alasan penolakan...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan harus diisi!'
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.reject-pendaftar", ["id" => ":id"]) }}'.replace(':id', id)
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: 'rejection_reason', value: result.value }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        // Program Studi data for convert function
        var prodiData = [
            @foreach(\App\Models\Akademik\ProgramStudi::where('status', 'aktif')->get() as $prodi)
                { id: {{ $prodi->id }}, name: '{{ addslashes($prodi->name) }}' },
            @endforeach
        ];

        function convertToMahasiswa(id) {
            // Build HTML content for program studi selection
            var htmlContent = '<div class="mb-3">';
            htmlContent += '<label class="form-label">Pilih Program Studi:</label>';
            htmlContent += '<select class="form-select" id="prodi_id" required>';
            htmlContent += '<option value="">Pilih Program Studi</option>';
            
            for (var i = 0; i < prodiData.length; i++) {
                htmlContent += '<option value="' + prodiData[i].id + '">' + prodiData[i].name + '</option>';
            }
            
            htmlContent += '</select>';
            htmlContent += '</div>';

            Swal.fire({
                title: 'Convert ke Mahasiswa',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Convert',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#007bff',
                preConfirm: function() {
                    var prodiId = document.getElementById('prodi_id').value;
                    if (!prodiId) {
                        Swal.showValidationMessage('Pilih program studi terlebih dahulu');
                        return false;
                    }
                    return { prodi_id: prodiId };
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengkonversi calon mahasiswa',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    // Submit conversion form
                    var form = $('<form>', {
                        method: 'POST',
                        action: '{{ route((isset($spref) ? $spref : "") . "pmb.management.convert") }}'
                    });
                    
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: 'pendaftar_id', value: id }));
                    form.append($('<input>', { type: 'hidden', name: 'prodi_id', value: result.value.prodi_id }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        // Batch Actions Functions
        function batchApprovePendaftar() {
            const selectedIds = getSelectedPendaftarIds();
            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu pendaftar', 'warning');
                return;
            }

            Swal.fire({
                title: 'Terima Pendaftaran Massal',
                text: 'Yakin ingin menerima ' + selectedIds.length + ' pendaftar yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Terima Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitBatchAction('approve', selectedIds);
                }
            });
        }

        function batchRejectPendaftar() {
            const selectedIds = getSelectedPendaftarIds();
            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu pendaftar', 'warning');
                return;
            }

            Swal.fire({
                title: 'Tolak Pendaftaran Massal',
                text: 'Masukkan alasan penolakan untuk ' + selectedIds.length + ' pendaftar:',
                input: 'textarea',
                inputPlaceholder: 'Alasan penolakan...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan harus diisi!'
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitBatchAction('reject', selectedIds, result.value);
                }
            });
        }

        function batchConvertToMahasiswa() {
            const selectedIds = getSelectedPendaftarIds();
            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu pendaftar', 'warning');
                return;
            }

            // Build HTML content for program studi selection
            var htmlContent = '<div class="mb-3">';
            htmlContent += '<label class="form-label">Pilih Program Studi untuk ' + selectedIds.length + ' pendaftar:</label>';
            htmlContent += '<select class="form-select" id="batch_prodi_id" required>';
            htmlContent += '<option value="">Pilih Program Studi</option>';
            
            for (var i = 0; i < prodiData.length; i++) {
                htmlContent += '<option value="' + prodiData[i].id + '">' + prodiData[i].name + '</option>';
            }
            
            htmlContent += '</select>';
            htmlContent += '</div>';

            Swal.fire({
                title: 'Convert ke Mahasiswa Massal',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Convert Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#007bff',
                preConfirm: function() {
                    var prodiId = document.getElementById('batch_prodi_id').value;
                    if (!prodiId) {
                        Swal.showValidationMessage('Pilih program studi terlebih dahulu');
                        return false;
                    }
                    return { prodi_id: prodiId };
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    submitBatchAction('convert', selectedIds, null, result.value.prodi_id);
                }
            });
        }

        function batchDeletePendaftar() {
            const selectedIds = getSelectedPendaftarIds();
            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu pendaftar', 'warning');
                return;
            }

            Swal.fire({
                title: 'Hapus Pendaftar Massal',
                text: 'Yakin ingin menghapus ' + selectedIds.length + ' pendaftar yang dipilih? Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitBatchAction('delete', selectedIds);
                }
            });
        }

        function getSelectedPendaftarIds() {
            const selectedIds = [];
            $('.pendaftar-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            return selectedIds;
        }

        function submitBatchAction(action, selectedIds, reason = null, prodiId = null) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses ' + selectedIds.length + ' pendaftar',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            let actionUrl = '';
            switch(action) {
                case 'approve':
                    actionUrl = '{{ url("pmb/management/batch-approve") }}';
                    break;
                case 'reject':
                    actionUrl = '{{ url("pmb/management/batch-reject") }}';
                    break;
                case 'convert':
                    actionUrl = '{{ url("pmb/management/batch-convert") }}';
                    break;
                case 'delete':
                    actionUrl = '{{ url("pmb/management/batch-delete") }}';
                    break;
            }

            const form = $('<form>', {
                method: 'POST',
                action: actionUrl
            });
            
            form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
            
            selectedIds.forEach(function(id) {
                form.append($('<input>', { type: 'hidden', name: 'pendaftar_ids[]', value: id }));
            });

            if (reason) {
                form.append($('<input>', { type: 'hidden', name: 'rejection_reason', value: reason }));
            }

            if (prodiId) {
                form.append($('<input>', { type: 'hidden', name: 'prodi_id', value: prodiId }));
            }
            
            $('body').append(form);
            form.submit();
        }
    </script>
@endsection
