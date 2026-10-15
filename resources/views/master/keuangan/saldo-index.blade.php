@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Stats cards - Light Mode */
        .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
        .bg-light-success { background-color: rgba(40, 167, 69, 0.1); }
        .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
        .bg-light-info { background-color: rgba(23, 162, 184, 0.1); }

        /* Stats cards - Dark Mode */
        [data-bs-theme="dark"] .bg-light-primary {
            background-color: rgba(67, 94, 190, 0.15) !important;
            border: 1px solid rgba(67, 94, 190, 0.3);
        }
        [data-bs-theme="dark"] .bg-light-success {
            background-color: rgba(40, 167, 69, 0.15) !important;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        [data-bs-theme="dark"] .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.15) !important;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        [data-bs-theme="dark"] .bg-light-info {
            background-color: rgba(23, 162, 184, 0.15) !important;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        /* Payment Statistics Card - Dark Mode */
        [data-bs-theme="dark"] .card.border-0.bg-light {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        [data-bs-theme="dark"] .card.border-0.bg-light .card-body {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Dark mode text colors */
        [data-bs-theme="dark"] .text-success { color: #20c997 !important; }
        [data-bs-theme="dark"] .text-warning { color: #ffc107 !important; }
        [data-bs-theme="dark"] .text-primary { color: #5873ce !important; }
        [data-bs-theme="dark"] .text-info { color: #17a2b8 !important; }
        [data-bs-theme="dark"] .text-danger { color: #dc3545 !important; }

        /* Card styling */
        .card {
            border: none;
            border-radius: 12px;
        }
        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
        }
        .card-body { padding: 1.5rem; }

        /* Dark mode card styling */
        [data-bs-theme="dark"] .card {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
        }
        [data-bs-theme="dark"] .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.9);
        }
        [data-bs-theme="dark"] .card-body {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Form & Form Controls */
        [data-bs-theme="dark"] .collapse .card.card-body.border {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.15);
            padding: 0.6rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
        }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
            background-color: rgba(0, 0, 0, 0.2);
            border-color: rgba(255,255,255,0.1);
            color: #fff;
        }

        /* Badge styling - Fixed for Dark/Light Mode Contras */
        .badge {
            padding: 0.55em 0.8em;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        [data-bs-theme="dark"] .badge.bg-success { background-color: #20c997 !important; color: #000 !important; }
        [data-bs-theme="dark"] .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        [data-bs-theme="dark"] .badge.bg-primary { background-color: #0d6efd !important; color: #fff !important; }
        [data-bs-theme="dark"] .badge.bg-info { background-color: #17a2b8 !important; color: #fff !important; }
        [data-bs-theme="dark"] .badge.bg-danger { background-color: #dc3545 !important; color: #fff !important; }

        /* List group & Alert dark mode */
        [data-bs-theme="dark"] .list-group-item {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.9) !important;
        }
        [data-bs-theme="dark"] .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }
        [data-bs-theme="dark"] .alert-light-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            border-color: rgba(40, 167, 69, 0.2) !important;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Table styling */
        .table { margin-bottom: 0; }
        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        [data-bs-theme="dark"] .table thead th {
            border-bottom: 2px solid rgba(255,255,255,0.1);
            color: #aeb3b9;
        }
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-sm { padding: 0.35rem 0.75rem; border-radius: 6px; }
        
        .collapse { transition: all 0.3s ease; }
        .collapse.show { margin-top: 1rem; }

        /* Responsive Mobile Table Styling */
        @media (max-width: 768px) {
            .table-responsive table, .table-responsive thead, .table-responsive tbody, .table-responsive th, .table-responsive td, .table-responsive tr {
                display: block; width: 100%;
            }
            .table-responsive thead { display: none; }
            .table-responsive tr {
                margin-bottom: 1.5rem;
                padding: 1rem;
                border-radius: 12px;
                background: #f8f9fa;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            }
            [data-bs-theme="dark"] .table-responsive tr {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .table-responsive td {
                position: relative;
                padding-left: 35% !important;
                text-align: left !important;
                border: none;
                border-bottom: 1px solid rgba(0,0,0,0.05);
                padding-top: 0.75rem; padding-bottom: 0.75rem;
            }
            [data-bs-theme="dark"] .table-responsive td { border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
            .table-responsive td:last-child { border-bottom: none; }
            .table-responsive td:before {
                position: absolute; top: 0.75rem; left: 0; width: 32%;
                padding-left: 0.5rem; font-weight: 600; color: #888;
                content: attr(data-label) ""; font-size: 0.85rem;
            }
            [data-bs-theme="dark"] .table-responsive td:before { color: rgba(255, 255, 255, 0.6); }
            
            .table-responsive td[data-label="Detail Transaksi"] { padding-left: 0.5rem !important; }
            .table-responsive td[data-label="Detail Transaksi"]:before {
                position: static; display: block; width: 100%; margin-bottom: 0.5rem; padding-left: 0;
            }
            .table td[data-label] .d-flex.align-items-center, .table td[data-label] .d-flex.flex-column {
                align-items: flex-start !important; text-align: left;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ $pages }}</h5>
                    <button class="btn btn-primary shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Saldo
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="p-3 bg-light-success rounded-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-muted">Total Pemasukan</h6>
                                        <h4 class="mb-0 text-success fw-bold">Rp {{ number_format($statistics['total_pemasukan'], 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ $saldos->where('type', 'Pemasukan')->count() }} transaksi</small>
                                    </div>
                                    <div class="text-success opacity-75">
                                        <i class="fas fa-arrow-up fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="p-3 bg-light-warning rounded-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-muted">Total Pengeluaran</h6>
                                        <h4 class="mb-0 text-warning fw-bold">Rp {{ number_format($statistics['total_pengeluaran'], 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ $saldos->where('type', 'Pengeluaran')->count() }} transaksi</small>
                                    </div>
                                    <div class="text-warning opacity-75">
                                        <i class="fas fa-arrow-down fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="p-3 bg-light-primary rounded-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-muted">Saldo Bersih</h6>
                                        <h4 class="mb-0 fw-bold {{ $statistics['saldo_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($statistics['saldo_bersih'], 0, ',', '.') }}
                                        </h4>
                                        <small class="text-muted">Pemasukan - Pengeluaran</small>
                                    </div>
                                    <div class="text-primary opacity-75">
                                        <i class="fas fa-wallet fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="p-3 bg-light-info rounded-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-muted">Trx Hari Ini</h6>
                                        <h4 class="mb-0 text-info fw-bold">{{ $statistics['total_transaksi_hari_ini'] }}</h4>
                                        <small class="text-muted">
                                            Masuk: Rp {{ number_format($statistics['pemasukan_hari_ini'], 0, ',', '.') }}
                                        </small>
                                    </div>
                                    <div class="text-info opacity-75">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light shadow-none rounded-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-4 fw-bold text-secondary">
                                        <i class="fas fa-graduation-cap me-2"></i>Statistik Pembayaran Mahasiswa
                                    </h6>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-2 me-3 shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-money-bill-wave text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-muted">Total Pembayaran</h6>
                                                    <h5 class="mb-0 text-success fw-bold">Rp {{ number_format($statistics['total_pembayaran_mahasiswa'], 0, ',', '.') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle p-2 me-3 shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-calendar-alt text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-muted">Bulan Ini</h6>
                                                    <h5 class="mb-0 text-primary fw-bold">Rp {{ number_format($statistics['pembayaran_mahasiswa_bulan_ini'], 0, ',', '.') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info rounded-circle p-2 me-3 shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-users text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-muted">Bayar Hari Ini</h6>
                                                    <h5 class="mb-0 text-info fw-bold">{{ $statistics['jumlah_mahasiswa_bayar_hari_ini'] }} orang</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border rounded-3 mb-4 shadow-sm">
                            <h5 class="card-title mb-3 fw-bold">Tambah Saldo Baru</h5>
                            <form action="{{ route('keuangan.saldo-handle') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label fw-medium">Tipe Saldo</label>
                                        <select class="form-select" name="type" id="type" required>
                                            <option value="">Pilih Tipe</option>
                                            <option value="Pemasukan">Pemasukan</option>
                                            <option value="Pengeluaran">Pengeluaran</option>
                                        </select>
                                        @error('type')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="amount" class="form-label fw-medium">Jumlah</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0 bg-transparent">Rp</span>
                                            <input type="number" class="form-control border-start-0 ps-0" name="amount" id="amount" min="0" required>
                                        </div>
                                        @error('amount')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="source" class="form-label fw-medium">Sumber Dana</label>
                                        <input type="text" class="form-control" name="source" id="source" required>
                                        @error('source')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="transaction_code" class="form-label fw-medium">Kode Transaksi (Opsional)</label>
                                        <input type="text" class="form-control" name="transaction_code" id="transaction_code">
                                        @error('transaction_code')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="tagihan_id" class="form-label fw-medium">Tagihan (Opsional)</label>
                                        <select class="form-select" name="tagihan_id" id="tagihan_id">
                                            <option value="">Pilih Tagihan...</option>
                                        </select>
                                        @error('tagihan_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="desc" class="form-label fw-medium">Deskripsi (Opsional)</label>
                                        <textarea name="desc" id="desc" class="form-control" rows="3" placeholder="Tambahkan catatan transaksi..."></textarea>
                                        @error('desc')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i>Simpan Data
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive mt-2">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th width="15%">Kode</th>
                                    <th width="10%">Tipe</th>
                                    <th width="30%">Detail Transaksi</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="10%">Status</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($saldos as $key => $item)
                                    <tr>
                                        <td data-label="No" class="text-center">{{ ++$key }}</td>
                                        <td data-label="Kode">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $item->code }}</span>
                                                @if($item->transaction_code)
                                                    <small class="text-muted"><i class="fas fa-barcode me-1"></i>{{ $item->transaction_code }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Tipe">
                                            <span class="badge rounded-pill {{ $item->type == 'Pemasukan' ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                                {{ $item->type }}
                                            </span>
                                        </td>
                                        <td data-label="Detail Transaksi">
                                            <div class="d-flex flex-column">
                                                @if($item->tagihan && $item->tagihan->mahasiswa)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-user text-primary me-2 w-15px text-center"></i>
                                                        <span class="fw-bold">{{ $item->tagihan->mahasiswa->name }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-id-card text-info me-2 w-15px text-center"></i>
                                                        <small class="text-muted">NIM: {{ $item->tagihan->mahasiswa->nim }}</small>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-file-invoice text-success me-2 w-15px text-center"></i>
                                                        <small class="text-muted">{{ $item->tagihan->name ?? 'Tagihan' }}</small>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-building text-secondary me-2 w-15px text-center"></i>
                                                        <span class="fw-bold">{{ $item->source }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if($item->desc)
                                                    <div class="d-flex align-items-start mt-1">
                                                        <i class="fas fa-sticky-note text-warning me-2 mt-1 w-15px text-center"></i>
                                                        <small class="text-muted">{{ Str::limit($item->desc, 50) }}</small>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex align-items-center mt-2 pt-2 border-top border-light">
                                                    <i class="fas fa-clock text-muted me-2 w-15px text-center"></i>
                                                    <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Jumlah">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold fs-6 {{ $item->type == 'Pemasukan' ? 'text-success' : 'text-warning' }}">
                                                    {{ $item->type == 'Pemasukan' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge rounded-pill {{ $item->status == 'Sukses' ? 'bg-success text-white' : ($item->status == 'Pending' ? 'bg-warning text-dark' : 'bg-danger text-white') }}">
                                                <i class="fas {{ $item->status == 'Sukses' ? 'fa-check' : ($item->status == 'Pending' ? 'fa-clock' : 'fa-times') }} me-1"></i>
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td data-label="Aksi" class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Saldo">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('keuangan.saldo-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger rounded-end" data-bs-toggle="tooltip" title="Hapus Saldo" onclick="confirmDelete('{{ $item->code }}')">
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
        
        <div class="col-lg-4 col-12 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title fw-bold mb-0">Informasi Saldo</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light-success border-0 shadow-sm rounded-3">
                        <h6 class="fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Petunjuk:</h6>
                        <ul class="mb-0 ps-3 text-muted small">
                            <li class="mb-1">Klik <strong>Tambah Saldo</strong> untuk entry baru.</li>
                            <li class="mb-1">Gunakan ikon <i class="fas fa-edit text-primary"></i> untuk mengubah nominal/keterangan.</li>
                            <li>Gunakan ikon <i class="fas fa-trash text-danger"></i> untuk menghapus histori.</li>
                        </ul>
                    </div>
                    
                    @if(count($recent_transactions) > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="fas fa-history me-2"></i>Transaksi Terbaru
                            </h6>
                            <div class="list-group list-group-flush border-top border-bottom">
                                @foreach($recent_transactions as $recent)
                                    <div class="list-group-item bg-transparent px-0 py-3">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <h6 class="mb-0 fw-bold">{{ $recent->code }}</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $recent->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-2 d-flex align-items-center">
                                                    <span class="badge rounded-pill {{ $recent->type == 'Pemasukan' ? 'bg-success text-white' : 'bg-warning text-dark' }} me-2" style="font-size: 0.65rem;">
                                                        {{ $recent->type }}
                                                    </span>
                                                    <span class="fw-bold {{ $recent->type == 'Pemasukan' ? 'text-success' : 'text-warning' }}">
                                                        {{ $recent->type == 'Pemasukan' ? '+' : '-' }} Rp {{ number_format($recent->amount, 0, ',', '.') }}
                                                    </span>
                                                </p>
                                                <div class="text-muted small">
                                                    <i class="fas fa-arrow-right me-1 text-secondary" style="font-size: 0.7rem;"></i> 
                                                    {{ Str::limit($recent->source, 40) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @foreach ($saldos as $item)
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('keuangan.saldo-update', $item->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold">Edit Saldo - <span class="text-primary">{{ $item->code }}</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Tipe Saldo</label>
                                    <select class="form-select" name="type" required>
                                        <option value="Pemasukan" {{ $item->type == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                        <option value="Pengeluaran" {{ $item->type == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Jumlah</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">Rp</span>
                                        <input type="number" class="form-control border-start-0 ps-0" name="amount" value="{{ $item->amount }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Sumber Dana</label>
                                    <input type="text" class="form-control" name="source" value="{{ $item->source }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Kode Transaksi</label>
                                    <input type="text" class="form-control" name="transaction_code" value="{{ $item->transaction_code }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Sukses" {{ $item->status == 'Sukses' ? 'selected' : '' }}>Sukses</option>
                                        <option value="Gagal" {{ $item->status == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Tagihan (Opsional)</label>
                                    <select class="form-select" name="tagihan_id">
                                        <option value="">Pilih Tagihan</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-medium">Deskripsi</label>
                                    <textarea name="desc" class="form-control" rows="3">{{ $item->desc }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pb-4 px-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
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
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data transaksi...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama", last: "Terakhir",
                        next: "›", previous: "‹"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[0, 'asc']],
                columnDefs: [ { orderable: false, targets: -1 } ],
                dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>'
            });
            
            // Fix tooltip init for dynamic elements
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function confirmDelete(code) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data saldo " + code + " tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + code).submit();
                }
            });
        }
    </script>
@endsection