@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        .form-label {
            font-weight: 500;
            color: #4b6cb7;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .avatars {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="row g-0">
            <div class="col-12 col-md-3 border-end">
                <div class="card-body">
                    <h4 class="subheader">Profile Settings</h4>
                    <div class="list-group list-group-transparent" id="profileTabs" role="tablist">
                        <a class="list-group-item list-group-item-action d-flex align-items-center active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab">
                            <i class="fas fa-user me-2"></i> Data Pribadi
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="academic-tab" data-bs-toggle="tab" href="#academic" role="tab">
                            <i class="fas fa-graduation-cap me-2"></i> Data Akademik
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab">
                            <i class="fas fa-address-book me-2"></i> Kontak
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="address-tab" data-bs-toggle="tab" href="#address" role="tab">
                            <i class="fas fa-map-marker-alt me-2"></i> Alamat
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="parent-tab" data-bs-toggle="tab" href="#parent" role="tab">
                            <i class="fas fa-users me-2"></i> Data Orang Tua
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="education-tab" data-bs-toggle="tab" href="#education" role="tab">
                            <i class="fas fa-school me-2"></i> Pendidikan
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="identity-tab" data-bs-toggle="tab" href="#identity" role="tab">
                            <i class="fas fa-id-card me-2"></i> Identitas
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="password-tab" data-bs-toggle="tab" href="#password" role="tab">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9 d-flex flex-column">
                <div class="card-body">
                    <h2 class="mb-4">Profile Mahasantri</h2>
                    <p class="text-muted mb-3">Lengkapi data profile Anda. Field yang wajib diisi ditandai dengan <span class="text-danger">*</span></p>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Mohon lengkapi data yang wajib diisi:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route($spref . 'profile-handle') }}" method="POST" enctype="multipart/form-data" id="profileForm" novalidate>
                        @csrf
                        @method('PATCH')

                        <div class="tab-content" id="profileTabsContent">
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <h3 class="card-title">Data Pribadi</h3>
                                <div class="row align-items-center mb-4">
                                    <div class="col-auto">
                                        <img src="{{ $mahasiswa->photo }}" alt="Profile Photo" class="avatars" id="preview-image">
                                    </div>
                                    <div class="col-auto">
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="photo-input" accept="image/*">
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted mt-1 d-block">Format: JPG, PNG, JPEG | Max: 2MB</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Nama Lengkap</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $mahasiswa->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $mahasiswa->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $mahasiswa->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" name="bio_gender">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ old('bio_gender', $mahasiswa->bio_gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('bio_gender', $mahasiswa->bio_gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" name="bio_placebirth" value="{{ old('bio_placebirth', $mahasiswa->bio_placebirth) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="bio_datebirth" value="{{ old('bio_datebirth', $mahasiswa->bio_datebirth) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Agama</label>
                                        <input type="text" class="form-control" name="bio_religion" value="{{ old('bio_religion', $mahasiswa->bio_religion) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information Tab -->
                            <div class="tab-pane fade" id="academic" role="tabpanel">
                                <h3 class="card-title">Data Akademik</h3>
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <div class="text-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                <path d="M12 9h.01"></path>
                                                <path d="M11 12h1v4h1"></path>
                                            </svg>
                                        </div>
                                        <div class="ms-2">
                                            <h4 class="alert-title">Informasi Penting</h4>
                                            <div class="text-muted">Data akademik hanya dapat diubah oleh admin melalui sistem manajemen data akademik mahasiswa.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Informasi Akademik</h5>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td class="text-muted">NIM</td>
                                                        <td><strong>{{ $mahasiswa->numb_nim ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Program Studi</td>
                                                        <td><strong>{{ $mahasiswa->prodi->name ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Kelas</td>
                                                        <td><strong>
                                                            @if($mahasiswa->kelas)
                                                                {{ $mahasiswa->kelas->name }} - {{ $mahasiswa->kelas->jenisKelas->name ?? 'Reguler' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Status Akademik</h5>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td class="text-muted">Tahun Akademik</td>
                                                        <td><strong>{{ $mahasiswa->kelas->taka->name ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Semester</td>
                                                        <td><strong>{{ $mahasiswa->semester ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Takhasus</td>
                                                        <td><strong>{{ $mahasiswa->takhasus ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Status</td>
                                                        <td>
                                                            <span class="badge bg-success text-white">{{ $mahasiswa->type ?? 'Aktif' }}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <h3 class="card-title">Informasi Kontak</h3>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="link_ig" value="{{ old('link_ig', $mahasiswa->link_ig) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="link_fb" value="{{ old('link_fb', $mahasiswa->link_fb) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="link_in" value="{{ old('link_in', $mahasiswa->link_in) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information Tab -->
                            <div class="tab-pane fade" id="address" role="tabpanel">
                                <h3 class="card-title">Informasi Alamat</h3>
                                <div class="mb-3">
                                    <label class="form-label">Alamat KTP</label>
                                    <textarea class="form-control" name="ktp_addres" rows="3">{{ old('ktp_addres', $mahasiswa->ktp_addres) }}</textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Kota</label>
                                        <input type="text" class="form-control" name="ktp_city" value="{{ old('ktp_city', $mahasiswa->ktp_city) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" class="form-control" name="ktp_province" value="{{ old('ktp_province', $mahasiswa->ktp_province) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Parent Information Tab -->
                            <div class="tab-pane fade" id="parent" role="tabpanel">
                                <h3 class="card-title">Data Orang Tua</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">Data Ayah</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Ayah</label>
                                            <input type="text" class="form-control" name="father_name" value="{{ old('father_name', $mahasiswa->father_name) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pekerjaan Ayah</label>
                                            <input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation', $mahasiswa->father_occupation) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Telepon Ayah</label>
                                            <input type="text" class="form-control" name="father_phone" value="{{ old('father_phone', $mahasiswa->father_phone) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Penghasilan Ayah</label>
                                            <input type="text" class="form-control" name="father_income" value="{{ old('father_income', $mahasiswa->father_income) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="card-title">Data Ibu</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Ibu</label>
                                            <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name', $mahasiswa->mother_name) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pekerjaan Ibu</label>
                                            <input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation', $mahasiswa->mother_occupation) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Telepon Ibu</label>
                                            <input type="text" class="form-control" name="mother_phone" value="{{ old('mother_phone', $mahasiswa->mother_phone) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Penghasilan Ibu</label>
                                            <input type="text" class="form-control" name="mother_income" value="{{ old('mother_income', $mahasiswa->mother_income) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education Information Tab -->
                            <div class="tab-pane fade" id="education" role="tabpanel">
                                <h3 class="card-title">Riwayat Pendidikan</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jenjang Pendidikan</label>
                                        <select class="form-select" name="edu1_type">
                                            <option value="">Pilih Jenjang Pendidikan</option>
                                            <option value="SMA/SMK" {{ old('edu1_type', $mahasiswa->edu1_type) == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                            <option value="Pesantren" {{ old('edu1_type', $mahasiswa->edu1_type) == 'Pesantren' ? 'selected' : '' }}>Pesantren</option>
                                            <option value="Diploma" {{ old('edu1_type', $mahasiswa->edu1_type) == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                            <option value="Sarjana" {{ old('edu1_type', $mahasiswa->edu1_type) == 'Sarjana' ? 'selected' : '' }}>Sarjana</option>
                                            <option value="Magister" {{ old('edu1_type', $mahasiswa->edu1_type) == 'Magister' ? 'selected' : '' }}>Magister</option>
                                            <option value="Doktor" {{ old('edu1_type', $mahasiswa->edu1_type) == 'Doktor' ? 'selected' : '' }}>Doktor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Sekolah/Institusi</label>
                                        <input type="text" class="form-control" name="edu_school_name" value="{{ old('edu_school_name', $mahasiswa->edu_school_name) }}">
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Jurusan</label>
                                        <input type="text" class="form-control" name="edu_school_major" value="{{ old('edu_school_major', $mahasiswa->edu_school_major) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Lulus</label>
                                        <input type="text" class="form-control" name="edu_school_graduate_year" value="{{ old('edu_school_graduate_year', $mahasiswa->edu_school_graduate_year) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Identity Information Tab -->
                            <div class="tab-pane fade" id="identity" role="tabpanel">
                                <h3 class="card-title">Data Identitas</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor KTP</label>
                                        <input type="text" class="form-control" name="numb_ktp" value="{{ old('numb_ktp', $mahasiswa->numb_ktp) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor NISN</label>
                                        <input type="text" class="form-control" name="numb_nisn" value="{{ old('numb_nisn', $mahasiswa->numb_nisn) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <h3 class="card-title mb-2">Ubah Password</h3>
                                <p class="text-muted small mb-4">Kosongkan jika Anda tidak ingin mengubah password.</p>
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Password Sekarang</label>
                                        <div class="input-group input-group-flat mb-2">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" placeholder="Masukkan password saat ini" autocomplete="new-password">
                                            <span class="input-group-text">
                                                <a href="javascript:void(0)" class="link-secondary toggle-password" title="Tampilkan sandi" data-bs-toggle="tooltip">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                </a>
                                            </span>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Password Baru</label>
                                        <div class="input-group input-group-flat mb-2">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password">
                                            <span class="input-group-text">
                                                <a href="javascript:void(0)" class="link-secondary toggle-password" title="Tampilkan sandi" data-bs-toggle="tooltip">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                </a>
                                            </span>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <div class="input-group input-group-flat">
                                            <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password">
                                            <span class="input-group-text">
                                                <a href="javascript:void(0)" class="link-secondary toggle-password" title="Tampilkan sandi" data-bs-toggle="tooltip">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent mt-4">
                            <div class="btn-list justify-content-end">
                                <button type="button" class="btn" onclick="window.history.back()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            const photoInput = document.getElementById('photo-input');
            const previewImage = document.getElementById('preview-image');

            if (photoInput && previewImage) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Handle tab navigation with persistence
            const activeTab = localStorage.getItem('activeProfileTab');
            if (activeTab) {
                const tabLink = document.querySelector(`[href="${activeTab}"]`);
                if (tabLink) {
                    // Remove active from defaults
                    document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('show', 'active'));
                    document.querySelectorAll('.list-group-item').forEach(item => item.classList.remove('active'));
                    
                    // Activate saved tab
                    tabLink.classList.add('active');
                    const target = document.querySelector(activeTab);
                    if (target) target.classList.add('show', 'active');
                }
            }

            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const target = document.querySelector(targetId);
                    
                    // Remove active class from all tabs and links
                    document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('show', 'active'));
                    document.querySelectorAll('.list-group-item').forEach(item => item.classList.remove('active'));
                    
                    // Add active class to current tab and link
                    this.classList.add('active');
                    target.classList.add('show', 'active');
                    
                    // Save to localStorage
                    localStorage.setItem('activeProfileTab', targetId);
                });
            });

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const input = this.closest('.input-group').querySelector('input');
                    if (input) {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        
                        const icon = this.querySelector('svg');
                        if (type === 'password') {
                            icon.innerHTML = `
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            `;
                        } else {
                            icon.innerHTML = `
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                <path d="M3 3l18 18" />
                            `;
                        }
                    }
                });
            });

            // Form submit loading state
            const profileForm = document.getElementById('profileForm');
            if (profileForm) {
                profileForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                    }
                });
            }
        });
    </script>
@endsection
