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
        
        .bg-light-danger {
            background-color: rgba(220, 53, 69, 0.1);
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

        /* File preview */
        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
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
                margin-bottom: 1rem;
                border-bottom: 2px solid #eee;
            }
            .table-responsive td {
                position: relative;
                padding-left: 50%;
                text-align: left !important;
                border: none;
                border-bottom: 1px solid #eee;
            }
            .table-responsive td:before {
                position: absolute;
                top: 0;
                left: 0;
                width: 48%;
                padding-left: 1rem;
                white-space: nowrap;
                font-weight: bold;
                color: #888;
                content: attr(data-label);
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <a href="{{ route($spref . 'pmb.syarat-render') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Kelola Syarat
                    </a>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Upload</h6>
                                <h3 class="mb-0">{{ $uploads->total() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Menunggu Review</h6>
                                <h3 class="mb-0">{{ $uploads->where('status', 'pending')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Disetujui</h6>
                                <h3 class="mb-0">{{ $uploads->where('status', 'approved')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-danger rounded">
                                <h6 class="mb-2">Ditolak</h6>
                                <h3 class="mb-0">{{ $uploads->where('status', 'rejected')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Pendaftar</th>
                                    <th>Syarat</th>
                                    <th>File</th>
                                    <th>Upload</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($uploads as $key => $upload)
                                    <tr>
                                        <td data-label="No" class="text-center">{{ $uploads->firstItem() + $key }}</td>
                                        <td data-label="Pendaftar">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $upload->pendaftar->nama_lengkap ?? 'N/A' }}</span>
                                                <small class="text-muted">{{ $upload->pendaftar->email ?? 'N/A' }}</small>
                                                <small class="text-muted">{{ $upload->pendaftar->no_hp ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td data-label="Syarat">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $upload->requirement->name }}</span>
                                                <small class="text-muted">{{ $upload->requirement->type }}</small>
                                                @if($upload->requirement->is_required)
                                                    <span class="badge bg-danger mt-1">Wajib</span>
                                                @else
                                                    <span class="badge bg-secondary mt-1">Opsional</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="File">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $upload->original_name }}</span>
                                                <small class="text-muted">{{ number_format($upload->file_size / 1024, 1) }} KB</small>
                                                @if(in_array(strtolower(pathinfo($upload->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                    <img src="{{ asset('storage/' . $upload->file_path) }}" alt="Preview" class="file-preview mt-2">
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Upload">
                                            <div class="d-flex flex-column">
                                                <small>{{ $upload->uploaded_at->format('d/m/Y H:i') }}</small>
                                                <small class="text-muted">{{ $upload->uploaded_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            @if($upload->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($upload->status == 'rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu Review</span>
                                            @endif
                                            @if($upload->admin_notes)
                                                <br><small class="text-muted">{{ Str::limit($upload->admin_notes, 30) }}</small>
                                            @endif
                                        </td>
                                        <td data-label="Aksi" class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat File">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($upload->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $upload->id }}" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $upload->id }}" title="Tolak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#notesModal{{ $upload->id }}" title="Lihat Catatan">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada upload dokumen</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($uploads->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $uploads->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modals -->
    @foreach ($uploads as $upload)
        @if($upload->status == 'pending')
            <div class="modal fade" id="approveModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $upload->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route($spref . 'pmb.syarat-approve-upload', $upload->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="approveModalLabel{{ $upload->id }}">Setujui Upload</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Pendaftar:</strong> {{ $upload->pendaftar->nama_lengkap ?? 'N/A' }}</p>
                                <p><strong>Syarat:</strong> {{ $upload->requirement->name }}</p>
                                <p><strong>File:</strong> {{ $upload->original_name }}</p>
                                
                                <div class="mb-3">
                                    <label for="admin_notes_approve{{ $upload->id }}" class="form-label">Catatan Admin (Opsional)</label>
                                    <textarea name="admin_notes" id="admin_notes_approve{{ $upload->id }}" class="form-control" rows="3" placeholder="Catatan untuk pendaftar"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i> Setujui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="rejectModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $upload->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route($spref . 'pmb.syarat-reject-upload', $upload->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel{{ $upload->id }}">Tolak Upload</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Pendaftar:</strong> {{ $upload->pendaftar->nama_lengkap ?? 'N/A' }}</p>
                                <p><strong>Syarat:</strong> {{ $upload->requirement->name }}</p>
                                <p><strong>File:</strong> {{ $upload->original_name }}</p>
                                
                                <div class="mb-3">
                                    <label for="admin_notes_reject{{ $upload->id }}" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea name="admin_notes" id="admin_notes_reject{{ $upload->id }}" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="modal fade" id="notesModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel{{ $upload->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="notesModalLabel{{ $upload->id }}">Detail Upload</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Pendaftar:</strong> {{ $upload->pendaftar->nama_lengkap ?? 'N/A' }}</p>
                            <p><strong>Syarat:</strong> {{ $upload->requirement->name }}</p>
                            <p><strong>File:</strong> {{ $upload->original_name }}</p>
                            <p><strong>Status:</strong> 
                                @if($upload->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </p>
                            @if($upload->admin_notes)
                                <p><strong>Catatan Admin:</strong></p>
                                <div class="alert alert-light">{{ $upload->admin_notes }}</div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('.table').DataTable({
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
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[4, 'desc']], // Sort by upload date
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });
    </script>
@endsection
