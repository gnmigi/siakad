@extends('core-themes.core-mainpage')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-green" width="64" height="64" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                            <path d="M9 12l2 2l4 -4"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-green mb-3">Pendaftaran Berhasil!</h2>
                    
                    <div class="alert alert-success text-start">
                        <h4 class="alert-title">Selamat! Pendaftaran Anda telah berhasil diproses.</h4>
                        <div class="text-secondary">
                            <p><strong>Nomor Pendaftaran:</strong> {{ $pendaftar->code }}</p>
                            <p><strong>Nama:</strong> {{ $pendaftar->name }}</p>
                            <p><strong>Email:</strong> {{ $pendaftar->email }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-yellow">Menunggu Verifikasi</span></p>
                        </div>
                    </div>

                    <div class="card bg-blue-lt mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Langkah Selanjutnya:</h4>
                            <ol class="text-start mb-0">
                                <li>Simpan nomor pendaftaran Anda: <strong>{{ $pendaftar->code }}</strong></li>
                                <li>Cek email Anda untuk konfirmasi pendaftaran</li>
                                <li>Login ke dashboard calon mahasiswa untuk melengkapi dokumen</li>
                                <li>Tunggu proses verifikasi dari admin</li>
                                <li>Pantau status pendaftaran Anda secara berkala</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col">
                            <a href="{{ route('auth.render-signin') }}" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M9 12h12l-3 -3"></path>
                                    <path d="M18 15l3 -3"></path>
                                </svg>
                                Login Dashboard
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('root.home-index') }}" class="btn btn-secondary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            Jika Anda mengalami kendala, silakan hubungi admin melalui WhatsApp atau email yang tersedia di website.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
