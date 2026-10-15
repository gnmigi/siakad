@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
        }

        .nav-tabs .nav-link {
            color: #4b6cb7;
        }

        .nav-tabs .nav-link.active {
            color: #182848;
            font-weight: 600;
        }

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

        .alert {
            margin-bottom: 1rem;
        }

        .alert-dismissible {
            padding-right: 3rem;
        }

        .alert-dismissible .btn-close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 1.25rem;
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
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9 d-flex flex-column">
                <div class="card-body">
                    <h2 class="mb-4">Profile Mahasantri</h2>
                    <p class="text-muted mb-3">Lengkapi data profile Anda. Field yang wajib diisi ditandai dengan <span class="text-danger">*</span></p>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex">
                                <div class="text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                </div>
                                <div class="ms-2">
                                    <h4 class="alert-title">Berhasil!</h4>
                                    <div class="text-muted">{{ session('success') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex">
                                <div class="text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="ms-2">
                                    <h4 class="alert-title">Oops!</h4>
                                    <div class="text-muted">{{ session('error') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex">
                                <div class="text-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 9h.01"></path>
                                        <path d="M11 12h1v4h1"></path>
                                    </svg>
                                </div>
                                <div class="ms-2">
                                    <h4 class="alert-title">Perhatian</h4>
                                    <div class="text-muted">
                                        <p class="mb-2">Mohon lengkapi data yang wajib diisi:</p>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <p class="mt-2 mb-0"><small>Field yang wajib diisi ditandai dengan tanda <span class="text-danger">*</span></small></p>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route($spref . 'profile-handle') }}" method="POST" enctype="multipart/form-data" id="profileForm">
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
                                        <input type="text" class="form-control" name="bio_placebirth" value="{{ old('bio_placebirth', $mahasiswa->bio_placebirth) }}" placeholder="Contoh: Jakarta">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="bio_datebirth" value="{{ old('bio_datebirth', $mahasiswa->bio_datebirth) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Agama</label>
                                        <input type="text" class="form-control" name="bio_religion" value="{{ old('bio_religion', $mahasiswa->bio_religion) }}" placeholder="Contoh: Islam">
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Kewarganegaraan</label>
                                        <input type="text" class="form-control" name="bio_nationality" value="{{ old('bio_nationality', $mahasiswa->bio_nationality) }}" placeholder="Contoh: Indonesia">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Golongan Darah</label>
                                        <input type="text" class="form-control" name="bio_blood" value="{{ old('bio_blood', $mahasiswa->bio_blood) }}" placeholder="Contoh: A, B, AB, O">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tinggi (cm)</label>
                                        <input type="number" class="form-control" name="bio_height" value="{{ old('bio_height', $mahasiswa->bio_height) }}" placeholder="Contoh: 170">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Berat (kg)</label>
                                        <input type="number" class="form-control" name="bio_weight" value="{{ old('bio_weight', $mahasiswa->bio_weight) }}" placeholder="Contoh: 65">
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information Tab -->
                            <div class="tab-pane fade" id="academic" role="tabpanel">
                                <h3 class="card-title">Data Akademik</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">NIM</label>
                                        <input type="text" class="form-control @error('nim') is-invalid @enderror" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
                                        @error('nim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Program Studi</label>
                                        <select class="form-select @error('prodi_id') is-invalid @enderror" name="prodi_id" required>
                                            <option value="">Pilih Program Studi</option>
                                            @foreach($prodis as $prodi)
                                                <option value="{{ $prodi->id }}" {{ old('prodi_id', $mahasiswa->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                                    {{ $prodi->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('prodi_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Kelas</label>
                                        <select class="form-select" name="kelas_id">
                                            <option value="">Pilih Kelas</option>
                                            @foreach($kelas as $k)
                                                <option value="{{ $k->id }}" {{ old('kelas_id', $mahasiswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                                    {{ $k->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required-field">Tahun Akademik</label>
                                        <select class="form-select @error('taka_id') is-invalid @enderror" name="taka_id" required>
                                            <option value="">Pilih Tahun Akademik</option>
                                            @foreach($takas as $taka)
                                                <option value="{{ $taka->id }}" {{ old('taka_id', $mahasiswa->taka_id) == $taka->id ? 'selected' : '' }}>
                                                    {{ $taka->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('taka_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required-field">Semester</label>
                                        <select class="form-select @error('semester') is-invalid @enderror" name="semester" required>
                                            <option value="">Pilih Semester</option>
                                            @for($i = 1; $i <= 14; $i++)
                                                <option value="{{ $i }}" {{ old('semester', $mahasiswa->semester) == $i ? 'selected' : '' }}>
                                                    Semester {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <h3 class="card-title">Informasi Kontak</h3>
                                <h4 class="card-title mt-4">Social Media</h4>
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">Alamat KTP</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Alamat</label>
                                            <textarea class="form-control" name="ktp_addres" rows="3">{{ old('ktp_addres', $mahasiswa->ktp_addres) }}</textarea>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">RT</label>
                                                <input type="text" class="form-control" name="ktp_rt" value="{{ old('ktp_rt', $mahasiswa->ktp_rt) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">RW</label>
                                                <input type="text" class="form-control" name="ktp_rw" value="{{ old('ktp_rw', $mahasiswa->ktp_rw) }}">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Kelurahan</label>
                                                <input type="text" class="form-control" name="ktp_village" value="{{ old('ktp_village', $mahasiswa->ktp_village) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kecamatan</label>
                                                <input type="text" class="form-control" name="ktp_subdistrict" value="{{ old('ktp_subdistrict', $mahasiswa->ktp_subdistrict) }}">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-4">
                                                <label class="form-label">Kota</label>
                                                <input type="text" class="form-control" name="ktp_city" value="{{ old('ktp_city', $mahasiswa->ktp_city) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Provinsi</label>
                                                <input type="text" class="form-control" name="ktp_province" value="{{ old('ktp_province', $mahasiswa->ktp_province) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kode Pos</label>
                                                <input type="text" class="form-control" name="ktp_poscode" value="{{ old('ktp_poscode', $mahasiswa->ktp_poscode) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="card-title">Alamat Domisili</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Sama dengan KTP</label>
                                            <select class="form-select" name="domicile_same" id="domicile_same">
                                                <option value="Yes" {{ old('domicile_same', $mahasiswa->domicile_same) == 'Yes' ? 'selected' : '' }}>Ya</option>
                                                <option value="No" {{ old('domicile_same', $mahasiswa->domicile_same) == 'No' ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                        </div>
                                        <div id="domicile_fields">
                                            <div class="mb-3">
                                                <label class="form-label">Alamat</label>
                                                <textarea class="form-control" name="domicile_addres" rows="3">{{ old('domicile_addres', $mahasiswa->domicile_addres) }}</textarea>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">RT</label>
                                                    <input type="text" class="form-control" name="domicile_rt" value="{{ old('domicile_rt', $mahasiswa->domicile_rt) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">RW</label>
                                                    <input type="text" class="form-control" name="domicile_rw" value="{{ old('domicile_rw', $mahasiswa->domicile_rw) }}">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Kelurahan</label>
                                                    <input type="text" class="form-control" name="domicile_village" value="{{ old('domicile_village', $mahasiswa->domicile_village) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Kecamatan</label>
                                                    <input type="text" class="form-control" name="domicile_subdistrict" value="{{ old('domicile_subdistrict', $mahasiswa->domicile_subdistrict) }}">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Kota</label>
                                                    <input type="text" class="form-control" name="domicile_city" value="{{ old('domicile_city', $mahasiswa->domicile_city) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Provinsi</label>
                                                    <input type="text" class="form-control" name="domicile_province" value="{{ old('domicile_province', $mahasiswa->domicile_province) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Kode Pos</label>
                                                    <input type="text" class="form-control" name="domicile_poscode" value="{{ old('domicile_poscode', $mahasiswa->domicile_poscode) }}">
                                                </div>
                                            </div>
                                        </div>
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
                                            <input type="text" class="form-control" name="parent_father_name" value="{{ old('parent_father_name', $mahasiswa->parent_father_name) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pekerjaan Ayah</label>
                                            <input type="text" class="form-control" name="parent_father_job" value="{{ old('parent_father_job', $mahasiswa->parent_father_job) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Penghasilan Ayah</label>
                                            <select class="form-select" name="parent_father_income">
                                                <option value="">Pilih Penghasilan</option>
                                                <option value="< 1 Juta" {{ old('parent_father_income', $mahasiswa->parent_father_income) == '< 1 Juta' ? 'selected' : '' }}>< 1 Juta</option>
                                                <option value="1-3 Juta" {{ old('parent_father_income', $mahasiswa->parent_father_income) == '1-3 Juta' ? 'selected' : '' }}>1-3 Juta</option>
                                                <option value="3-5 Juta" {{ old('parent_father_income', $mahasiswa->parent_father_income) == '3-5 Juta' ? 'selected' : '' }}>3-5 Juta</option>
                                                <option value="> 5 Juta" {{ old('parent_father_income', $mahasiswa->parent_father_income) == '> 5 Juta' ? 'selected' : '' }}>> 5 Juta</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="card-title">Data Ibu</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Ibu</label>
                                            <input type="text" class="form-control" name="parent_mother_name" value="{{ old('parent_mother_name', $mahasiswa->parent_mother_name) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pekerjaan Ibu</label>
