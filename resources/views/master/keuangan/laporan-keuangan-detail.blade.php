@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Detail Transaksi Keuangan</h3>
                    <p class="text-muted mb-0 small">Menampilkan daftar detail pencatatan masuk/keluar keuangan</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" onclick="exportDetail()" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                    <a href="{{ route($spref . 'keuangan.laporan-index', Request::except('page')) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $currentFilters['start_date'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $currentFilters['end_date'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun Akademik</label>
                        <select class="form-select" name="taka_id">
                            <option value="">Semua Tahun Akademik</option>
                            @foreach($tahunAkademiks as $taka)
                                <option value="{{ $taka->id }}" {{ $currentFilters['taka_id'] == $taka->id ? 'selected' : '' }}>
                                    {{ $taka->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Program Studi</label>
                        <select class="form-select" name="prodi_id">
                            <option value="">Semua Program Studi</option>
                            @foreach($programStudis as $prodi)
                                <option value="{{ $prodi->id }}" {{ $currentFilters['prodi_id'] == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="type">
                            <option value="Pemasukan" {{ $currentFilters['type'] == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="Pengeluaran" {{ $currentFilters['type'] == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="Sukses" {{ $currentFilters['status'] == 'Sukses' ? 'selected' : '' }}>Sukses / Lunas</option>
                            <option value="Pending" {{ $currentFilters['status'] == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Gagal" {{ $currentFilters['status'] == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Cari NIM, Nama, Kode Transaksi, Deskripsi...</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Masukkan kata kunci pencarian..." value="{{ $currentFilters['search'] }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route(Route::currentRouteName()) }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </a>
                    </div>
                </form>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>NIM</th>
                                <th>Mahasantri</th>
                                <th>Program Studi</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th>Metode / Sumber</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="text-nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <code class="text-dark">{{ $tx->code ?? $tx->transaction_code }}</code>
                                    </td>
                                    <td>{{ $tx->tagihan->mahasiswa->numb_nim ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $tx->tagihan->mahasiswa->name ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $tx->tagihan->mahasiswa->prodi->name ?? '-' }}</td>
                                    <td>
                                        <div class="text-wrap" style="max-width: 250px;">{{ $tx->desc }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tx->type == 'Pemasukan' ? 'bg-success-lt' : 'bg-danger-lt' }}">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td>{{ $tx->source ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <strong class="{{ $tx->type == 'Pemasukan' ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($tx->status == 'Sukses')
                                            <span class="badge bg-success">Sukses</span>
                                        @elseif($tx->status == 'Pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Gagal</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-1"></i> Tidak ada data transaksi yang ditemukan sesuai filter
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                @if($transactions->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between mt-3">
                        <p class="m-0 text-muted">
                            Menampilkan <span>{{ $transactions->firstItem() }}</span> hingga <span>{{ $transactions->lastItem() }}</span> dari <span>{{ $transactions->total() }}</span> entri
                        </p>
                        <div class="m-0">
                            {{ $transactions->links('custom.pagination-clean') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
function exportDetail() {
    const params = new URLSearchParams(window.location.search);
    params.set('export_format', 'detail');
    window.open('{{ route($spref . "keuangan.laporan-export") }}?' + params.toString(), '_blank');
}
</script>
@endsection
