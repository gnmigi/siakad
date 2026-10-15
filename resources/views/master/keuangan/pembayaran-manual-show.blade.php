@extends('core-themes.core-backpage')

@php
    $menus = 'Master Keuangan';
    $pages = 'Detail Pembayaran Manual';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Detail Pembayaran Manual
                </h3>
                <div class="card-actions">
                    <div class="btn-list">
                        @if(in_array($payment->status, ['pending', 'paid']))
                            <a href="{{ route('keuangan.pembayaran-manual-edit', $payment->id) }}" class="btn btn-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Edit
                            </a>
                        @endif

                        @if($payment->status === 'verified')
                            <a href="{{ route('keuangan.pembayaran-manual-download-receipt', $payment->id) }}" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 15l2 2l4 -4" />
                                </svg>
                                Download Receipt
                            </a>
                        @endif

                        <a href="{{ route('keuangan.pembayaran-manual-index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path d="M5 12l14 0" />
                                <path d="M5 12l6 -6" />
                                <path d="M5 12l6 6" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Transaction Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informasi Transaksi</h4>
                            </div>
                            <div class="card-body">
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Kode Transaksi</div>
                                        <div class="datagrid-content">
                                            <strong>{{ $payment->transaction_code }}</strong>
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Nomor Invoice</div>
                                        <div class="datagrid-content">{{ $payment->invoice_number }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Status</div>
                                        <div class="datagrid-content">
                                            @switch($payment->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge bg-info">Dibayar</span>
                                                    @break
                                                @case('verified')
                                                    <span class="badge bg-success">Terverifikasi</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">Gagal</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-secondary">Dibatalkan</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light">{{ ucfirst($payment->status) }}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Jumlah Tagihan</div>
                                        <div class="datagrid-content">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Jumlah Dibayar</div>
                                        <div class="datagrid-content">
                                            <strong class="text-success">Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    @if($payment->paid_amount < $payment->total_amount)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Sisa Tagihan</div>
                                            <div class="datagrid-content">
                                                <span class="text-danger">Rp {{ number_format($payment->total_amount - $payment->paid_amount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Tanggal Pembayaran</div>
                                        <div class="datagrid-content">
                                            {{ $payment->paid_at ? $payment->paid_at->format('d F Y, H:i') : '-' }}
                                        </div>
                                    </div>
                                    @if($payment->verified_at)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Tanggal Verifikasi</div>
                                            <div class="datagrid-content">
                                                {{ $payment->verified_at->format('d F Y, H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informasi Mahasiswa</h4>
                            </div>
                            <div class="card-body">
                                @if($payment->mahasiswa)
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">NIM</div>
                                            <div class="datagrid-content">{{ $payment->mahasiswa->nim }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Nama</div>
                                            <div class="datagrid-content">
                                                <strong>{{ $payment->mahasiswa->name }}</strong>
                                            </div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Program Studi</div>
                                            <div class="datagrid-content">{{ $payment->mahasiswa->programStudi->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Email</div>
                                            <div class="datagrid-content">{{ $payment->mahasiswa->email ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Telepon</div>
                                            <div class="datagrid-content">{{ $payment->mahasiswa->phone ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Status</div>
                                            <div class="datagrid-content">
                                                <span class="badge bg-{{ $payment->mahasiswa->status === 'Aktif' ? 'success' : 'secondary' }}">
                                                    {{ $payment->mahasiswa->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h4 class="alert-title">Data mahasiswa tidak ditemukan</h4>
                                        <p class="mb-0">Informasi mahasiswa untuk transaksi ini tidak tersedia.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Bill Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informasi Tagihan</h4>
                            </div>
                            <div class="card-body">
                                @if($payment->tagihan)
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Nama Tagihan</div>
                                            <div class="datagrid-content">
                                                <strong>{{ $payment->tagihan->name }}</strong>
                                            </div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Deskripsi</div>
                                            <div class="datagrid-content">{{ $payment->tagihan->description ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Grup Tagihan</div>
                                            <div class="datagrid-content">{{ $payment->tagihan->group->name ?? 'Individual' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Tahun Akademik</div>
                                            <div class="datagrid-content">{{ $payment->tagihan->tahunAkademik->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Jatuh Tempo</div>
                                            <div class="datagrid-content">
                                                {{ $payment->tagihan->due_date->format('d F Y') }}
                                                @if($payment->tagihan->isOverdue())
                                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Status Tagihan</div>
                                            <div class="datagrid-content">
                                                @switch($payment->tagihan->status)
                                                    @case('Pending')
                                                        <span class="badge bg-warning">Belum Bayar</span>
                                                        @break
                                                    @case('Sukses')
                                                        <span class="badge bg-success">Lunas</span>
                                                        @break
                                                    @case('Gagal')
                                                        <span class="badge bg-danger">Gagal</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light">{{ $payment->tagihan->status }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h4 class="alert-title">Data tagihan tidak ditemukan</h4>
                                        <p class="mb-0">Informasi tagihan untuk transaksi ini tidak tersedia.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Metode Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Metode</div>
                                        <div class="datagrid-content">
                                            <strong>{{ $payment->paymentMethod->name ?? 'Manual' }}</strong>
                                        </div>
                                    </div>
                                    @if($payment->bankAccount)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Bank</div>
                                            <div class="datagrid-content">{{ $payment->bankAccount->bank_name }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Nomor Rekening</div>
                                            <div class="datagrid-content">{{ $payment->bankAccount->account_number }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Nama Rekening</div>
                                            <div class="datagrid-content">{{ $payment->bankAccount->account_name }}</div>
                                        </div>
                                    @endif
                                    @if($payment->payment_reference)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Referensi</div>
                                            <div class="datagrid-content">{{ $payment->payment_reference }}</div>
                                        </div>
                                    @endif
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Gateway</div>
                                        <div class="datagrid-content">
                                            <span class="badge bg-orange">{{ ucfirst($payment->gateway) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($payment->payment_proof)
                                    <div class="mt-3">
                                        <label class="form-label">Bukti Pembayaran</label>
                                        <div class="card">
                                            <div class="card-body p-2">
                                                @php
                                                    $extension = pathinfo($payment->payment_proof, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                @endphp
                                                
                                                @if($isImage)
                                                    <img src="{{ asset('storage/' . $payment->payment_proof) }}" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 200px; cursor: pointer;"
                                                         onclick="window.open(this.src, '_blank')">
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        </svg>
                                                        <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="text-decoration-none">
                                                            Lihat Dokumen ({{ strtoupper($extension) }})
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($payment->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Catatan</h4>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $payment->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Admin Information -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informasi Admin</h4>
                            </div>
                            <div class="card-body">
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Dibuat Oleh</div>
                                        <div class="datagrid-content">{{ $payment->createdBy->name ?? 'System' }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Tanggal Dibuat</div>
                                        <div class="datagrid-content">{{ $payment->created_at->format('d F Y, H:i') }}</div>
                                    </div>
                                    @if($payment->verifiedBy)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Diverifikasi Oleh</div>
                                            <div class="datagrid-content">{{ $payment->verifiedBy->name }}</div>
                                        </div>
                                    @endif
                                    @if($payment->updated_at != $payment->created_at)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Terakhir Diupdate</div>
                                            <div class="datagrid-content">{{ $payment->updated_at->format('d F Y, H:i') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($payment->riwayatPembayaran)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Riwayat Pembayaran</h4>
                                </div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Jumlah</div>
                                            <div class="datagrid-content">Rp {{ number_format($payment->riwayatPembayaran->amount, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Metode</div>
                                            <div class="datagrid-content">{{ $payment->riwayatPembayaran->payment_method }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Tanggal</div>
                                            <div class="datagrid-content">{{ $payment->riwayatPembayaran->payment_date->format('d F Y') }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Status</div>
                                            <div class="datagrid-content">
                                                <span class="badge bg-success">{{ $payment->riwayatPembayaran->status }}</span>
                                            </div>
                                        </div>
                                        @if($payment->riwayatPembayaran->notes)
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Catatan</div>
                                                <div class="datagrid-content">{{ $payment->riwayatPembayaran->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
