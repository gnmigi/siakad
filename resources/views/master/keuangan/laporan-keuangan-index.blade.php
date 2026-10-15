@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Keuangan</h3>
                <div class="card-actions">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="exportData('summary')">
                                <i class="fas fa-file-excel me-1"></i> Export Summary (Excel)
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportData('detail')">
                                <i class="fas fa-file-excel me-1"></i> Export Detail (Excel)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $currentFilters['start_date'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $currentFilters['end_date'] }}">
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route($spref . 'keuangan.laporan-index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-1"></i> Reset
                        </a>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-white-50">Total Pemasukan</div>
                                    <div class="ms-auto">
                                        <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="h1 mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-white-50">Total Transaksi</div>
                                    <div class="ms-auto">
                                        <i class="fas fa-receipt fa-2x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="h1 mb-0">{{ number_format($totalTransaksi) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-white-50">Rata-rata Transaksi</div>
                                    <div class="ms-auto">
                                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="h1 mb-0">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-white-50">Tagihan Pending</div>
                                    <div class="ms-auto">
                                        <i class="fas fa-clock fa-2x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="h1 mb-0">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
                                <div class="text-white-50 small">{{ $jumlahTagihanPending }} tagihan</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Trend Pembayaran Harian</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="dailyPaymentChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Metode Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentMethodChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Students Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Top 10 Mahasiswa Pembayar</h4>
                                <div class="card-actions">
                                    <a href="{{ route($spref . 'keuangan.laporan-detail', $currentFilters) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Total Pembayaran</th>
                                                <th>Jumlah Transaksi</th>
                                                <th>Rata-rata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topStudents as $index => $student)
                                                <tr>
                                                    <td>
                                                        @if($index == 0)
                                                            <span class="badge bg-warning text-dark">
                                                                #{{ $index + 1 }}
                                                            </span>
                                                        @elseif($index == 1)
                                                            <span class="badge bg-secondary text-white">
                                                                #{{ $index + 1 }}
                                                            </span>
                                                        @elseif($index == 2)
                                                            <span class="badge bg-orange text-white">
                                                                #{{ $index + 1 }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-primary text-white">
                                                                #{{ $index + 1 }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $student->numb_nim }}</td>
                                                    <td>{{ $student->name }}</td>
                                                    <td>
                                                        <strong class="text-success">
                                                            Rp {{ number_format($student->total_paid, 0, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                    <td>{{ $student->payment_count }} transaksi</td>
                                                    <td>Rp {{ $student->payment_count > 0 ? number_format($student->total_paid / $student->payment_count, 0, ',', '.') : '0' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        Tidak ada data pembayaran dalam periode ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Payment Chart
    const dailyCtx = document.getElementById('dailyPaymentChart').getContext('2d');
    const dailyData = @json($chartData);
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => item.date),
            datasets: [{
                label: 'Total Pembayaran',
                data: dailyData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Jumlah Transaksi',
                data: dailyData.map(item => item.count),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'y1',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Total Pembayaran (Rp)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            } else {
                                return 'Transaksi: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        }
    });

    // Payment Method Chart
    const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const methodData = @json($paymentMethods);
    
    new Chart(methodCtx, {
        type: 'doughnut',
        data: {
            labels: methodData.map(item => item.name),
            datasets: [{
                data: methodData.map(item => item.total),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                        }
                    }
                }
            }
        }
    });
});

function exportData(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export_format', format);
    
    window.open('{{ route($spref . "keuangan.laporan-export") }}?' + params.toString(), '_blank');
}
</script>
@endsection
