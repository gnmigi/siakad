@extends('core-themes.core-mainpage')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                            <path d="M9 9l1 0"></path>
                            <path d="M9 13l6 0"></path>
                            <path d="M9 17l6 0"></path>
                        </svg>
                        Formulir Pendaftaran Mahasiswa Baru
                    </h3>
                </div>
                <div class="card-body">
                    @if($periodes->isEmpty())
                        <div class="alert alert-warning">
                            <h4>Pendaftaran Belum Dibuka</h4>
                            <p>Saat ini belum ada periode pendaftaran yang aktif. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                        </div>
                    @else
                        <form action="{{ route('pmb.registration.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Periode dan Jalur Pendaftaran -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Periode Pendaftaran</label>
                                    <select class="form-select @error('periode_id') is-invalid @enderror" name="periode_id" id="periode_id" required>
                                        @forelse($periodes as $periode)
                                            <option value="{{ $periode->id }}" {{ old('periode_id') == $periode->id ? 'selected' : '' }}>
                                                {{ $periode->name }}
                                            </option>
                                        @empty
                                            <option disabled>Tidak ada periode tersedia</option>
                                        @endforelse
                                    </select>
                                    @error('periode_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Jalur Pendaftaran</label>
                                    <select class="form-select @error('jalur_id') is-invalid @enderror" name="jalur_id" id="jalur_id" required>
                                        <option value="">Pilih Jalur</option>
                                    </select>
                                    @error('jalur_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Program Studi Pilihan -->
                            <h4 class="mb-3">Pilihan Program Studi</h4>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Pilihan 1</label>
                                    <select class="form-select @error('prodi_1') is-invalid @enderror" name="prodi_1" required>
                                        <option value="">Pilih Program Studi</option>
                                        @foreach($prodis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ old('prodi_1') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('prodi_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pilihan 2 (Opsional)</label>
                                    <select class="form-select @error('prodi_2') is-invalid @enderror" name="prodi_2">
                                        <option value="">Pilih Program Studi</option>
                                        @foreach($prodis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ old('prodi_2') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('prodi_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pilihan 3 (Opsional)</label>
                                    <select class="form-select @error('prodi_3') is-invalid @enderror" name="prodi_3">
                                        <option value="">Pilih Program Studi</option>
                                        @foreach($prodis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ old('prodi_3') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('prodi_3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data Pribadi -->
                            <h4 class="mb-3">Data Pribadi</h4>
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Jenis Kelamin</label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" required>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">NIK</label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik') }}" required maxlength="16">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Agama</label>
                                    <select class="form-select @error('agama') is-invalid @enderror" name="agama" required>
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kontak -->
                            <h4 class="mb-3">Informasi Kontak</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">No. HP/WhatsApp</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Alamat Lengkap</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Pendidikan -->
                            <h4 class="mb-3">Data Pendidikan Terakhir</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Asal Sekolah</label>
                                    <input type="text" class="form-control @error('asal_sekolah') is-invalid @enderror" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required>
                                    @error('asal_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tahun Lulus</label>
                                    <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" name="tahun_lulus" value="{{ old('tahun_lulus') }}" min="2000" max="{{ date('Y') + 1 }}" required>
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data Orang Tua -->
                            <h4 class="mb-3">Data Orang Tua/Wali</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Nama Ayah</label>
                                    <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" name="nama_ayah" value="{{ old('nama_ayah') }}" required>
                                    @error('nama_ayah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Nama Ibu</label>
                                    <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" name="nama_ibu" value="{{ old('nama_ibu') }}" required>
                                    @error('nama_ibu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <h4 class="mb-3">Akun Login</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    <small class="form-hint">Minimal 6 karakter</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- Persetujuan -->
                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="agreement" required>
                                    <span class="form-check-label">
                                        Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan. 
                                        Saya bersedia menerima sanksi apabila dikemudian hari terbukti data yang saya berikan tidak benar.
                                    </span>
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('root.home-index') }}" class="btn btn-secondary">
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
                                    Daftar Sekarang
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodeSelect = document.getElementById('periode_id');
    const jalurSelect = document.getElementById('jalur_id');

    function loadJalur(periodeId, selectedJalurId = null) {
        if (!periodeId) {
            jalurSelect.innerHTML = '<option value="">Pilih Jalur</option>';
            return;
        }

        jalurSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`{{ url('api/pmb/jalurs') }}/${periodeId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                jalurSelect.innerHTML = '<option value="">Pilih Jalur</option>';
                
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(jalur => {
                        const selected = selectedJalurId && selectedJalurId == jalur.id ? 'selected' : '';
                        jalurSelect.innerHTML += `<option value="${jalur.id}" ${selected}>${jalur.name}</option>`;
                    });
                } else {
                    jalurSelect.innerHTML = '<option value="">Tidak ada jalur tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error loading jalur:', error);
                jalurSelect.innerHTML = '<option value="">Error loading data</option>';
            });
    }

    if (periodeSelect && jalurSelect) {
        // Load jalur on page load if periode is already selected
        const initialPeriodeId = periodeSelect.value;
        const initialJalurId = '{{ old("jalur_id") }}';
        
        if (initialPeriodeId) {
            loadJalur(initialPeriodeId, initialJalurId);
        }

        // Load jalur when periode changes
        periodeSelect.addEventListener('change', function() {
            loadJalur(this.value);
        });
    }
});
</script>
@endsection
