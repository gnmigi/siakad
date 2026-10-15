@extends('core-themes.core-backpage')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium fs-3" id="fd-total-mahasiswa">{{ number_format($totalMahasiswa ?? 0) }}</div>
                                <div class="text-muted fs-5">Total Mahasiswa Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium fs-3" id="fd-monthly-revenue">Rp {{ number_format($monthlyRevenue ?? 0) }}</div>
                                <div class="text-muted fs-5">Pendapatan Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-yellow text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v20m8-9H4" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium fs-3" id="fd-pending-payments">{{ number_format($pendingPayments ?? 0) }}</div>
                                <div class="text-muted fs-5">Pembayaran Pending</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-red text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v18h18" />
                                        <path d="M18.7 8l-5.1 5.2l-2.8 -2.7l-4.8 4.8" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium fs-3" id="fd-outstanding-balance">Rp {{ number_format($outstandingBalance ?? 0) }}</div>
                                <div class="text-muted fs-5">Total Tunggakan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title">Tren Pembayaran</h3>
            </div>
            <div class="card-body"><div id="chart-payment-trends"></div></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title">Metode Pembayaran</h3>
            </div>
            <div class="card-body"><div id="chart-payment-methods"></div></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm" style="height: 28rem;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Pembayaran Terbaru</h3>
                <small class="text-muted">Update: <span id="fd-last-updated" class="fw-medium">{{ $lastUpdated ?? '-' }}</span></small>
            </div>
            <div class="card-body card-body-scrollable custom-scrollbar">
                @if($recentPayments && $recentPayments->count() > 0)
                    <div class="list-group list-group-flush list-group-hoverable">
                        @foreach($recentPayments as $payment)
                            <div class="list-group-item px-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="badge bg-green-lt p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" /></svg>
                                        </span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="fw-semibold text-reset d-block">{{ $payment->description ?? 'Pembayaran' }}</div>
                                        <small class="text-muted text-truncate mt-n1">{{ $payment->created_at ? $payment->created_at->diffForHumans() : 'Baru saja' }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-green fw-bold">+Rp {{ number_format($payment->amount ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty mt-4">
                        <div class="empty-icon text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                        </div>
                        <p class="empty-title">Belum ada pembayaran</p>
                        <p class="empty-subtitle text-muted">Transaksi pembayaran terbaru akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm" style="height: 28rem;">
            <div class="card-header border-bottom">
                <h3 class="card-title">Tagihan Terbaru</h3>
            </div>
            <div class="card-body card-body-scrollable custom-scrollbar">
                @if($recentTagihan && $recentTagihan->count() > 0)
                    <div class="list-group list-group-flush list-group-hoverable">
                        @foreach($recentTagihan as $tagihan)
                            <div class="list-group-item px-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="badge bg-red-lt p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 7l1 0" /><path d="M9 13l6 0" /><path d="M13 17l2 0" /></svg>
                                        </span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="fw-semibold text-reset d-block">{{ $tagihan->name ?? 'Tagihan' }}</div>
                                        <small class="text-muted text-truncate mt-n1">{{ $tagihan->created_at ? $tagihan->created_at->diffForHumans() : 'Baru saja' }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <span class="fw-bold">Rp {{ number_format($tagihan->amount ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty mt-4">
                        <div class="empty-icon text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="12" x2="15" y2="12" /></svg>
                        </div>
                        <p class="empty-title">Tidak ada tagihan</p>
                        <p class="empty-subtitle text-muted">Saat ini tidak ada tagihan baru yang tercatat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<style>
.finance-quick-actions .btn { min-height: 88px; }
/* Scrollbar tipis yang lebih elegan */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e0e6ed; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #c0c6cc; }
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function formatIDR(v){ return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v||0)); }

    async function refreshFinanceDashboard() {
        try {
            const response = await fetch("{{ route('dashboard-render') }}?finance_refresh=1", {
                headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
                credentials: 'same-origin'
            });
            const contentType = (response.headers.get('content-type') || '').toLowerCase();
            if (!response.ok || !contentType.includes('application/json')) return;
            const payload = await response.json();
            if (!payload || !payload.success || !payload.data) return;

            const d = payload.data;
            const elM = document.getElementById('fd-total-mahasiswa');
            const elR = document.getElementById('fd-monthly-revenue');
            const elP = document.getElementById('fd-pending-payments');
            const elO = document.getElementById('fd-outstanding-balance');
            const elU = document.getElementById('fd-last-updated');

            if (elM) elM.textContent = new Intl.NumberFormat('id-ID').format(Number(d.totalMahasiswa || 0));
            if (elR) elR.textContent = formatIDR(d.monthlyRevenue || 0);
            if (elP) elP.textContent = new Intl.NumberFormat('id-ID').format(Number(d.pendingPayments || 0));
            if (elO) elO.textContent = formatIDR(d.outstandingBalance || 0);
            if (elU) elU.textContent = d.lastUpdated || '-';
        } catch (e) {}
    }

    setInterval(refreshFinanceDashboard, 20000);

    if (document.getElementById('chart-payment-trends') && window.ApexCharts) {
        new ApexCharts(document.getElementById('chart-payment-trends'), {
            chart: { type: "area", height: 240, parentHeightOffset: 0, toolbar: { show: false }, animations: { enabled: false } },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            stroke: { width: 2, curve: "smooth" },
            series: [{ name: "Pembayaran", data: @js($paymentTrends['values'] ?? [0,0,0,0,0,0]) }],
            xaxis: { categories: @js($paymentTrends['labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun']), tooltip: { enabled: false } },
            colors: [tabler.getColor("primary")],
            grid: { padding: { bottom: -10 } }
        }).render();
    }

    if (document.getElementById('chart-payment-methods') && window.ApexCharts) {
        new ApexCharts(document.getElementById('chart-payment-methods'), {
            chart: { type: "donut", height: 240, animations: { enabled: false } },
            series: @js($paymentMethods['values'] ?? [0,0,0,0]),
            labels: @js($paymentMethods['labels'] ?? ['Transfer Bank','Tunai','Midtrans','Lainnya']),
            colors: [tabler.getColor("primary"), tabler.getColor("success"), tabler.getColor("warning"), tabler.getColor("info")],
            dataLabels: { dropShadow: { enabled: false } },
            legend: { position: 'bottom' }
        }).render();
    }
});
</script>
@endsection