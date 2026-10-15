@extends('core-themes.core-backpage')

@section('title')
    {{ $pages }} - {{ $academy }}
@endsection

@section('menu')
    {{ $menus }}
@endsection

@section('submenu')
    {{ $pages }}
@endsection

@section('subdesc')
    Halaman untuk mengelola tagihan individual mahasiswa
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Tagihan Individual
                    </h5>
                    <div class="btn-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Export Excel
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportData('summary')">
                                        <i class="fas fa-table me-2"></i>Ringkasan Tagihan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportData('detail')">
                                        <i class="fas fa-list-alt me-2"></i>Detail Tagihan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportData('by_prodi')">
                                        <i class="fas fa-graduation-cap me-2"></i>Per Program Studi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportData('by_status')">
                                        <i class="fas fa-chart-pie me-2"></i>Per Status (Lengkap)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportData('by_jenis')">
                                        <i class="fas fa-tags me-2"></i>Per Jenis Tagihan
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Tagihan Individual
                        </button>
                    </div>
                </div>
                
                <div class="collapse" id="collapseForm">
                    <div class="card-body border-top">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Tagihan Individual Baru</h5>
                            <form action="{{ route('keuangan.tagihan-individual-store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mahasiswa_id" class="form-label">Mahasiswa</label>
                                        <select class="form-select choices" name="mahasiswa_id" id="mahasiswa_id" required>
                                            <option value="">Pilih Mahasiswa</option>
                                            @foreach($allMahasiswas as $mahasiswa)
                                                <option value="{{ $mahasiswa->id }}">{{ $mahasiswa->numb_nim }} - {{ $mahasiswa->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('mahasiswa_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="taka_id" class="form-label">Tahun Akademik</label>
                                        <select class="form-select" name="taka_id" id="taka_id" required>
                                            <option value="">Pilih Tahun Akademik</option>
                                            @foreach($tahunAkademiks as $taka)
                                                <option value="{{ $taka->id }}">{{ $taka->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('taka_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="desc" class="form-label">Deskripsi Tagihan</label>
                                        <input type="text" class="form-control" name="desc" id="desc" placeholder="Contoh: SPP Semester 1, Biaya Sidang, dll" required>
                                        @error('desc')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="amount" class="form-label">Jumlah Tagihan</label>
                                        <input type="number" class="form-control" name="amount" id="amount" min="0" placeholder="0" required>
                                        @error('amount')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="due_date" class="form-label">Jatuh Tempo</label>
                                        <input type="date" class="form-control" name="due_date" id="due_date">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="no_due_date" name="no_due_date" value="1">
                                            <label class="form-check-label" for="no_due_date">
                                                Tidak ada tempo
                                            </label>
                                        </div>
                                        @error('due_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#collapseForm">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Simpan Tagihan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $totalTagihan }}</h3>
                                    <p class="mb-0">Total Tagihan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $pendingCount }}</h3>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $paidCount }}</h3>
                                    <p class="mb-0">Lunas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                                    <p class="mb-0">Total Pending</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-filter me-2"></i>Filter Data
                            </h6>
                            <form method="GET" action="{{ route('keuangan.tagihan-individual-index') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Cari Mahasiswa</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" id="search" 
                                                   placeholder="Cari nama atau NIM..." 
                                                   value="{{ $currentFilters['search'] ?? '' }}">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="status_filter" class="form-label">Status Tagihan</label>
                                        <select class="form-select" name="status_filter" id="status_filter">
                                            <option value="">Semua Status</option>
                                            <option value="has_tagihan" {{ ($currentFilters['status_filter'] ?? '') == 'has_tagihan' ? 'selected' : '' }}>Memiliki Tagihan</option>
                                            <option value="no_tagihan" {{ ($currentFilters['status_filter'] ?? '') == 'no_tagihan' ? 'selected' : '' }}>Belum Memiliki Tagihan</option>
                                            <option value="Pending" {{ ($currentFilters['status_filter'] ?? '') == 'Pending' ? 'selected' : '' }}>Ada Pending / Cicilan</option>
                                            <option value="Cicilan" {{ ($currentFilters['status_filter'] ?? '') == 'Cicilan' ? 'selected' : '' }}>Ada Cicilan</option>
                                            <option value="Paid" {{ ($currentFilters['status_filter'] ?? '') == 'Paid' ? 'selected' : '' }}>Semua Lunas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="prodi_filter" class="form-label">Program Studi</label>
                                        <select class="form-select" name="prodi_filter" id="prodi_filter">
                                            <option value="">Semua Prodi</option>
                                            @foreach($programStudis as $prodi)
                                                <option value="{{ $prodi->id }}" {{ ($currentFilters['prodi_filter'] ?? '') == $prodi->id ? 'selected' : '' }}>
                                                    {{ $prodi->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="kelas_filter" class="form-label">Kelas</label>
                                        <select class="form-select" name="kelas_filter" id="kelas_filter">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}" {{ ($currentFilters['kelas_filter'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                                    {{ $kelas->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="semester_filter" class="form-label">Semester</label>
                                        <select class="form-select" name="semester_filter" id="semester_filter">
                                            <option value="">Semua Semester</option>
                                            @foreach($semesterList as $semester)
                                                <option value="{{ $semester->id }}" {{ ($currentFilters['semester_filter'] ?? '') == $semester->id ? 'selected' : '' }}>
                                                    {{ $semester->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1 mb-3 d-flex align-items-end">
                                        <a href="{{ route('keuangan.tagihan-individual-index') }}" class="btn btn-secondary" title="Reset Filter">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Hidden inputs for sorting -->
                                <input type="hidden" name="sort_by" value="{{ $currentFilters['sort_by'] ?? 'name' }}">
                                <input type="hidden" name="sort_direction" value="{{ $currentFilters['sort_direction'] ?? 'asc' }}">
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="mahasiswaTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('name')" class="text-decoration-none text-dark">
                                            Mahasiswa
                                            @if(($currentFilters['sort_by'] ?? 'name') == 'name')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('numb_nim')" class="text-decoration-none text-dark">
                                            NIM
                                            @if(($currentFilters['sort_by'] ?? '') == 'numb_nim')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Prodi</th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('total_tagihan')" class="text-decoration-none text-dark">
                                            Total Tagihan
                                            @if(($currentFilters['sort_by'] ?? '') == 'total_tagihan')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('pending_tagihan')" class="text-decoration-none text-dark">
                                            Pending
                                            @if(($currentFilters['sort_by'] ?? '') == 'pending_tagihan')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('paid_tagihan')" class="text-decoration-none text-dark">
                                            Lunas
                                            @if(($currentFilters['sort_by'] ?? '') == 'paid_tagihan')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortTable('total_amount')" class="text-decoration-none text-dark">
                                            Total Pending
                                            @if(($currentFilters['sort_by'] ?? '') == 'total_amount')
                                                @if(($currentFilters['sort_direction'] ?? 'asc') == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Total Lunas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswas as $key => $mahasiswa)
                                    <tr data-prodi-id="{{ $mahasiswa->prodi_id ?? '' }}" 
                                        data-kelas-id="{{ $mahasiswa->kelas_id ?? '' }}" 
                                        data-semester-id="{{ $mahasiswa->semester ?? '' }}">
                                        <td>{{ $mahasiswas->firstItem() + $key }}</td>
                                        <td>{{ $mahasiswa->name }}</td>
                                        <td>{{ $mahasiswa->numb_nim }}</td>
                                        <td>{{ $mahasiswa->prodi->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $mahasiswa->total_tagihan }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $mahasiswa->pending_tagihan }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $mahasiswa->paid_tagihan }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-warning">Rp {{ number_format($mahasiswa->total_amount ?? 0, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">Rp {{ number_format($mahasiswa->paid_amount ?? 0, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#detailTagihan{{ $mahasiswa->id }}"
                                                        onclick="loadTagihanDetail({{ $mahasiswa->id }})"
                                                        title="Lihat Detail">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                                
                                                @if($mahasiswa->pending_tagihan > 0)
                                                <a href="{{ route('keuangan.pembayaran-manual-index') }}?mahasiswa_id={{ $mahasiswa->id }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Bayar Tagihan">
                                                    <i class="fas fa-credit-card"></i> Bayar
                                                </a>
                                                @endif
                                                
                                                <a href="{{ route('keuangan.tagihan-individual-mahasiswa', $mahasiswa->id) }}"
                                                   class="btn btn-sm btn-primary" title="Kelola Tagihan">
                                                    <i class="fas fa-edit"></i> Kelola
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Detail Row (Collapsible) -->
                                    <tr class="collapse" id="detailTagihan{{ $mahasiswa->id }}">
                                        <td colspan="10">
                                            <div class="card card-body bg-secondary bg-opacity-10 border-secondary">
                                                <h6 class="mb-3">Detail Tagihan - {{ $mahasiswa->name }}</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah</th>
                                        <th>Sisa Tagihan</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Status</th>
                                        <th>Tahun Akademik</th>
                                    </tr>
                                                        </thead>
                                                        <tbody id="tagihanDetail{{ $mahasiswa->id }}">
                                                            <tr>
                                                                <td colspan="7" class="text-center py-3">
                                                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                                    Memuat detail tagihan...
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <div class="pagination-wrapper">
                            {{ $mahasiswas->links('custom.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>
                        Pembayaran Manual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Student Info -->
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nama:</strong> <span id="modalStudentName">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>NIM:</strong> <span id="modalStudentNim">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bills Selection -->
                    <div class="mb-3">
                        <h6>Pilih Tagihan yang akan dibayar:</h6>
                        <div id="billsList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Memuat tagihan...
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Total terpilih: <span id="selectedTotal" class="fw-bold text-primary">Rp 0</span>
                            </small>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="modalPaymentForm">
                        <input type="hidden" id="modalMahasiswaId" name="mahasiswa_id">
                        <input type="hidden" id="modalSelectedTagihan" name="tagihan_ids">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Metode Pembayaran</label>
                                <select name="payment_method_id" class="form-select" required>
                                    <option value="">Pilih Metode...</option>
                                    @if(isset($paymentMethods))
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rekening Bank</label>
                                <select name="bank_account_id" class="form-select">
                                    <option value="">Pilih Rekening (Opsional)...</option>
                                    @if(isset($bankAccounts))
                                        @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->bank_name }} - {{ $account->account_number }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Jumlah Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="paid_amount" id="modalPaidAmount" class="form-control" 
                                           placeholder="0" min="1" step="1" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Tanggal Pembayaran</label>
                                <input type="date" name="payment_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Referensi Pembayaran</label>
                                <input type="text" name="payment_reference" class="form-control" 
                                       placeholder="Nomor referensi, kode transfer, dll">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bukti Pembayaran</label>
                                <input type="file" name="payment_proof" class="form-control" 
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-hint">Format: JPG, PNG, PDF. Maksimal 5MB</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="processPaymentBtn">
                        <i class="fas fa-credit-card me-1"></i>
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal modal-blur fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-success icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                    <h3>Pembayaran Berhasil!</h3>
                    <div class="text-muted" id="successMessage">Pembayaran telah berhasil diproses.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        /* Sortable Table Headers */
        .table th a {
            color: inherit !important;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0;
        }

        .table th a:hover {
            color: var(--tblr-primary) !important;
        }

        .table th a i {
            margin-left: 8px;
            font-size: 12px;
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .table th a:hover i {
            opacity: 1;
            color: var(--tblr-primary) !important;
        }

        .table th a i.fa-sort-up,
        .table th a i.fa-sort-down {
            opacity: 1;
            color: var(--tblr-primary) !important;
        }

        /* Dark mode sort icons */
        [data-bs-theme="dark"] .table th a {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table th a:hover {
            color: var(--tblr-primary) !important;
        }

        [data-bs-theme="dark"] .table th a i {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .table th a:hover i {
            color: var(--tblr-primary) !important;
        }

        [data-bs-theme="dark"] .table th a i.fa-sort-up,
        [data-bs-theme="dark"] .table th a i.fa-sort-down {
            color: var(--tblr-primary) !important;
        }

        /* Fix badge text colors */
        .badge {
            color: white !important;
        }

        .badge.bg-primary {
            color: white !important;
        }

        .badge.bg-warning {
            color: white !important;
        }

        .badge.bg-success {
            color: white !important;
        }

        .badge.bg-danger {
            color: white !important;
        }

        .badge.bg-info {
            color: white !important;
        }

        .badge.bg-secondary {
            color: white !important;
        }

        /* Dark mode badge fixes */
        [data-bs-theme="dark"] .badge {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-primary {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-warning {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-success {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-danger {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-info {
            color: white !important;
        }

        [data-bs-theme="dark"] .badge.bg-secondary {
            color: white !important;
        }

        /* Enhanced Pagination Styling */
        .pagination-wrapper {
            padding: 20px;
            border-radius: 8px;
            background: var(--tblr-bg-surface);
            border: 1px solid var(--tblr-border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .pagination-wrapper .pagination {
            margin: 0;
            gap: 8px;
        }

        .pagination-wrapper .page-link {
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 6px;
            border: 1px solid var(--tblr-border-color);
            color: var(--tblr-body-color);
            background-color: var(--tblr-bg-surface);
            text-decoration: none;
            transition: all 0.2s ease;
            min-width: 40px;
            text-align: center;
        }

        .pagination-wrapper .page-link:hover {
            background-color: var(--tblr-primary);
            color: white;
            border-color: var(--tblr-primary);
            transform: translateY(-1px);
        }

        .pagination-wrapper .page-item.active .page-link {
            background-color: var(--tblr-primary);
            border-color: var(--tblr-primary);
            color: white;
            font-weight: 600;
        }

        .pagination-wrapper .page-item.disabled .page-link {
            color: var(--tblr-secondary);
            background-color: var(--tblr-bg-surface-secondary);
            border-color: var(--tblr-border-color-light);
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Dark Mode Specific Styles */
        [data-bs-theme="dark"] .pagination-wrapper,
        .dark .pagination-wrapper,
        body[data-theme="dark"] .pagination-wrapper {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 1px 3px rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="dark"] .pagination-wrapper .page-link,
        .dark .pagination-wrapper .page-link,
        body[data-theme="dark"] .pagination-wrapper .page-link {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .pagination-wrapper .page-link:hover,
        .dark .pagination-wrapper .page-link:hover,
        body[data-theme="dark"] .pagination-wrapper .page-link:hover {
            background-color: var(--tblr-primary) !important;
            color: white !important;
            border-color: var(--tblr-primary) !important;
        }

        [data-bs-theme="dark"] .pagination-wrapper .page-item.active .page-link,
        .dark .pagination-wrapper .page-item.active .page-link,
        body[data-theme="dark"] .pagination-wrapper .page-item.active .page-link {
            background-color: var(--tblr-primary) !important;
            border-color: var(--tblr-primary) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .pagination-wrapper .page-item.disabled .page-link,
        .dark .pagination-wrapper .page-item.disabled .page-link,
        body[data-theme="dark"] .pagination-wrapper .page-item.disabled .page-link {
            background-color: #0f172a !important;
            color: #64748b !important;
            border-color: #334155 !important;
        }

        /* Additional dark mode fixes for any remaining white backgrounds */
        [data-bs-theme="dark"] .pagination .page-item .page-link,
        .dark .pagination .page-item .page-link,
        body[data-theme="dark"] .pagination .page-item .page-link {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        /* Ultra-aggressive dark mode fixes - target all possible pagination elements */
        [data-bs-theme="dark"] .pagination *,
        .dark .pagination *,
        body[data-theme="dark"] .pagination * {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .pagination .page-item:not(.active):not(.disabled) .page-link,
        .dark .pagination .page-item:not(.active):not(.disabled) .page-link,
        body[data-theme="dark"] .pagination .page-item:not(.active):not(.disabled) .page-link {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        /* Force dark background on all pagination elements */
        html[data-bs-theme="dark"] .pagination-wrapper,
        html[data-bs-theme="dark"] .pagination,
        html[data-bs-theme="dark"] .page-item,
        html[data-bs-theme="dark"] .page-link {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        /* Override Bootstrap's default pagination styles completely in dark mode */
        [data-bs-theme="dark"] .pagination .page-link {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .pagination .page-link:focus,
        [data-bs-theme="dark"] .pagination .page-link:hover {
            background-color: var(--tblr-primary) !important;
            border-color: var(--tblr-primary) !important;
            color: white !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pagination-wrapper {
                padding: 15px;
            }
            
            .pagination-wrapper .page-link {
                padding: 6px 10px;
                font-size: 14px;
                min-width: 35px;
            }
        }

        /* Additional spacing for pagination info */
        .pagination-info {
            color: var(--tblr-secondary);
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        [data-bs-theme="dark"] .pagination-info {
            color: var(--tblr-secondary);
        }

        /* Final nuclear option - force all elements to be dark */
        [data-bs-theme="dark"] .pagination-wrapper *,
        [data-bs-theme="dark"] .pagination *,
        .dark .pagination-wrapper *,
        .dark .pagination *,
        body[data-theme="dark"] .pagination-wrapper *,
        body[data-theme="dark"] .pagination * {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        /* Override active and hover states */
        [data-bs-theme="dark"] .pagination .page-item.active *,
        .dark .pagination .page-item.active *,
        body[data-theme="dark"] .pagination .page-item.active * {
            background-color: var(--tblr-primary) !important;
            color: white !important;
            border-color: var(--tblr-primary) !important;
        }

        [data-bs-theme="dark"] .pagination .page-link:hover,
        [data-bs-theme="dark"] .pagination .page-link:focus,
        .dark .pagination .page-link:hover,
        .dark .pagination .page-link:focus,
        body[data-theme="dark"] .pagination .page-link:hover,
        body[data-theme="dark"] .pagination .page-link:focus {
            background-color: var(--tblr-primary) !important;
            color: white !important;
            border-color: var(--tblr-primary) !important;
        }

        /* Modal Overlay Fixes */
        .modal {
            z-index: 9999 !important;
        }
        
        .modal-backdrop {
            z-index: 9998 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        .modal-dialog {
            z-index: 10000 !important;
        }
        
        /* Ensure modal appears above everything */
        #paymentModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999 !important;
        }
        
        #paymentModal .modal-dialog {
            position: relative !important;
            z-index: 10000 !important;
        }
        
        /* Success modal should also be on top */
        #successModal {
            z-index: 10001 !important;
        }
        
        #successModal .modal-dialog {
            z-index: 10002 !important;
        }

        /* Choices.js Dark Mode Support */
        [data-bs-theme="dark"] .choices__inner,
        .dark .choices__inner,
        body[data-theme="dark"] .choices__inner {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .choices__list--dropdown,
        .dark .choices__list--dropdown,
        body[data-theme="dark"] .choices__list--dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            z-index: 1000 !important;
        }

        [data-bs-theme="dark"] .choices[data-type*=select-one] .choices__input,
        .dark .choices[data-type*=select-one] .choices__input,
        body[data-theme="dark"] .choices[data-type*=select-one] .choices__input {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .choices__list--dropdown .choices__item--choice,
        .dark .choices__list--dropdown .choices__item--choice,
        body[data-theme="dark"] .choices__list--dropdown .choices__item--choice {
            color: #f8fafc !important;
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted,
        .dark .choices__list--dropdown .choices__item--selectable.is-highlighted,
        body[data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: var(--tblr-primary, #206bc4) !important;
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .choices__list--dropdown .choices__item,
        .dark .choices__list--dropdown .choices__item,
        body[data-theme="dark"] .choices__list--dropdown .choices__item {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .choices__placeholder,
        .dark .choices__placeholder,
        body[data-theme="dark"] .choices__placeholder {
            color: #94a3b8 !important;
            opacity: 0.8 !important;
        }

        [data-bs-theme="dark"] .choices__list--single,
        .dark .choices__list--single,
        body[data-theme="dark"] .choices__list--single {
            color: #f8fafc !important;
        }
        
        [data-bs-theme="dark"] .choices[data-type*=select-one]::after,
        .dark .choices[data-type*=select-one]::after,
        body[data-theme="dark"] .choices[data-type*=select-one]::after {
            border-color: #e2e8f0 transparent transparent transparent !important;
        }

        [data-bs-theme="dark"] .choices[data-type*=select-one].is-open::after,
        .dark .choices[data-type*=select-one].is-open::after,
        body[data-theme="dark"] .choices[data-type*=select-one].is-open::after {
            border-color: transparent transparent #e2e8f0 transparent !important;
        }
    </style>
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="{{ asset('dist') }}/assets/static/js/pages/form-element-select.js"></script>
    <script>
        let searchTimeout;
        
        // Auto-submit form when filters change (except search)
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelects = ['status_filter', 'prodi_filter', 'kelas_filter', 'semester_filter'];
            
            filterSelects.forEach(function(selectId) {
                const element = document.getElementById(selectId);
                if (element) {
                    element.addEventListener('change', function() {
                        document.getElementById('filterForm').submit();
                    });
                }
            });

            // Search functionality - ONLY server-side, no real-time filtering to avoid detail content issues
            const searchInput = document.getElementById('search');
            if (searchInput) {
                // Debounced server-side search only
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    
                    const searchValue = this.value.trim();
                    if (searchValue.length >= 2) {
                        searchTimeout = setTimeout(function() {
                            document.getElementById('filterForm').submit();
                        }, 1500); // Wait 1.5 seconds after user stops typing
                    } else if (searchValue.length === 0) {
                        // If search is cleared, submit immediately to show all results
                        document.getElementById('filterForm').submit();
                    }
                });

                // Allow Enter key to submit search immediately
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        document.getElementById('filterForm').submit();
                    }
                });
            }

            // Only apply real-time filtering for non-search filters (status, prodi, kelas, semester) (DISABLED: Handled server-side)
            // filterNonSearchFilters();
            
            // Handle "Tidak ada tempo" checkbox
            const noDueDateCheckbox = document.getElementById('no_due_date');
            const dueDateInput = document.getElementById('due_date');
            
            if (noDueDateCheckbox && dueDateInput) {
                noDueDateCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        dueDateInput.disabled = true;
                        dueDateInput.value = '';
                        dueDateInput.removeAttribute('required');
                    } else {
                        dueDateInput.disabled = false;
                    }
                });
                
                // Also handle when due_date is changed
                dueDateInput.addEventListener('change', function() {
                    if (this.value) {
                        noDueDateCheckbox.checked = false;
                    }
                });
            }
        });

        // Filter function for non-search filters only (status, prodi, kelas, semester)
        function filterNonSearchFilters() {
            const statusValue = document.getElementById('status_filter').value;
            const prodiValue = document.getElementById('prodi_filter').value;
            const kelasValue = document.getElementById('kelas_filter').value;
            const semesterValue = document.getElementById('semester_filter').value;
            
            const table = document.getElementById('mahasiswaTable');
            if (!table) return;
            
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                
                // Skip detail rows (collapsible rows)
                if (row.classList.contains('collapse')) {
                    continue;
                }
                
                // Get cell values safely
                const cells = row.cells;
                if (cells.length < 6) continue;
                
                const prodi = cells[3] ? cells[3].textContent.toLowerCase().trim() : '';
                
                // Get pending count safely
                let pendingCount = 0;
                const pendingBadge = cells[5] ? cells[5].querySelector('.badge') : null;
                if (pendingBadge) {
                    pendingCount = parseInt(pendingBadge.textContent.trim()) || 0;
                }
                
                // Check status match
                let matchesStatus = true;
                if (statusValue === 'Pending') {
                    matchesStatus = pendingCount > 0;
                } else if (statusValue === 'Cicilan') {
                    const pendingBadgeText = pendingBadge ? pendingBadge.textContent.trim() : '0';
                    matchesStatus = parseInt(pendingBadgeText) > 0;
                } else if (statusValue === 'Paid') {
                    matchesStatus = pendingCount === 0;
                }

                // Check prodi match
                let matchesProdi = true;
                if (prodiValue !== '') {
                    const prodiSelect = document.getElementById('prodi_filter');
                    if (prodiSelect && prodiSelect.selectedIndex > 0) {
                        const selectedProdiOption = prodiSelect.options[prodiSelect.selectedIndex];
                        const selectedProdiText = selectedProdiOption.text.toLowerCase();
                        matchesProdi = prodi.includes(selectedProdiText);
                    }
                }

                // Check kelas match using data attributes
                let matchesKelas = true;
                if (kelasValue !== '') {
                    const kelasId = row.getAttribute('data-kelas-id');
                    matchesKelas = kelasId === kelasValue;
                }

                // Check semester match using data attributes
                let matchesSemester = true;
                if (semesterValue !== '') {
                    const semesterId = row.getAttribute('data-semester-id');
                    matchesSemester = semesterId === semesterValue;
                }

                // Show/hide row based on non-search filters only
                if (matchesStatus && matchesProdi && matchesKelas && matchesSemester) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Always show the corresponding detail row
                    const nextRow = row.nextElementSibling;
                    if (nextRow && nextRow.classList.contains('collapse')) {
                        nextRow.style.display = '';
                    }
                } else {
                    row.style.display = 'none';
                    // Hide the corresponding detail row when parent is hidden
                    const nextRow = row.nextElementSibling;
                    if (nextRow && nextRow.classList.contains('collapse')) {
                        nextRow.style.display = 'none';
                    }
                }
            }
        }

        // Real-time table filtering function (DISABLED to prevent detail content filtering)
        function filterTableRealtime() {
            const searchValue = document.getElementById('search').value.toLowerCase().trim();
            const statusValue = document.getElementById('status_filter').value;
            const prodiValue = document.getElementById('prodi_filter').value;
            const kelasValue = document.getElementById('kelas_filter').value;
            const semesterValue = document.getElementById('semester_filter').value;
            
            const table = document.getElementById('mahasiswaTable');
            if (!table) return;
            
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                
                // Skip detail rows (collapsible rows) - don't filter these, they should always follow their parent row
                if (row.classList.contains('collapse')) {
                    continue;
                }
                
                // Get cell values safely
                const cells = row.cells;
                if (cells.length < 6) continue;
                
                const mahasiswa = cells[1] ? cells[1].textContent.toLowerCase().trim() : '';
                const nim = cells[2] ? cells[2].textContent.toLowerCase().trim() : '';
                const prodi = cells[3] ? cells[3].textContent.toLowerCase().trim() : '';
                
                // Get pending count safely
                let pendingCount = 0;
                const pendingBadge = cells[5] ? cells[5].querySelector('.badge') : null;
                if (pendingBadge) {
                    pendingCount = parseInt(pendingBadge.textContent.trim()) || 0;
                }
                
                // Check search match
                let matchesSearch = true;
                if (searchValue !== '') {
                    matchesSearch = mahasiswa.includes(searchValue) || nim.includes(searchValue);
                }
                
                // Check status match
                let matchesStatus = true;
                if (statusValue === 'Pending') {
                    matchesStatus = pendingCount > 0;
                } else if (statusValue === 'Paid') {
                    matchesStatus = pendingCount === 0;
                }

                // Check prodi match
                let matchesProdi = true;
                if (prodiValue !== '') {
                    const prodiSelect = document.getElementById('prodi_filter');
                    if (prodiSelect && prodiSelect.selectedIndex > 0) {
                        const selectedProdiOption = prodiSelect.options[prodiSelect.selectedIndex];
                        const selectedProdiText = selectedProdiOption.text.toLowerCase();
                        matchesProdi = prodi.includes(selectedProdiText);
                    }
                }

                // Check kelas match using data attributes
                let matchesKelas = true;
                if (kelasValue !== '') {
                    const kelasId = row.getAttribute('data-kelas-id');
                    matchesKelas = kelasId === kelasValue;
                }

                // Check semester match using data attributes
                let matchesSemester = true;
                if (semesterValue !== '') {
                    const semesterId = row.getAttribute('data-semester-id');
                    matchesSemester = semesterId === semesterValue;
                }

                // Show/hide row based on all filters
                if (matchesSearch && matchesStatus && matchesProdi && matchesKelas && matchesSemester) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Always show the corresponding detail row if it exists (don't filter detail content)
                    const nextRow = row.nextElementSibling;
                    if (nextRow && nextRow.classList.contains('collapse')) {
                        // Don't hide detail rows - let them show their full content regardless of search
                        nextRow.style.display = '';
                    }
                } else {
                    row.style.display = 'none';
                    // Hide the corresponding detail row when parent is hidden
                    const nextRow = row.nextElementSibling;
                    if (nextRow && nextRow.classList.contains('collapse')) {
                        nextRow.style.display = 'none';
                    }
                }
            }

            // Show message if no results found
            showNoResultsMessage(visibleCount === 0 && searchValue !== '');
        }

        // Show/hide no results message
        function showNoResultsMessage(show) {
            let noResultsRow = document.getElementById('noResultsRow');
            
            if (show) {
                if (!noResultsRow) {
                    const table = document.getElementById('mahasiswaTable');
                    const tbody = table.getElementsByTagName('tbody')[0];
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = '<td colspan="10" class="text-center py-4"><i class="fas fa-search me-2"></i>Tidak ada hasil yang ditemukan</td>';
                    tbody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else {
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        }

        // Sorting function
        function sortTable(column) {
            const form = document.getElementById('filterForm');
            const currentSortBy = form.querySelector('input[name="sort_by"]').value;
            const currentSortDirection = form.querySelector('input[name="sort_direction"]').value;
            
            // Determine new sort direction
            let newDirection = 'asc';
            if (currentSortBy === column && currentSortDirection === 'asc') {
                newDirection = 'desc';
            }
            
            // Update hidden inputs
            form.querySelector('input[name="sort_by"]').value = column;
            form.querySelector('input[name="sort_direction"]').value = newDirection;
            
            // Submit form
            form.submit();
        }

        // Show loading state when form is submitted
        document.getElementById('filterForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;
            }
        });

        // Payment Modal Functions
        let selectedBills = [];
        let currentMahasiswaId = null;

        function openPaymentModal(mahasiswaId, name, nim) {
            currentMahasiswaId = mahasiswaId;
            
            // Set student info
            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentNim').textContent = nim;
            document.getElementById('modalMahasiswaId').value = mahasiswaId;
            
            // Reset form
            document.getElementById('modalPaymentForm').reset();
            document.getElementById('modalMahasiswaId').value = mahasiswaId;
            selectedBills = [];
            updateSelectedTotal();
            
            // Load bills
            loadStudentBills(mahasiswaId);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        function loadStudentBills(mahasiswaId) {
            const billsList = document.getElementById('billsList');
            billsList.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat tagihan...</div>';
            
            fetch(`/keuangan/pembayaran-manual/ajax/mahasiswa/${mahasiswaId}/tagihan`)
                .then(response => response.json())
                .then(data => {
                    if (data.tagihans && data.tagihans.length > 0) {
                        let html = '';
                        data.tagihans.forEach(tagihan => {
                            const isOverdue = tagihan.is_overdue ? 'text-danger' : '';
                            html += `
                                <div class="form-check mb-2 p-3 border rounded ${isOverdue}">
                                    <input class="form-check-input" type="checkbox" value="${tagihan.id}" 
                                           id="bill_${tagihan.id}" onchange="toggleBill(${tagihan.id}, ${tagihan.amount})">
                                    <label class="form-check-label w-100" for="bill_${tagihan.id}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>${tagihan.name}</strong>
                                                <br>
                                                <small class="text-muted">${tagihan.description}</small>
                                                <br>
                                                <small class="text-muted">Jatuh tempo: ${tagihan.due_date}</small>
                                                ${tagihan.is_overdue ? '<br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Terlambat</small>' : ''}
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-primary">${tagihan.formatted_amount}</strong>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            `;
                        });
                        billsList.innerHTML = html;
                    } else {
                        billsList.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-info-circle me-2"></i>Tidak ada tagihan pending untuk mahasiswa ini.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading bills:', error);
                    billsList.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat tagihan. Silakan coba lagi.</div>';
                });
        }

        function toggleBill(billId, amount) {
            const checkbox = document.getElementById(`bill_${billId}`);
            if (checkbox.checked) {
                selectedBills.push({ id: billId, amount: amount });
            } else {
                selectedBills = selectedBills.filter(bill => bill.id !== billId);
            }
            updateSelectedTotal();
        }

        function updateSelectedTotal() {
            const total = selectedBills.reduce((sum, bill) => sum + bill.amount, 0);
            document.getElementById('selectedTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('modalPaidAmount').value = total;
            
            // Update hidden input
            const billIds = selectedBills.map(bill => bill.id);
            document.getElementById('modalSelectedTagihan').value = JSON.stringify(billIds);
        }

        // Process payment
        document.getElementById('processPaymentBtn').addEventListener('click', function() {
            if (selectedBills.length === 0) {
                alert('Silakan pilih tagihan yang akan dibayar.');
                return;
            }

            const form = document.getElementById('modalPaymentForm');
            const formData = new FormData(form);
            
            // Add selected bills
            formData.set('tagihan_ids', selectedBills.map(bill => bill.id));
            
            // Show loading
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
            btn.disabled = true;

            fetch('/keuangan/pembayaran-manual/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide payment modal
                    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    paymentModal.hide();
                    
                    // Show success modal
                    document.getElementById('successMessage').textContent = data.message;
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Reload page after success modal is closed
                    successModal._element.addEventListener('hidden.bs.modal', function() {
                        location.reload();
                    });
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan saat memproses pembayaran.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
            })
            .finally(() => {
                // Reset button
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // Load tagihan detail via AJAX
        function loadTagihanDetail(mahasiswaId) {
            const detailContainer = document.getElementById(`tagihanDetail${mahasiswaId}`);
            
            // Only load if not already loaded
            if (detailContainer.innerHTML.includes('Memuat detail tagihan...')) {
                fetch(`/keuangan/tagihan-individual/ajax/mahasiswa/${mahasiswaId}/tagihan`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.tagihans) {
                            let html = '';
                            if (data.tagihans.length > 0) {
                                data.tagihans.forEach(tagihan => {
                                    let statusBadge = '';
                                    if (tagihan.status === 'Pending') {
                                        statusBadge = '<span class="badge bg-warning">Pending</span>';
                                    } else if (tagihan.status === 'Cicilan') {
                                        statusBadge = '<span class="badge bg-danger">Cicilan</span>';
                                    } else if (tagihan.status === 'Paid' || tagihan.status === 'Sukses') {
                                        statusBadge = '<span class="badge bg-success">Lunas</span>';
                                    } else {
                                        statusBadge = `<span class="badge bg-danger">${tagihan.status_label}</span>`;
                                    }

                                    const amount = parseFloat(tagihan.amount || 0);
                                    const paidAmount = parseFloat(tagihan.paid_amount || 0);
                                    const remaining = tagihan.remaining_amount !== null
                                        ? parseFloat(tagihan.remaining_amount || 0)
                                        : Math.max(amount - paidAmount, 0);
                                    const remainingFormatted = 'Rp ' + remaining.toLocaleString('id-ID');
                                    
                                    html += `
                                        <tr>
                                            <td><span class="badge bg-primary text-white">${tagihan.code}</span></td>
                                            <td>${tagihan.desc}</td>
                                            <td>${tagihan.formatted_amount}</td>
                                            <td><strong class="${remaining > 0 ? 'text-danger' : 'text-success'}">${remainingFormatted}</strong></td>
                                            <td>${tagihan.due_date}</td>
                                            <td>${statusBadge}</td>
                                            <td>${tagihan.tahun_akademik}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html = `
                                    <tr>
                                    <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Mahasiswa ini belum memiliki tagihan
                                            </div>
                                            <small class="text-muted">
                                                Silakan tambah tagihan baru menggunakan form di atas atau melalui menu Kelola
                                            </small>
                                        </td>
                                    </tr>
                                `;
                            }
                            detailContainer.innerHTML = html;
                        } else {
                            detailContainer.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Gagal memuat detail tagihan: ${data.message || 'Terjadi kesalahan'}
                                    </td>
                                </tr>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading tagihan detail:', error);
                        detailContainer.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-4 text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Gagal memuat detail tagihan. Silakan coba lagi.
                                </td>
                            </tr>
                        `;
                    });
            }
        }

        // Export function
        function exportData(type) {
            // Get current filters from the form
            const params = new URLSearchParams();
            
            // Add current filter values
            const search = document.getElementById('search').value;
            const statusFilter = document.getElementById('status_filter').value;
            const prodiFilter = document.getElementById('prodi_filter').value;
            const kelasFilter = document.getElementById('kelas_filter').value;
            const semesterFilter = document.getElementById('semester_filter').value;
            
            if (search) params.set('search', search);
            if (statusFilter) params.set('status_filter', statusFilter);
            if (prodiFilter) params.set('prodi_filter', prodiFilter);
            if (kelasFilter) params.set('kelas_filter', kelasFilter);
            if (semesterFilter) params.set('semester_filter', semesterFilter);
            
            // Add export type
            params.set('type', type);
            
            // Create export URL
            const exportUrl = '{{ route("keuangan.tagihan-individual-export") }}?' + params.toString();
            
            // Open in new window/tab
            window.open(exportUrl, '_blank');
        }
    </script>
@endsection
