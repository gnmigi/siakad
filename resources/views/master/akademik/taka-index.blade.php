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
            padding-top: 1rem;    /* Added padding top */
            padding-bottom: 0.75rem;
            text-align: left;     /* Default left align for headers */
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left; /* Default left align for cells */
        }

        /* Specific text alignment for certain columns */
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

        /* Collapsible form */
        .collapse {
            transition: all 0.3s ease;
        }

        .collapse.show {
            margin-top: 1rem;
        }

        /* Responsive styling */
        @media screen and (max-width: 768px) {
            .table td[data-label] .d-flex.align-items-center,
            .table td[data-label] .d-flex.flex-column.align-items-center {
                align-items: flex-end !important;
                text-align: right;
            }
        }

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
                text-align: left !important; /* Ensure left alignment in mobile view */
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
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <div class="btn-group">
                        <button class="btn btn-success" type="button" onclick="showAutoAdvancePreview()">
                            <i class="fas fa-rocket me-2"></i>Auto Naikkan Tahun Akademik
                        </button>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Tahun Akademik
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Tahun Akademik</h6>
                                <h3 class="mb-0">{{ count($taka) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Semester Aktif</h6>
                                <h3 class="mb-0">{{ $taka->where('status', 'Aktif')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Semester Tidak Aktif</h6>
                                <h3 class="mb-0">{{ $taka->where('status', 'Tidak Aktif')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Tahun Akademik Baru</h5>
                            <form action="{{ route($spref . 'akademik.taka-handle') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Tahun Akademik <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" 
                                               value="{{ old('name') }}" placeholder="Contoh: 2023/2024" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Tipe Semester <span class="text-danger">*</span></label>
                                        <select class="form-select @error('type') is-invalid @enderror" name="type" id="type" required>
                                            <option value="" {{ old('type') == '' ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value="Ganjil" {{ old('type') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ old('type') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                               name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ended_date" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('ended_date') is-invalid @enderror" 
                                               name="ended_date" id="ended_date" value="{{ old('ended_date') }}" required>
                                        @error('ended_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                            <option value="" {{ old('status') == '' ? 'selected' : '' }}>Pilih Status</option>
                                            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="desc" class="form-label">Deskripsi</label>
                                        <textarea name="desc" id="desc" class="form-control @error('desc') is-invalid @enderror" 
                                                  rows="4" placeholder="Deskripsi tahun akademik">{{ old('desc') }}</textarea>
                                        @error('desc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tahun Akademik</th>
                                    <th class="text-center">Semester</th>
                                    <th class="text-center">Periode</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taka as $key => $item)
                                    <tr>
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Tahun Akademik">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $item->name }}</span>
                                                @if($item->type)
                                                    <small class="text-muted">{{ Str::limit($item->type) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Semester">
                                            <span class="badge bg-light-primary text-primary">{{ $item->type }}</span>
                                        </td>
                                        <td data-label="Periode">
                                            <small>{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->ended_date)->format('d M Y') }}</small>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge {{ $item->status == 'Aktif' ? 'bg-light-success text-success' : 'bg-light-warning text-warning' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Tahun Akademik">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route($spref . 'akademik.taka-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="true" data-bs-toggle="tooltip" title="Hapus Tahun Akademik" onclick="confirmDelete('{{ $item->code }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Tahun Akademik</h5>
                </div>
                <div class="card-body">
                    <p>Tahun Akademik adalah periode waktu yang digunakan untuk kegiatan akademik di perguruan tinggi.</p>
                    
                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Tahun Akademik" untuk menambahkan tahun akademik baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data tahun akademik</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus tahun akademik</li>
                        </ul>
                    </div>
                    
                    @if(count($taka) > 0)
                        <div class="mt-4">
                            <h6>Tahun Akademik Aktif</h6>
                            <div class="list-group">
                                @foreach($taka->where('status', 'Aktif')->take(3) as $active)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $active->name }}</h6>
                                            <small class="text-muted">{{ $active->type }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($active->desc, 50) }}</p>
                                        <small>{{ \Carbon\Carbon::parse($active->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($active->ended_date)->format('d M Y') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach ($taka as $item)
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.taka-update', $item->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Tahun Akademik - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name{{ $item->code }}" class="form-label">Nama Tahun Akademik</label>
                                <input type="text" class="form-control" name="name" id="edit_name{{ $item->code }}" value="{{ $item->name }}" placeholder="Contoh: 2023/2024">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_type{{ $item->code }}" class="form-label">Tipe Semester</label>
                                <select class="form-select" name="type" id="edit_type{{ $item->code }}">
                                    <option value="Ganjil" {{ $item->type == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $item->type == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_start_date{{ $item->code }}" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date{{ $item->code }}" value="{{ $item->formatted_start_date }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_ended_date{{ $item->code }}" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" name="ended_date" id="edit_ended_date{{ $item->code }}" value="{{ $item->formatted_ended_date }}">
                                @error('ended_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_desc{{ $item->code }}" class="form-label">Deskripsi</label>
                                <textarea name="desc" id="edit_desc{{ $item->code }}" class="form-control" rows="4" placeholder="Deskripsi tahun akademik">{{ $item->desc }}</textarea>
                                @error('desc')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_status{{ $item->code }}" class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit_status{{ $item->code }}">
                                    <option value="Aktif" {{ $item->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ $item->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Auto Advance Preview Modal -->
    <div class="modal fade" id="autoAdvancePreviewModal" tabindex="-1" aria-labelledby="autoAdvancePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="autoAdvancePreviewModalLabel">
                        <i class="fas fa-rocket me-2"></i>Preview Auto Naikkan Tahun Akademik
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat preview...</p>
                    </div>
                </div>
                <div class="modal-footer" id="previewFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="executeAdvanceBtn">
                        <i class="fas fa-rocket me-2"></i>Jalankan Auto Advance
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Advance Confirmation Modal -->
    <div class="modal fade" id="autoAdvanceConfirmModal" tabindex="-1" aria-labelledby="autoAdvanceConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="autoAdvanceConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Auto Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Peringatan!</strong> Tindakan ini akan:
                        <ul class="mb-0 mt-2">
                            <li>Membuat tahun akademik baru secara otomatis</li>
                            <li>Menaikkan semester semua mahasiswa aktif</li>
                            <li>Mengubah status mahasiswa semester > 14 menjadi Non-Aktif (Drop Out)</li>
                            <li>Mengaktifkan tahun akademik baru</li>
                        </ul>
                    </div>
                    <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="autoAdvanceForm" action="{{ route($spref . 'akademik.taka-auto-advance') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-rocket me-2"></i>Ya, Jalankan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });

            // Client-side validation for date fields
            $('#start_date, #ended_date').on('change', function() {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#ended_date').val());
                
                if (startDate && endDate && startDate >= endDate) {
                    $('#ended_date').addClass('is-invalid');
                    if (!$('.date-error').length) {
                        $('#ended_date').after('<div class="invalid-feedback date-error">Tanggal berakhir harus setelah tanggal mulai</div>');
                    }
                } else {
                    $('#ended_date').removeClass('is-invalid');
                    $('.date-error').remove();
                }
            });

            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });

        // Konfirmasi delete dengan SweetAlert
        function confirmDelete(code) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data tahun akademik yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + code).submit();
                }
            });
        }

        // Show collapse form if there are validation errors
        @if($errors->any())
            $(document).ready(function() {
                $('#collapseForm').collapse('show');
                $('html, body').animate({
                    scrollTop: $('#collapseForm').offset().top - 100
                }, 500);
            });
        @endif

        // Auto Advance Academic Year Functions
        function showAutoAdvancePreview() {
            // Show modal
            $('#autoAdvancePreviewModal').modal('show');
            
            // Reset modal content
            $('#previewContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat preview...</p>
                </div>
            `);
            $('#previewFooter').hide();
            
            // Fetch preview data
            $.ajax({
                url: '{{ route($spref . "akademik.taka-preview-advancement") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        displayPreviewData(response.data, response.warnings);
                        $('#previewFooter').show();
                    } else {
                        displayPreviewError(response.message, response.errors);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat memuat preview.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    displayPreviewError(errorMessage, xhr.responseJSON?.errors);
                }
            });
        }

        function displayPreviewData(data, warnings) {
            let warningsHtml = '';
            if (warnings && warnings.length > 0) {
                warningsHtml = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Peringatan:</h6>
                        <ul class="mb-0">
                            ${warnings.map(warning => `<li>${warning}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }

            let semesterDistributionHtml = '';
            if (data.student_statistics.semester_distribution.length > 0) {
                semesterDistributionHtml = `
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Semester Saat Ini</th>
                                    <th>Semester Setelah Naik</th>
                                    <th>Jumlah Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.student_statistics.semester_distribution.map(item => `
                                    <tr>
                                        <td><span class="badge bg-light-info text-info">Semester ${item.current_semester}</span></td>
                                        <td><span class="badge bg-light-success text-success">Semester ${item.next_semester}</span></td>
                                        <td><strong>${item.count}</strong> mahasiswa</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            let droppedOutStudentsHtml = '';
            if (data.student_statistics.dropped_out_students.length > 0) {
                droppedOutStudentsHtml = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Mahasiswa yang Akan Drop Out:</h6>
                        ${data.student_statistics.dropped_out_students.map(group => `
                            <p class="mb-1"><strong>${group.count} mahasiswa</strong> dari semester ${group.current_semester} akan drop out (Non-Aktif)</p>
                        `).join('')}
                    </div>
                `;
            }

            $('#previewContent').html(`
                ${warningsHtml}
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-light-primary">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Tahun Akademik Saat Ini</h6>
                            </div>
                            <div class="card-body">
                                <h5>${data.current_academic_year.name}</h5>
                                <p class="mb-1"><span class="badge bg-light-info text-info">${data.current_academic_year.type}</span></p>
                                <small class="text-muted">${data.current_academic_year.period}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-light-success">
                                <h6 class="mb-0"><i class="fas fa-arrow-right me-2"></i>Tahun Akademik Baru</h6>
                            </div>
                            <div class="card-body">
                                <h5>${data.next_academic_year.name}</h5>
                                <p class="mb-1"><span class="badge bg-light-success text-success">${data.next_academic_year.type}</span></p>
                                <small class="text-muted">${data.next_academic_year.period}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light-primary rounded">
                            <h3 class="text-primary">${data.student_statistics.total_active_students}</h3>
                            <p class="mb-0">Total Mahasiswa Aktif</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light-success rounded">
                            <h3 class="text-success">${data.student_statistics.total_advancing}</h3>
                            <p class="mb-0">Akan Naik Semester</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light-warning rounded">
                            <h3 class="text-warning">${data.student_statistics.total_dropped_out}</h3>
                            <p class="mb-0">Akan Drop Out</p>
                        </div>
                    </div>
                </div>

                ${droppedOutStudentsHtml}

                <h6><i class="fas fa-list me-2"></i>Detail Kenaikan Semester:</h6>
                ${semesterDistributionHtml}
            `);
        }

        function displayPreviewError(message, errors) {
            let errorHtml = `<div class="alert alert-danger"><h6>Error:</h6><p>${message}</p>`;
            
            if (errors && errors.length > 0) {
                errorHtml += `<ul class="mb-0">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`;
            }
            
            errorHtml += '</div>';
            $('#previewContent').html(errorHtml);
        }

        // Execute auto advance
        $('#executeAdvanceBtn').on('click', function() {
            $('#autoAdvancePreviewModal').modal('hide');
            $('#autoAdvanceConfirmModal').modal('show');
        });

        // Handle form submission with loading state
        $('#autoAdvanceForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        });
    </script>
@endsection
