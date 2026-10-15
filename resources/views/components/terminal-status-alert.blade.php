@props(['mahasiswa'])

@php
    $terminalService = app(\App\Services\TerminalService::class);
    $terminalStatus = $terminalService->getTerminalStatus($mahasiswa);
@endphp

@if($terminalStatus['status'] === 'terminal')
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="fas fa-ban me-2"></i>
        <div>
            <strong>Status Terminal:</strong> {{ $terminalStatus['message'] }}
            @if(isset($terminalStatus['terminal_info']['reason']))
                <br><small>{{ $terminalStatus['terminal_info']['reason'] }}</small>
            @endif
        </div>
    </div>
@elseif($terminalStatus['status'] === 'warning')
    @php
        $riskLevel = $terminalStatus['risk_level'] ?? 'none';
        $alertClass = match($riskLevel) {
            'high' => 'alert-danger',
            'medium' => 'alert-warning',
            'low' => 'alert-info',
            default => 'alert-secondary'
        };
        $icon = match($riskLevel) {
            'high' => 'fas fa-exclamation-triangle',
            'medium' => 'fas fa-exclamation-circle',
            'low' => 'fas fa-info-circle',
            default => 'fas fa-info'
        };
    @endphp
    
    <div class="{{ $alertClass }} d-flex align-items-center" role="alert">
        <i class="{{ $icon }} me-2"></i>
        <div>
            <strong>Peringatan Tunggakan:</strong> {{ $terminalStatus['message'] }}
            @if($riskLevel === 'high')
                <br><small class="text-danger"><strong>Risiko Tinggi:</strong> Segera lakukan pembayaran untuk menghindari pemblokiran akses.</small>
            @elseif($riskLevel === 'medium')
                <br><small class="text-warning"><strong>Risiko Sedang:</strong> Harap segera melakukan pembayaran.</small>
            @endif
            
            @if(isset($terminalStatus['unpaid_bills']) && $terminalStatus['unpaid_bills']->isNotEmpty())
                <div class="mt-2">
                    <small><strong>Detail Tunggakan:</strong></small>
                    <ul class="mb-0 mt-1">
                        @foreach($terminalStatus['unpaid_bills']->take(3) as $bill)
                            <li><small>{{ $bill->desc }} - Rp {{ number_format($bill->amount, 0, ',', '.') }}</small></li>
                        @endforeach
                        @if($terminalStatus['unpaid_bills']->count() > 3)
                            <li><small>... dan {{ $terminalStatus['unpaid_bills']->count() - 3 }} tagihan lainnya</small></li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
