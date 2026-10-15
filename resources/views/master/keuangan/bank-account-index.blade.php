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

        /* Primary account highlight */
        .primary-account {
            background-color: rgba(40, 167, 69, 0.05);
            border-left: 4px solid #28a745;
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
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Rekening Bank
                    </button>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Rekening</h6>
                                <h3 class="mb-0">{{ count($bankAccounts) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Aktif</h6>
                                <h3 class="mb-0">{{ $bankAccounts->where('is_active', true)->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Rekening Utama</h6>
                                <h3 class="mb-0">{{ $bankAccounts->where('is_primary', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Rekening Bank Baru</h5>
                            <form action="{{ route('keuangan.bank-accounts-store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="bank_name" class="form-label">Nama Bank</label>
                                        <input type="text" class="form-control" name="bank_name" id="bank_name" required>
                                        @error('bank_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bank_code" class="form-label">Kode Bank</label>
                                        <input type="text" class="form-control" name="bank_code" id="bank_code" maxlength="10" required>
                                        @error('bank_code')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="account_number" class="form-label">Nomor Rekening</label>
                                        <input type="text" class="form-control" name="account_number" id="account_number" required>
                                        @error('account_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="account_name" class="form-label">Nama Pemilik Rekening</label>
                                        <input type="text" class="form-control" name="account_name" id="account_name" required>
                                        @error('account_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="branch" class="form-label">Cabang (Opsional)</label>
                                        <input type="text" class="form-control" name="branch" id="branch">
                                        @error('branch')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="virtual_account_prefix" class="form-label">Prefix Virtual Account (Opsional)</label>
                                        <input type="text" class="form-control" name="virtual_account_prefix" id="virtual_account_prefix" maxlength="10">
                                        @error('virtual_account_prefix')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                        @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                            <label class="form-check-label" for="is_active">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_primary" id="is_primary">
                                            <label class="form-check-label" for="is_primary">
                                                Rekening Utama
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
                                    <th>Bank</th>
                                    <th>Rekening</th>
                                    <th>Pemilik</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bankAccounts as $key => $account)
                                    <tr class="{{ $account->is_primary ? 'primary-account' : '' }}">
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Bank">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $account->bank_name }}</span>
                                                <small class="text-muted">{{ $account->bank_code }}</small>
                                                @if($account->branch)
                                                    <small class="text-muted">Cabang: {{ $account->branch }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Rekening">
                                            <div class="d-flex flex-column">
                                                <code>{{ $account->account_number }}</code>
                                                @if($account->virtual_account_prefix)
                                                    <small class="text-muted">VA Prefix: {{ $account->virtual_account_prefix }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Pemilik">{{ $account->account_name }}</td>
                                        <td data-label="Status">
                                            <div class="d-flex flex-column">
                                                <span class="badge {{ $account->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                                    {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                                @if($account->is_primary)
                                                    <small class="badge bg-light-warning text-warning mt-1">
                                                        <i class="fas fa-star"></i> Utama
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(!empty($account->code))
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $account->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Rekening">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(!$account->is_primary && !empty($account->code))
                                                    <form action="{{ route('keuangan.bank-accounts-set-primary', $account->code) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Jadikan Utama">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(!empty($account->code))
                                                    <form action="{{ route('keuangan.bank-accounts-toggle-status', $account->code) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $account->is_active ? 'warning' : 'success' }}" data-bs-toggle="tooltip" title="{{ $account->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="fas fa-{{ $account->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('keuangan.bank-accounts-destroy', $account->code) }}" method="POST" class="d-inline" id="delete-form-{{ $account->code }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="true" data-bs-toggle="tooltip" title="Hapus Rekening" onclick="confirmDelete('{{ $account->code }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">Kode tidak valid</span>
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
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Rekening Bank</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Rekening Bank.</p>
                    
                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Rekening Bank" untuk menambahkan rekening baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit rekening</li>
                            <li>Klik ikon <i class="fas fa-star"></i> untuk menjadikan rekening utama</li>
                            <li>Klik ikon <i class="fas fa-eye"></i> untuk mengaktifkan/menonaktifkan rekening</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus rekening</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light-info">
                        <h6 class="">Catatan Penting:</h6>
                        <ul class="mb-0">
                            <li>Hanya boleh ada satu rekening utama</li>
                            <li>Rekening utama akan digunakan sebagai default untuk pembayaran</li>
                            <li>Virtual Account Prefix digunakan untuk generate VA number</li>
                            <li>Rekening yang sedang digunakan tidak dapat dihapus</li>
                        </ul>
                    </div>
                    
                    @if(count($bankAccounts) > 0)
                        <div class="mt-4">
                            <h6>Rekening Terbaru</h6>
                            <div class="list-group">
                                @foreach($bankAccounts->take(5) as $recent)
                                    <div class="list-group-item list-group-item-action {{ $recent->is_primary ? 'list-group-item-success' : '' }}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                {{ $recent->bank_name }}
                                                @if($recent->is_primary)
                                                    <i class="fas fa-star text-warning"></i>
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $recent->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">
                                            <code>{{ $recent->account_number }}</code>
                                            <span class="badge {{ $recent->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                                {{ $recent->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </p>
                                        <small class="text-muted">{{ $recent->account_name }}</small>
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
    @foreach ($bankAccounts as $account)
        @if(!empty($account->code))
            <div class="modal fade" id="editData{{ $account->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $account->code }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form action="{{ route('keuangan.bank-accounts-update', $account->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $account->code }}">Edit Rekening Bank - {{ $account->bank_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bank_name{{ $account->code }}" class="form-label">Nama Bank</label>
                                    <input type="text" class="form-control" name="bank_name" id="edit_bank_name{{ $account->code }}" value="{{ $account->bank_name }}" required>
                                    @error('bank_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bank_code{{ $account->code }}" class="form-label">Kode Bank</label>
                                    <input type="text" class="form-control" name="bank_code" id="edit_bank_code{{ $account->code }}" value="{{ $account->bank_code }}" maxlength="10" required>
                                    @error('bank_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_account_number{{ $account->code }}" class="form-label">Nomor Rekening</label>
                                    <input type="text" class="form-control" name="account_number" id="edit_account_number{{ $account->code }}" value="{{ $account->account_number }}" required>
                                    @error('account_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_account_name{{ $account->code }}" class="form-label">Nama Pemilik Rekening</label>
                                    <input type="text" class="form-control" name="account_name" id="edit_account_name{{ $account->code }}" value="{{ $account->account_name }}" required>
                                    @error('account_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_branch{{ $account->code }}" class="form-label">Cabang (Opsional)</label>
                                    <input type="text" class="form-control" name="branch" id="edit_branch{{ $account->code }}" value="{{ $account->branch }}">
                                    @error('branch')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_virtual_account_prefix{{ $account->code }}" class="form-label">Prefix Virtual Account (Opsional)</label>
                                    <input type="text" class="form-control" name="virtual_account_prefix" id="edit_virtual_account_prefix{{ $account->code }}" value="{{ $account->virtual_account_prefix }}" maxlength="10">
                                    @error('virtual_account_prefix')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_description{{ $account->code }}" class="form-label">Deskripsi (Opsional)</label>
                                    <textarea class="form-control" name="description" id="edit_description{{ $account->code }}" rows="3">{{ $account->description }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active{{ $account->code }}" {{ $account->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_active{{ $account->code }}">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_primary" id="edit_is_primary{{ $account->code }}" {{ $account->is_primary ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_primary{{ $account->code }}">
                                            Rekening Utama
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
                order: [[4, 'desc']], // Sort by is_primary first
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });

        // Konfirmasi delete dengan SweetAlert
        function confirmDelete(code) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Rekening bank yang dihapus tidak dapat dikembalikan!",
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
    </script>
@endsection
