@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Analisis Pembayaran</h3>
                <div class="card-actions">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="exportAnalysis('summary')">
                                <i class="fas fa-file-excel me-1"></i> Export Summary
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportAnalysis('trends')">
                                <i class="fas fa-chart-line me-1"></i> Export Trends
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportAnalysis('behavior')">
                                <i class="fas fa-users me-1"></i> Export Behavior
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trendsModal" id="btnTrendAnalysis">
                        <i class="fas fa-chart-line me-1"></i> Trend Analysis
                    </button>
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
                        <a href="{{ route($spref . 'keuangan.analisis-index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-1"></i> Reset
                        </a>
                    </div>
                </form>

                <!-- Monthly Trend Analysis -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Trend Pembayaran Bulanan</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyTrendChart" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Performance -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Performa Metode Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Metode Pembayaran</th>
                                                <th>Total Amount</th>
                                                <th>Jumlah Transaksi</th>
                                                <th>Rata-rata</th>
                                                <th>Periode Aktif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentMethodAnalysis as $method)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $method->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="text-success fw-bold">
                                                            Rp {{ number_format($method->total_amount, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($method->transaction_count) }}</td>
                                                    <td>Rp {{ number_format($method->avg_amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($method->first_payment)->format('d/m/Y') }} - 
                                                            {{ \Carbon\Carbon::parse($method->last_payment)->format('d/m/Y') }}
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Success Rate Analysis</h4>
                            </div>
                            <div class="card-body">
                                @foreach($successRateAnalysis as $rate)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small">{{ $rate->payment_method }}</span>
                                            <span class="small fw-bold">{{ $rate->success_rate }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $rate->success_rate >= 90 ? 'success' : ($rate->success_rate >= 70 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $rate->success_rate }}%"></div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $rate->successful_payments }}/{{ $rate->total_attempts }} transaksi
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Behavior Analysis -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Analisis Perilaku Pembayaran Mahasiswa</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Program Studi</th>
                                                <th>Total Pembayaran</th>
                                                <th>Frekuensi</th>
                                                <th>Rata-rata</th>
                                                <th>Rentang Waktu</th>
                                                <th>Behavior Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($studentBehavior as $student)
                                                @php
                                                    $behaviorScore = min(100, ($student->payment_frequency * 10) + ($student->payment_span_days > 0 ? (365 / max(1, $student->payment_span_days)) * 20 : 0));
                                                @endphp
                                                <tr>
                                                    <td>{{ $student->numb_nim }}</td>
                                                    <td>{{ $student->name }}</td>
                                                    <td>{{ $student->prodi_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="text-success fw-bold">
                                                            Rp {{ number_format($student->total_paid, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $student->payment_frequency }}x</td>
                                                    <td>Rp {{ number_format($student->avg_payment, 0, ',', '.') }}</td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $student->payment_span_days }} hari
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $behaviorScore >= 80 ? 'success' : ($behaviorScore >= 60 ? 'warning' : 'danger') }}">
                                                            {{ number_format($behaviorScore, 1) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outstanding Bills Analysis -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Analisis Tagihan Tertunggak per Prodi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Program Studi</th>
                                                <th>Total Tagihan</th>
                                                <th>Overdue</th>
                                                <th>Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($outstandingAnalysis as $outstanding)
                                                @php
                                                    $overduePercentage = $outstanding->total_bills > 0 ? ($outstanding->overdue_count / $outstanding->total_bills) * 100 : 0;
                                                @endphp
                                                <tr>
                                                    <td>{{ $outstanding->prodi_name ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $outstanding->total_bills }} tagihan<br>
                                                        <small class="text-muted">
                                                            Rp {{ number_format($outstanding->total_amount, 0, ',', '.') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        {{ $outstanding->overdue_count }} tagihan<br>
                                                        <small class="text-danger">
                                                            Rp {{ number_format($outstanding->overdue_amount, 0, ',', '.') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $overduePercentage <= 10 ? 'success' : ($overduePercentage <= 25 ? 'warning' : 'danger') }}">
                                                            {{ number_format($overduePercentage, 1) }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Peak Payment Times</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="peakTimesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collection Efficiency -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Efisiensi Penagihan per Program Studi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Program Studi</th>
                                                <th>Total Mahasiswa</th>
                                                <th>Mahasiswa Bayar</th>
                                                <th>Collection Rate</th>
                                                <th>Amount Collected</th>
                                                <th>Amount Collection Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($collectionEfficiency as $efficiency)
                                                <tr>
                                                    <td>{{ $efficiency->prodi_name ?? 'N/A' }}</td>
                                                    <td>{{ $efficiency->total_students }}</td>
                                                    <td>{{ $efficiency->paying_students }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2">{{ $efficiency->collection_rate }}%</span>
                                                            <div class="progress flex-fill" style="height: 8px;">
                                                                <div class="progress-bar bg-{{ $efficiency->collection_rate >= 80 ? 'success' : ($efficiency->collection_rate >= 60 ? 'warning' : 'danger') }}" 
                                                                     style="width: {{ $efficiency->collection_rate }}%"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">
                                                            Rp {{ number_format($efficiency->collected_amount, 0, ',', '.') }}
                                                        </span><br>
                                                        <small class="text-muted">
                                                            dari Rp {{ number_format($efficiency->total_billed_amount, 0, ',', '.') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $efficiency->amount_collection_rate >= 80 ? 'success' : ($efficiency->amount_collection_rate >= 60 ? 'warning' : 'danger') }}">
                                                            {{ $efficiency->amount_collection_rate }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
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
    // Monthly Trend Chart
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    const monthlyData = {!! json_encode($monthlyTrend) !!};
    
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`),
            datasets: [{
                label: 'Total Amount',
                data: monthlyData.map(item => item.total_amount),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Transaction Count',
                data: monthlyData.map(item => item.transaction_count),
                type: 'line',
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                        text: 'Total Amount (Rp)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Transaction Count'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Peak Times Chart
    const peakCtx = document.getElementById('peakTimesChart').getContext('2d');
    const peakData = {!! json_encode($peakTimesAnalysis) !!};
    
    new Chart(peakCtx, {
        type: 'bar',
        data: {
            labels: peakData.map(item => `${item.hour}:00`),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: peakData.map(item => item.transaction_count),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Jam (24 Hour Format)'
                    }
                }
            }
        }
    });

    // Trends Modal Logic
    const trendsModalEl = document.getElementById('trendsModal');
    let modalChart = null;
    let cachedTrendsData = null;

    trendsModalEl.addEventListener('show.bs.modal', function () {
        const select = document.getElementById('trendTypeSelect');
        const spinner = document.getElementById('modalLoadingSpinner');

        // Reset state
        if (modalChart) {
            modalChart.destroy();
            modalChart = null;
        }
        
        // Show spinner
        spinner.classList.remove('d-none');
        
        // Fetch trends data via AJAX
        const params = new URLSearchParams(window.location.search);
        fetch('{{ route($spref . "keuangan.analisis-trends") }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('d-none');
                if (data.success) {
                    cachedTrendsData = data.trends;
                    renderModalChart(select.value);
                } else {
                    alert('Gagal memuat data tren pembayaran.');
                }
            })
            .catch(error => {
                spinner.classList.add('d-none');
                console.error('Error fetching trends:', error);
                alert('Terjadi kesalahan koneksi saat memuat tren.');
            });
    });

    document.getElementById('trendTypeSelect').addEventListener('change', function() {
        if (cachedTrendsData) {
            renderModalChart(this.value);
        }
    });

    function renderModalChart(type) {
        const canvas = document.getElementById('modalTrendsChart');
        const ctx = canvas.getContext('2d');
        
        if (modalChart) {
            modalChart.destroy();
        }

        let labels = [];
        let data = [];
        let labelName = '';
        let borderColor = 'rgb(54, 162, 235)';
        let backgroundColor = 'rgba(54, 162, 235, 0.2)';

        if (type === 'daily') {
            labels = ['H-6', 'H-5', 'H-4', 'H-3', 'H-2', 'H-1', 'Hari Ini'];
            data = cachedTrendsData.daily;
            labelName = 'Pemasukan Harian';
            borderColor = 'rgb(75, 192, 192)';
            backgroundColor = 'rgba(75, 192, 192, 0.2)';
        } else if (type === 'weekly') {
            labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
            data = cachedTrendsData.weekly;
            labelName = 'Pemasukan Mingguan';
            borderColor = 'rgb(153, 102, 255)';
            backgroundColor = 'rgba(153, 102, 255, 0.2)';
        } else if (type === 'monthly') {
            labels = ['Bulan 1', 'Bulan 2', 'Bulan 3', 'Bulan 4', 'Bulan 5'];
            data = cachedTrendsData.monthly;
            labelName = 'Pemasukan Bulanan';
            borderColor = 'rgb(255, 159, 64)';
            backgroundColor = 'rgba(255, 159, 64, 0.2)';
        }

        modalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: labelName,
                    data: data,
                    borderColor: borderColor,
                    backgroundColor: backgroundColor,
                    tension: 0.2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }
});

function exportAnalysis(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('type', type);
    
    window.open('{{ route($spref . "keuangan.analisis-export") }}?' + params.toString(), '_blank');
}
</script>
@endsection
