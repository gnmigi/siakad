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
                        <i class="fas fa-plus-circle me-2"></i>Tambah Metode Pembayaran
                    </button>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Metode</h6>
                                <h3 class="mb-0">{{ count($paymentMethods) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Aktif</h6>
                                <h3 class="mb-0">{{ $paymentMethods->where('is_active', true)->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Auto Verify</h6>
                                <h3 class="mb-0">{{ $paymentMethods->where('auto_verify', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Metode Pembayaran Baru</h5>
                            <form action="{{ route('keuangan.payment-methods-store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Metode Pembayaran</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Tipe</label>
                                        <select class="form-select" name="type" id="type" required>
                                            <option value="">Pilih Tipe</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="virtual_account">Virtual Account</option>
                                            <option value="e_wallet">E-Wallet</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                        @error('type')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="provider" class="form-label">Provider</label>
                                        <select class="form-select" name="provider" id="provider" required>
                                            <option value="">Pilih Provider</option>
                                            <option value="midtrans">Midtrans</option>
                                            <option value="xendit">Xendit</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                        @error('provider')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="sort_order" class="form-label">Urutan Tampilan</label>
                                        <input type="number" class="form-control" name="sort_order" id="sort_order" value="0" min="0">
                                        @error('sort_order')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fee_percentage" class="form-label">Biaya Persentase (%)</label>
                                        <input type="number" class="form-control" name="fee_percentage" id="fee_percentage" step="0.01" min="0" max="100">
                                        @error('fee_percentage')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fee_fixed" class="form-label">Biaya Tetap (Rp)</label>
                                        <input type="number" class="form-control" name="fee_fixed" id="fee_fixed" min="0">
                                        @error('fee_fixed')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
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
                                            <input class="form-check-input" type="checkbox" name="auto_verify" id="auto_verify">
                                            <label class="form-check-label" for="auto_verify">
                                                Verifikasi Otomatis
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
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Provider</th>
                                    <th>Biaya</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentMethods as $key => $method)
                                    <tr>
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Kode">
                                            <code>{{ $method->code }}</code>
                                        </td>
                                        <td data-label="Nama">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $method->name }}</span>
                                                <small class="text-muted">{{ $method->description }}</small>
                                            </div>
                                        </td>
                                        <td data-label="Tipe">
                                            <span class="badge bg-light-info text-info">
                                                {{ ucfirst(str_replace('_', ' ', $method->type)) }}
                                            </span>
                                        </td>
                                        <td data-label="Provider">{{ ucfirst($method->provider) }}</td>
                                        <td data-label="Biaya">
                                            @if($method->fee_percentage > 0 || $method->fee_fixed > 0)
                                                @if($method->fee_percentage > 0)
                                                    {{ $method->fee_percentage }}%
                                                @endif
                                                @if($method->fee_fixed > 0)
                                                    {{ $method->fee_percentage > 0 ? ' + ' : '' }}Rp {{ number_format($method->fee_fixed, 0, ',', '.') }}
                                                @endif
                                            @else
                                                <span class="text-muted">Gratis</span>
                                            @endif
                                        </td>
                                        <td data-label="Status">
                                            <div class="d-flex flex-column">
                                                <span class="badge {{ $method->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                                    {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                                @if($method->auto_verify)
                                                    <small class="badge bg-light-warning text-warning mt-1">Auto Verify</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $method->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Metode">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('keuangan.payment-methods-toggle-status', $method->code) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $method->is_active ? 'warning' : 'success' }}" data-bs-toggle="tooltip" title="{{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i class="fas fa-{{ $method->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('keuangan.payment-methods-destroy', $method->code) }}" method="POST" class="d-inline" id="delete-form-{{ $method->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="true" data-bs-toggle="tooltip" title="Hapus Metode" onclick="confirmDelete('{{ $method->code }}')">
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
                    <h5 class="card-title">Informasi Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Metode Pembayaran.</p>
                    
                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Metode Pembayaran" untuk menambahkan metode baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit metode pembayaran</li>
                            <li>Klik ikon <i class="fas fa-eye"></i> untuk mengaktifkan/menonaktifkan metode</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus metode</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light-info">
                        <h6 class="">Konfigurasi Midtrans:</h6>
                        <p class="mb-2">Untuk menggunakan Midtrans, pastikan konfigurasi berikut sudah diatur:</p>
                        <ul class="mb-0">
                            <li>Server Key: <code>{{ config('midtrans.server_key') ? 'Configured' : 'Not Set' }}</code></li>
                            <li>Client Key: <code>{{ config('midtrans.client_key') ? 'Configured' : 'Not Set' }}</code></li>
                            <li>Environment: <code>{{ config('midtrans.is_production') ? 'Production' : 'Sandbox' }}</code></li>
                        </ul>
                    </div>
                    
                    @if(count($paymentMethods) > 0)
                        <div class="mt-4">
                            <h6>Metode Terbaru</h6>
                            <div class="list-group">
                                @foreach($paymentMethods->take(5) as $recent)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $recent->name }}</h6>
                                            <small class="text-muted">{{ $recent->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">
                                            <span class="badge bg-light-info text-info">
                                                {{ ucfirst(str_replace('_', ' ', $recent->type)) }}
                                            </span>
                                            <span class="badge {{ $recent->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                                {{ $recent->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </p>
                                        <small class="text-muted">{{ $recent->provider }}</small>
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
    @foreach ($paymentMethods as $method)
        <div class="modal fade" id="editData{{ $method->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $method->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('keuangan.payment-methods-update', $method->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $method->code }}">Edit Metode Pembayaran - {{ $method->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_name{{ $method->code }}" class="form-label">Nama Metode Pembayaran</label>
                                    <input type="text" class="form-control" name="name" id="edit_name{{ $method->code }}" value="{{ $method->name }}" required>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_type{{ $method->code }}" class="form-label">Tipe</label>
                                    <select class="form-select" name="type" id="edit_type{{ $method->code }}" required>
                                        <option value="bank_transfer" {{ $method->type == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="virtual_account" {{ $method->type == 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                                        <option value="e_wallet" {{ $method->type == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                        <option value="credit_card" {{ $method->type == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="cash" {{ $method->type == 'cash' ? 'selected' : '' }}>Cash</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_provider{{ $method->code }}" class="form-label">Provider</label>
                                    <select class="form-select" name="provider" id="edit_provider{{ $method->code }}" required>
                                        <option value="midtrans" {{ $method->provider == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                                        <option value="xendit" {{ $method->provider == 'xendit' ? 'selected' : '' }}>Xendit</option>
                                        <option value="manual" {{ $method->provider == 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                    @error('provider')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_sort_order{{ $method->code }}" class="form-label">Urutan Tampilan</label>
                                    <input type="number" class="form-control" name="sort_order" id="edit_sort_order{{ $method->code }}" value="{{ $method->sort_order }}" min="0">
                                    @error('sort_order')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fee_percentage{{ $method->code }}" class="form-label">Biaya Persentase (%)</label>
                                    <input type="number" class="form-control" name="fee_percentage" id="edit_fee_percentage{{ $method->code }}" value="{{ $method->fee_percentage }}" step="0.01" min="0" max="100">
                                    @error('fee_percentage')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fee_fixed{{ $method->code }}" class="form-label">Biaya Tetap (Rp)</label>
                                    <input type="number" class="form-control" name="fee_fixed" id="edit_fee_fixed{{ $method->code }}" value="{{ $method->fee_fixed }}" min="0">
                                    @error('fee_fixed')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_description{{ $method->code }}" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="description" id="edit_description{{ $method->code }}" rows="3">{{ $method->description }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active{{ $method->code }}" {{ $method->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_active{{ $method->code }}">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="auto_verify" id="edit_auto_verify{{ $method->code }}" {{ $method->auto_verify ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_auto_verify{{ $method->code }}">
                                            Verifikasi Otomatis
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
                order: [[6, 'asc']], // Sort by sort_order
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });

        // Konfirmasi delete dengan SweetAlert
        function confirmDelete(code) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Metode pembayaran yang dihapus tidak dapat dikembalikan!",
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
