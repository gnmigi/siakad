@extends('central.backpage.core-backpage-calon-mahasiswa')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard Calon Mahasiswa
                    </h2>
                    <div class="text-muted mt-1">Selamat datang, {{ $user->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="row row-deck row-cards">
            <!-- Status Pendaftaran dengan UI/UX yang Ditingkatkan -->
            @if($user->mahasiswa_id && $user->mahasiswa_id > 0 && $mahasiswaData)
            <!-- Status Mahasiswa Aktif -->
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-xl me-3" style="background: rgba(255,255,255,0.2);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"></path>
                                            <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="mb-1">🎓 Selamat! Anda Sudah Menjadi Mahasiswa Aktif!</h2>
                                        <p class="mb-0 opacity-75">Status Anda telah berubah menjadi mahasiswa aktif</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>NIM:</strong> {{ $mahasiswaData->numb_nim }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Program Studi:</strong> {{ $mahasiswaData->prodi->name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Kelas:</strong> {{ $mahasiswaData->kelas->name ?? '-' }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Semester:</strong> {{ $mahasiswaData->semester ?? 1 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <span class="badge bg-white text-primary fs-4 px-4 py-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                        MAHASISWA AKTIF
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Login Mahasiswa -->
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex">
                        <div class="me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                <path d="M12 8l0 4l3 0"></path>
                            </svg>
                        </div>
                        <div class="flex-fill">
                            <h4 class="alert-title">Informasi Login Sistem Akademik</h4>
                            <div class="text-secondary">
                                <p class="mb-2">Untuk mengakses sistem akademik, silakan login menggunakan:</p>
                                <ul class="mb-2">
                                    <li><strong>Username/NIM:</strong> {{ $mahasiswaData->numb_nim }}</li>
                                    <li><strong>Password:</strong> <span class="text-muted">(sama dengan password akun calon mahasiswa Anda)</span></li>
                                </ul>
                                <p class="mb-0">
                                    <a href="{{ route('auth.render-signin') }}" class="btn btn-primary btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M20 12h-13l3 -3m0 6l-3 -3"></path>
                                        </svg>
                                        Login ke Sistem Akademik
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @elseif($user->status == 'Lulus' || $user->status == 'Diterima' || $user->status == 'diterima')
            <!-- Status Diterima -->
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-xl me-3" style="background: rgba(255,255,255,0.2);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="mb-1">🎉 Selamat! Pendaftaran Anda DITERIMA!</h2>
                                        <p class="mb-0 opacity-75">Pendaftaran Anda telah disetujui oleh admin</p>
                                    </div>
                                </div>
                                <p class="mb-0">Silakan tunggu informasi selanjutnya mengenai proses registrasi ulang dan pembayaran. Pantau terus dashboard ini untuk update terbaru.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <span class="badge bg-white text-success fs-4 px-4 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                    DITERIMA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @elseif($user->status == 'Gagal' || $user->status == 'Ditolak' || $user->status == 'ditolak')
            <!-- Status Ditolak -->
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-xl me-3" style="background: rgba(255,255,255,0.2);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M18 6l-12 12"></path>
                                            <path d="M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="mb-1">Pendaftaran Ditolak</h2>
                                        <p class="mb-0 opacity-75">Mohon maaf, pendaftaran Anda tidak dapat diproses</p>
                                    </div>
                                </div>
                                @if($user->rejection_reason)
                                <div class="mb-2">
                                    <strong>Alasan:</strong> {{ $user->rejection_reason }}
                                </div>
                                @endif
                                <p class="mb-0">Silakan hubungi admin untuk informasi lebih lanjut atau untuk mendaftar ulang pada periode berikutnya.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <span class="badge bg-white text-danger fs-4 px-4 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M18 6l-12 12"></path>
                                        <path d="M6 6l12 12"></path>
                                    </svg>
                                    DITOLAK
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <!-- Status Pending/Menunggu -->
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-xl me-3" style="background: rgba(255,255,255,0.2);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M12 8l0 4l3 0"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="mb-1">Menunggu Verifikasi</h2>
                                        <p class="mb-0 opacity-75">Pendaftaran Anda sedang dalam proses verifikasi</p>
                                    </div>
                                </div>
                                <p class="mb-0">Pastikan semua dokumen telah dilengkapi dengan benar. Kami akan menginformasikan hasil verifikasi melalui dashboard ini.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <span class="badge bg-white text-warning fs-4 px-4 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path d="M12 8l0 4l3 0"></path>
                                    </svg>
                                    MENUNGGU
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informasi Dasar Pendaftaran -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Pendaftaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Pendaftaran</label>
                                    <div class="form-control-plaintext"><strong>{{ $user->code }}</strong></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <div class="form-control-plaintext">{{ $user->name }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <div class="form-control-plaintext">{{ $user->created_at->format('d F Y H:i') }}</div>
                                </div>
                                @if($user->periode)
                                <div class="mb-3">
                                    <label class="form-label">Periode</label>
                                    <div class="form-control-plaintext">{{ $user->periode->name }} ({{ $user->periode->tahun ?? date('Y') }})</div>
                                </div>
                                @endif
                                @if($user->jalur)
                                <div class="mb-3">
                                    <label class="form-label">Jalur Pendaftaran</label>
                                    <div class="form-control-plaintext">{{ $user->jalur->name }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pribadi</h3>
                        <div class="card-actions">
                            <a href="{{ route('camaba.profile-render') }}" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                    <path d="M16 5l3 3"></path>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">NIK</label>
                                <div class="form-control-plaintext">{{ $user->nik ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <div class="form-control-plaintext">{{ $user->tempat_lahir ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <div class="form-control-plaintext">
                                    {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <div class="form-control-plaintext">{{ $user->alamat ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">No. HP</label>
                                <div class="form-control-plaintext">{{ $user->phone ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pendidikan -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pendidikan</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <div class="form-control-plaintext">{{ $user->asal_sekolah ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Lulus</label>
                            <div class="form-control-plaintext">{{ $user->tahun_lulus ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <div class="form-control-plaintext">{{ $user->nisn ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Orang Tua/Wali</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Data Ayah</h4>
                                <div class="mb-3">
                                    <label class="form-label">Nama Ayah</label>
                                    <div class="form-control-plaintext">{{ $user->nama_ayah ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pekerjaan</label>
                                    <div class="form-control-plaintext">{{ $user->pekerjaan_ayah ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3">Data Ibu</h4>
                                <div class="mb-3">
                                    <label class="form-label">Nama Ibu</label>
                                    <div class="form-control-plaintext">{{ $user->nama_ibu ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pekerjaan</label>
                                    <div class="form-control-plaintext">{{ $user->pekerjaan_ibu ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">No. HP Orang Tua</label>
                                    <div class="form-control-plaintext">{{ $user->no_hp_ortu ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Pendaftaran -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Dokumen Pendaftaran</h3>
                            <a href="{{ route('camaba.requirements-render') }}" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                </svg>
                                Kelola Dokumen
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($requirements->isEmpty())
                            <div class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="empty-title">Belum ada syarat pendaftaran</p>
                                    <p class="empty-subtitle text-muted">
                                        Syarat pendaftaran untuk periode Anda belum ditetapkan oleh admin.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                @foreach($requirements as $requirement)
                                    @php
                                        $upload = $uploads->get($requirement->id);
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1">{{ $requirement->name }}</h6>
                                                @if($requirement->is_required)
                                                    <span class="badge bg-red">Wajib</span>
                                                @else
                                                    <span class="badge bg-blue">Opsional</span>
                                                @endif
                                            </div>
                                            
                                            @if($requirement->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($requirement->description, 80) }}</p>
                                            @endif

                                            @if($upload)
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                                        </svg>
                                                        <span class="small text-break">{{ Str::limit($upload->original_name, 25) }}</span>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        {{ number_format($upload->file_size / 1024, 1) }} KB • {{ $upload->uploaded_at->diffForHumans() }}
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    @if($upload->status == 'approved')
                                                        <span class="badge bg-green">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M5 12l5 5l10 -10"></path>
                                                            </svg>
                                                            Disetujui
                                                        </span>
                                                    @elseif($upload->status == 'rejected')
                                                        <span class="badge bg-red">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M18 6l-12 12"></path>
                                                                <path d="M6 6l12 12"></path>
                                                            </svg>
                                                            Ditolak
                                                        </span>
                                                    @else
                                                        <span class="badge bg-yellow">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                                                <path d="M12 8l0 4l3 0"></path>
                                                            </svg>
                                                            Menunggu
                                                        </span>
                                                    @endif
                                                    
                                                    <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-2">
                                                    <div class="text-muted mb-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M18 6l-12 12"></path>
                                                            <path d="M6 6l12 12"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="badge bg-secondary">Belum Upload</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $totalRequirements = $requirements->count();
                                $uploadedCount = $uploads->count();
                                $approvedCount = $uploads->where('status', 'approved')->count();
                                $rejectedCount = $uploads->where('status', 'rejected')->count();
                                $pendingCount = $uploads->where('status', 'pending')->count();
                            @endphp

                            <!-- Summary -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                                    <path d="M12 8l0 4l3 0"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="alert-title">Status Dokumen</h4>
                                                <div class="text-secondary">
                                                    <div class="row">
                                                        <div class="col-6 col-md-3 mb-1">
                                                            <strong>Total:</strong> {{ $totalRequirements }} dokumen
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-1">
                                                            <strong>Diupload:</strong> {{ $uploadedCount }} dokumen
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-1">
                                                            <strong>Disetujui:</strong> {{ $approvedCount }} dokumen
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-1">
                                                            <strong>Menunggu:</strong> {{ $pendingCount }} dokumen
                                                        </div>
                                                    </div>
                                                    @if($rejectedCount > 0)
                                                        <div class="mt-2">
                                                            <span class="text-danger"><strong>{{ $rejectedCount }} dokumen ditolak</strong> - silakan upload ulang melalui halaman syarat pendaftaran.</span>
                                                        </div>
                                                    @endif
                                                    @if($uploadedCount < $totalRequirements)
                                                        <div class="mt-2">
                                                            <span class="text-warning"><strong>{{ $totalRequirements - $uploadedCount }} dokumen belum diupload</strong> - lengkapi melalui halaman syarat pendaftaran.</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
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
</div>
@endsection
