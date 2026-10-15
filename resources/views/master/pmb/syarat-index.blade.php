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
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <div>
                        <a href="{{ route((isset($spref) ? $spref : '') . 'pmb.syarat-uploads') }}" class="btn btn-info me-2">
                            <i class="fas fa-file-upload me-2"></i>Review Upload
                        </a>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Syarat
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Syarat</h6>
                                <h3 class="mb-0">{{ count($requirements) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Syarat Aktif</h6>
                                <h3 class="mb-0">{{ $requirements->where('is_active', true)->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Upload Pending</h6>
                                <h3 class="mb-0">{{ $requirements->sum('pending_uploads_count') }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-info rounded">
                                <h6 class="mb-2">Upload Disetujui</h6>
                                <h3 class="mb-0">{{ $requirements->sum('approved_uploads_count') }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Syarat Baru</h5>
                            <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.syarat-handle') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Syarat</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Contoh: Ijazah SMA" value="{{ old('name') }}">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Tipe Dokumen</label>
                                        <select class="form-select" name="type" id="type">
                                            <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File Umum</option>
                                            <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Gambar</option>
                                            <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Dokumen</option>
                                        </select>
                                        @error('type')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="allowed_extensions" class="form-label">Ekstensi File</label>
                                        <input type="text" class="form-control" name="allowed_extensions" id="allowed_extensions" placeholder="jpg,jpeg,png,pdf" value="{{ old('allowed_extensions', 'jpg,jpeg,png,pdf') }}">
                                        <small class="text-muted">Pisahkan dengan koma</small>
                                        @error('allowed_extensions')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="max_size_mb" class="form-label">Ukuran Maksimal (MB)</label>
                                        <input type="number" class="form-control" name="max_size_mb" id="max_size_mb" min="1" max="50" value="{{ old('max_size_mb', 5) }}">
                                        @error('max_size_mb')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsi syarat pendaftaran">{{ old('description') }}</textarea>
                                        @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="sort_order" class="form-label">Urutan</label>
                                        <input type="number" class="form-control" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', 0) }}">
                                        @error('sort_order')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pengaturan</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_required" id="is_required" {{ old('is_required') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_required">
                                                Wajib diisi
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Aktif
                                            </label>
                                        </div>
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
                                    <th>Nama Syarat</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Upload</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requirements as $key => $item)
                                    <tr>
                                        <td data-label="No" class="text-center">{{ ++$key }}</td>
                                        <td data-label="Nama Syarat">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $item->name }}</span>
                                                @if($item->description)
                                                    <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                @endif
                                                <div class="mt-1">
                                                    @if($item->is_required)
                                                        <span class="badge bg-danger">Wajib</span>
                                                    @else
                                                        <span class="badge bg-secondary">Opsional</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Tipe">
                                            <span class="badge bg-info">{{ ucfirst($item->type) }}</span>
                                            <br><small class="text-muted">{{ $item->allowed_extensions }}</small>
                                            <br><small class="text-muted">Max: {{ $item->max_size_mb }}MB</small>
                                        </td>
                                        <td data-label="Status">
                                            @if($item->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td data-label="Upload">
                                            <div class="d-flex flex-column">
                                                <small>Total: {{ $item->uploads_count }}</small>
                                                <small class="text-warning">Pending: {{ $item->pending_uploads_count }}</small>
                                                <small class="text-success">Disetujui: {{ $item->approved_uploads_count }}</small>
                                            </div>
                                        </td>
                                        <td data-label="Aksi" class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editData{{ $item->id }}" title="Edit Syarat">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route((isset($spref) ? $spref : '') . 'pmb.syarat-toggle-active', $item->id) }}" class="btn btn-sm {{ $item->is_active ? 'btn-warning' : 'btn-success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $item->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </a>
                                                <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.syarat-delete', $item->id) }}" method="POST" class="d-inline" id="delete-form-{{ $item->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="Hapus Syarat" onclick="confirmDelete('{{ $item->id }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada syarat pendaftaran</td>
                                    </tr>
                                @endforelse
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
                    <h5 class="card-title">Informasi Syarat</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Syarat Pendaftaran PMB.</p>
                    
                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Syarat" untuk menambahkan syarat baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data syarat</li>
                            <li>Klik ikon <i class="fas fa-eye"></i>/<i class="fas fa-eye-slash"></i> untuk mengaktifkan/nonaktifkan syarat</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus syarat</li>
                            <li>Klik "Review Upload" untuk melihat dan mengelola dokumen yang diupload mahasiswa</li>
                        </ul>
                    </div>
                    
                    @if(count($requirements) > 0)
                        <div class="mt-4">
                            <h6>Syarat Terbaru</h6>
                            <div class="list-group">
                                @foreach($requirements->take(5) as $syarat)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $syarat->name }}</h6>
                                            <small class="text-muted">{{ ucfirst($syarat->type) }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($syarat->description, 50) }}</p>
                                        <small>Upload: {{ $syarat->uploads_count }}</small>
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
    @foreach ($requirements as $item)
        <div class="modal fade" id="editData{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.syarat-update', $item->id) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit Syarat - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_name{{ $item->id }}" class="form-label">Nama Syarat</label>
                                    <input type="text" class="form-control" name="name" id="edit_name{{ $item->id }}" value="{{ $item->name }}" placeholder="Contoh: Ijazah SMA">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_type{{ $item->id }}" class="form-label">Tipe Dokumen</label>
                                    <select class="form-select" name="type" id="edit_type{{ $item->id }}">
                                        <option value="file" {{ $item->type == 'file' ? 'selected' : '' }}>File Umum</option>
                                        <option value="image" {{ $item->type == 'image' ? 'selected' : '' }}>Gambar</option>
                                        <option value="document" {{ $item->type == 'document' ? 'selected' : '' }}>Dokumen</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_allowed_extensions{{ $item->id }}" class="form-label">Ekstensi File</label>
                                    <input type="text" class="form-control" name="allowed_extensions" id="edit_allowed_extensions{{ $item->id }}" value="{{ $item->allowed_extensions }}">
                                    <small class="text-muted">Pisahkan dengan koma</small>
                                    @error('allowed_extensions')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_max_size_mb{{ $item->id }}" class="form-label">Ukuran Maksimal (MB)</label>
                                    <input type="number" class="form-control" name="max_size_mb" id="edit_max_size_mb{{ $item->id }}" min="1" max="50" value="{{ $item->max_size_mb }}">
                                    @error('max_size_mb')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_description{{ $item->id }}" class="form-label">Deskripsi</label>
                                    <textarea name="description" id="edit_description{{ $item->id }}" class="form-control" rows="3" placeholder="Deskripsi syarat pendaftaran">{{ $item->description }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_sort_order{{ $item->id }}" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" name="sort_order" id="edit_sort_order{{ $item->id }}" min="0" value="{{ $item->sort_order }}">
                                    @error('sort_order')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pengaturan</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_required" id="edit_is_required{{ $item->id }}" {{ $item->is_required ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_required{{ $item->id }}">
                                            Wajib diisi
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_active{{ $item->id }}">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
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
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            // Check if table has data rows (excluding header)
            var hasData = $('.table tbody tr').length > 0 && !$('.table tbody tr').first().find('td[colspan]').length;
            
            if (hasData) {
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
            }
        });

        // Konfirmasi delete dengan SweetAlert
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data syarat yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
