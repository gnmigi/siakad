@extends('central.backpage.core-backpage-calon-mahasiswa')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Profil Calon Mahasiswa
                    </h2>
                    <div class="text-muted mt-1">Kelola data pribadi dan dokumen pendaftaran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <form action="{{ route('camaba.profile-handle') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Data Pribadi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Data Pribadi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label required">Jenis Kelamin</label>
                                        <select class="form-select" name="jenis_kelamin" required>
                                            <option value="">Pilih</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">NIK</label>
                                        <input type="text" class="form-control" name="nik" value="{{ old('nik', $user->nik) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">NISN</label>
                                        <input type="text" class="form-control" name="nisn" value="{{ old('nisn', $user->nisn) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Tempat Lahir</label>
                                        <input type="text" class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') : '') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">No. HP/WhatsApp</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat', $user->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pendidikan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Data Pendidikan Terakhir</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Asal Sekolah</label>
                                        <input type="text" class="form-control" name="asal_sekolah" value="{{ old('asal_sekolah', $user->asal_sekolah) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Tahun Lulus</label>
                                        <input type="number" class="form-control" name="tahun_lulus" value="{{ old('tahun_lulus', $user->tahun_lulus) }}" min="2000" max="{{ date('Y') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Data Orang Tua/Wali</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama Ayah</label>
                                        <input type="text" class="form-control" name="nama_ayah" value="{{ old('nama_ayah', $user->nama_ayah) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama Ibu</label>
                                        <input type="text" class="form-control" name="nama_ibu" value="{{ old('nama_ibu', $user->nama_ibu) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ayah</label>
                                        <input type="text" class="form-control" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $user->pekerjaan_ayah) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ibu</label>
                                        <input type="text" class="form-control" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $user->pekerjaan_ibu) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. HP Orang Tua</label>
                                <input type="text" class="form-control" name="no_hp_ortu" value="{{ old('no_hp_ortu', $user->no_hp_ortu) }}">
                            </div>
                        </div>
                    </div>


                    <!-- Ubah Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Ubah Password</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Password Lama</label>
                                        <input type="password" class="form-control" name="current_password">
                                        <small class="form-hint">Kosongkan jika tidak ingin mengubah password</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" name="password">
                                        <small class="form-hint">Minimal 8 karakter</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('camaba.dashboard-render') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
