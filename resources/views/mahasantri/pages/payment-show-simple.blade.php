@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Detail Pembayaran - {{ config('app.name') }}
@endsection

@section('custom-css')
    <!-- Midtrans Snap Script -->
    @if($transaction->paymentMethod->type === 'midtrans_snap')
    <script type="text/javascript" 
            src="{{ config('midtrans.is_production') ? config('midtrans.production_snap_url') : config('midtrans.sandbox_snap_url') }}" 
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
@endsection

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Tagihan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td>Kode Transaksi:</td>
                                    <td><strong>{{ $transaction->transaction_code }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Tagihan:</td>
                                    <td>{{ $transaction->tagihan->name ?? 'Tagihan Akademik' }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah:</td>
                                    <td><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>
                                        @if($transaction->status === 'pending')
                                            <span class="badge bg-warning">Menunggu Pembayaran</span>
                                        @elseif($transaction->status === 'verified')
                                            <span class="badge bg-success">Terverifikasi</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Metode Pembayaran</h6>
                            <p><strong>{{ $transaction->paymentMethod->name }}</strong></p>
                            
                            @if($transaction->paymentMethod->type === 'midtrans_snap')
                                <div class="alert alert-info">
                                    <h6>Pembayaran Online (Midtrans Snap)</h6>
                                    <p>Tersedia 50+ metode pembayaran:</p>
                                    <ul>
                                        <li>Virtual Account (BCA, Mandiri, BNI, BRI, dll)</li>
                                        <li>E-Wallet (GoPay, OVO, DANA, ShopeePay)</li>
                                        <li>Credit/Debit Card</li>
                                        <li>Internet Banking</li>
                                        <li>Retail (Indomaret, Alfamart)</li>
                                    </ul>
                                </div>
                                
                                @if(session('snap_token'))
                                    <button id="pay-button" class="btn btn-success btn-lg">
                                        <i class="fas fa-credit-card"></i> Bayar Sekarang
                                    </button>
                                    <p class="mt-2 text-muted">
                                        <small>Klik tombol di atas untuk membuka jendela pembayaran Midtrans</small>
                                    </p>
                                @else
                                    <div class="alert alert-warning">
                                        <p>Token pembayaran tidak tersedia. Silakan refresh halaman atau buat transaksi baru.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
@if($transaction->paymentMethod->type === 'midtrans_snap')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Midtrans Snap Debug Info:');
    console.log('- Snap script loaded:', typeof snap !== 'undefined');
    console.log('- Transaction ID:', {{ $transaction->id }});
    console.log('- Payment Method:', '{{ $transaction->paymentMethod->type }}');
    console.log('- Has snap_token:', {{ session('snap_token') ? 'true' : 'false' }});
    
    @if(session('snap_token'))
    console.log('- Snap token length:', '{{ session('snap_token') }}'.length);
    
    var payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function() {
            var snapToken = '{{ session('snap_token') }}';
            
            console.log('🚀 Triggering Snap Pop-up with token:', snapToken.substring(0, 20) + '...');
            
            if (typeof snap === 'undefined') {
                alert('❌ Midtrans Snap script tidak dapat dimuat. Silakan refresh halaman.');
                return;
            }
            
            if (!snapToken || snapToken.length < 10) {
                alert('❌ Token pembayaran tidak valid. Silakan coba lagi.');
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('✅ Payment success:', result);
                    alert('Pembayaran berhasil! Halaman akan dimuat ulang...');
                    window.location.reload();
                },
                onPending: function(result) {
                    console.log('⏳ Payment pending:', result);
                    alert('Pembayaran sedang diproses. Status akan diperbarui secara otomatis.');
                    window.location.reload();
                },
                onError: function(result) {
                    console.log('❌ Payment error:', result);
                    alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                },
                onClose: function() {
                    console.log('🔒 Payment popup closed');
                }
            });
        });
    }
    @else
    console.log('⚠️ No snap token found in session');
    @endif
});
</script>
@endif
@endsection