@extends('central.backpage.core-backpage-dosen')

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

        @media (max-width: 767.98px) {
            #profileTabs {
                border-bottom: 1px solid var(--tblr-border-color);
                padding-bottom: 8px;
            }
            #profileTabs .list-group-item {
                border: 1px solid var(--tblr-border-color);
                border-radius: 20px !important;
                margin-right: 8px;
                padding: 6px 16px;
                white-space: nowrap;
            }
            #profileTabs .list-group-item.active {
                background-color: #4b6cb7 !important;
                color: white !important;
                border-color: #4b6cb7;
            }
        }
    </style>
@endsection

@section('content')
    <div class="card">


        <div class="row g-0">
            <div class="col-12 col-md-3 border-end">
                <div class="card-body">
                    <h4 class="subheader d-none d-md-block">Profile Settings</h4>
                    <div class="list-group list-group-transparent flex-row flex-md-column overflow-auto flex-nowrap mb-3 mb-md-0" id="profileTabs" role="tablist">
                        <a class="list-group-item list-group-item-action d-flex align-items-center active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab">
                            <i class="fas fa-user me-2"></i> Personal Info
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab">
                            <i class="fas fa-address-book me-2"></i> Contact Info
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="address-tab" data-bs-toggle="tab" href="#address" role="tab">
                            <i class="fas fa-map-marker-alt me-2"></i> Address
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="education-tab" data-bs-toggle="tab" href="#education" role="tab">
                            <i class="fas fa-graduation-cap me-2"></i> Education
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="identity-tab" data-bs-toggle="tab" href="#identity" role="tab">
                            <i class="fas fa-id-card me-2"></i> Identity
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9 d-flex flex-column">
                <div class="card-body">
                    <h2 class="mb-4">Profile Settings</h2>
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
                                <h3 class="card-title">Profile Details</h3>
                                <div class="row align-items-center mb-4">
                                    <div class="col-auto">
                                        <img src="{{ $user == null ? '' : $user->photo }}" alt="Profile Photo" class="avatars" id="preview-image">
                                    </div>
                                    <div class="col-auto">
                                        <input type="file" class="form-control" name="photo" id="photo-input" accept="image/*">
                                    </div>
                                </div>

                                <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required-field">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" class="form-control @error('title_front') is-invalid @enderror" name="title_front" value="{{ old('title_front', $user->title_front) }}" placeholder="Contoh: Dr., Prof.">
                                    @error('title_front')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" class="form-control @error('title_behind') is-invalid @enderror" name="title_behind" value="{{ old('title_behind', $user->title_behind) }}" placeholder="Contoh: S.T., M.T.">
                                    @error('title_behind')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" name="bio_placebirth" value="{{ old('bio_placebirth', $user->bio_placebirth) }}" placeholder="Contoh: Jakarta">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="bio_datebirth" value="{{ old('bio_datebirth', $user->bio_datebirth) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" name="bio_gender">
                                            <option value="Laki-laki" {{ old('bio_gender', $user->bio_gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('bio_gender', $user->bio_gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Agama</label>
                                        <input type="text" class="form-control" name="bio_religion" value="{{ old('bio_religion', $user->bio_religion) }}" placeholder="Contoh: Islam">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kewarganegaraan</label>
                                        <input type="text" class="form-control" name="bio_nationality" value="{{ old('bio_nationality', $user->bio_nationality) }}" placeholder="Contoh: Indonesia">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Golongan Darah</label>
                                        <input type="text" class="form-control" name="bio_blood" value="{{ old('bio_blood', $user->bio_blood) }}" placeholder="Contoh: A, B, AB, O">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tinggi (cm)</label>
                                        <input type="number" class="form-control" name="bio_height" value="{{ old('bio_height', $user->bio_height) }}" placeholder="Contoh: 170">
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Berat (kg)</label>
                                        <input type="number" class="form-control" name="bio_weight" value="{{ old('bio_weight', $user->bio_weight) }}" placeholder="Contoh: 65">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <h3 class="card-title">Contact Information</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Phone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h4 class="card-title mt-4">Social Media</h4>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="link_ig" value="{{ $user->link_ig }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="link_fb" value="{{ $user->link_fb }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="link_in" value="{{ $user->link_in }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information Tab -->
                            <div class="tab-pane fade" id="address" role="tabpanel">
                                <h3 class="card-title">Address Information</h3>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">Alamat KTP</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Alamat</label>
                                            <textarea class="form-control" name="ktp_addres" rows="3">{{ $user->ktp_addres }}</textarea>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">RT</label>
                                                <input type="text" class="form-control" name="ktp_rt" value="{{ $user->ktp_rt }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">RW</label>
                                                <input type="text" class="form-control" name="ktp_rw" value="{{ $user->ktp_rw }}">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Kelurahan</label>
                                                <input type="text" class="form-control" name="ktp_village" value="{{ $user->ktp_village }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kecamatan</label>
                                                <input type="text" class="form-control" name="ktp_subdistrict" value="{{ $user->ktp_subdistrict }}">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-4">
                                                <label class="form-label">Kota</label>
                                                <input type="text" class="form-control" name="ktp_city" value="{{ $user->ktp_city }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Provinsi</label>
                                                <input type="text" class="form-control" name="ktp_province" value="{{ $user->ktp_province }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kode Pos</label>
                                                <input type="text" class="form-control" name="ktp_poscode" value="{{ $user->ktp_poscode }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="card-title">Alamat Domisili</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Sama dengan KTP</label>
                                            <select class="form-select" name="domicile_same" id="domicile_same">
                                                <option value="Yes" {{ $user->domicile_same == 'Yes' ? 'selected' : '' }}>Ya</option>
                                                <option value="No" {{ $user->domicile_same == 'No' ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                        </div>
                                        <div id="domicile_fields">
                                            <div class="mb-3">
                                                <label class="form-label">Alamat</label>
                                                <textarea class="form-control" name="domicile_addres" rows="3">{{ $user->domicile_addres }}</textarea>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">RT</label>
                                                    <input type="text" class="form-control" name="domicile_rt" value="{{ $user->domicile_rt }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">RW</label>
                                                    <input type="text" class="form-control" name="domicile_rw" value="{{ $user->domicile_rw }}">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Kelurahan</label>
                                                    <input type="text" class="form-control" name="domicile_village" value="{{ $user->domicile_village }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Kecamatan</label>
                                                    <input type="text" class="form-control" name="domicile_subdistrict" value="{{ $user->domicile_subdistrict }}">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Kota</label>
                                                    <input type="text" class="form-control" name="domicile_city" value="{{ $user->domicile_city }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Provinsi</label>
                                                    <input type="text" class="form-control" name="domicile_province" value="{{ $user->domicile_province }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Kode Pos</label>
                                                    <input type="text" class="form-control" name="domicile_poscode" value="{{ $user->domicile_poscode }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education Information Tab -->
                            <div class="tab-pane fade" id="education" role="tabpanel">
                                <h3 class="card-title">Education History</h3>
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4 class="card-title">Pendidikan 1</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis</label>
                                            <select class="form-select" name="edu1_type">
                                                <option value="SMA/SMK" {{ $user->edu1_type == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                                <option value="Diploma" {{ $user->edu1_type == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                                <option value="Sarjana" {{ $user->edu1_type == 'Sarjana' ? 'selected' : '' }}>Sarjana</option>
                                                <option value="Magister" {{ $user->edu1_type == 'Magister' ? 'selected' : '' }}>Magister</option>
                                                <option value="Doktor" {{ $user->edu1_type == 'Doktor' ? 'selected' : '' }}>Doktor</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Institusi</label>
                                            <input type="text" class="form-control" name="edu1_place" value="{{ $user->edu1_place }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jurusan</label>
                                            <input type="text" class="form-control" name="edu1_major" value="{{ $user->edu1_major }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">IPK</label>
                                            <input type="text" class="form-control" name="edu1_average_score" value="{{ $user->edu1_average_score }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Lulus</label>
                                            <input type="text" class="form-control" name="edu1_graduate_year" value="{{ $user->edu1_graduate_year }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <h4 class="card-title">Pendidikan 2</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis</label>
                                            <select class="form-select" name="edu2_type">
                                                <option value="SMA/SMK" {{ $user->edu2_type == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                                <option value="Diploma" {{ $user->edu2_type == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                                <option value="Sarjana" {{ $user->edu2_type == 'Sarjana' ? 'selected' : '' }}>Sarjana</option>
                                                <option value="Magister" {{ $user->edu2_type == 'Magister' ? 'selected' : '' }}>Magister</option>
                                                <option value="Doktor" {{ $user->edu2_type == 'Doktor' ? 'selected' : '' }}>Doktor</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Institusi</label>
                                            <input type="text" class="form-control" name="edu2_place" value="{{ $user->edu2_place }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jurusan</label>
                                            <input type="text" class="form-control" name="edu2_major" value="{{ $user->edu2_major }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">IPK</label>
                                            <input type="text" class="form-control" name="edu2_average_score" value="{{ $user->edu2_average_score }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Lulus</label>
                                            <input type="text" class="form-control" name="edu2_graduate_year" value="{{ $user->edu2_graduate_year }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <h4 class="card-title">Pendidikan 3</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis</label>
                                            <select class="form-select" name="edu3_type">
                                                <option value="SMA/SMK" {{ $user->edu3_type == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                                <option value="Diploma" {{ $user->edu3_type == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                                <option value="Sarjana" {{ $user->edu3_type == 'Sarjana' ? 'selected' : '' }}>Sarjana</option>
                                                <option value="Magister" {{ $user->edu3_type == 'Magister' ? 'selected' : '' }}>Magister</option>
                                                <option value="Doktor" {{ $user->edu3_type == 'Doktor' ? 'selected' : '' }}>Doktor</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Institusi</label>
                                            <input type="text" class="form-control" name="edu3_place" value="{{ $user->edu3_place }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jurusan</label>
                                            <input type="text" class="form-control" name="edu3_major" value="{{ $user->edu3_major }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">IPK</label>
                                            <input type="text" class="form-control" name="edu3_average_score" value="{{ $user->edu3_average_score }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Lulus</label>
                                            <input type="text" class="form-control" name="edu3_graduate_year" value="{{ $user->edu3_graduate_year }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Identity Information Tab -->
                            <div class="tab-pane fade" id="identity" role="tabpanel">
                                <h3 class="card-title">Identity Information</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nomor KK</label>
                                            <input type="text" class="form-control" name="numb_kk" value="{{ $user->numb_kk }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor KTP</label>
                                            <input type="text" class="form-control" name="numb_ktp" value="{{ $user->numb_ktp }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor NPSN</label>
                                            <input type="text" class="form-control" name="numb_npsn" value="{{ $user->numb_npsn }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nomor NIDN</label>
                                            <input type="text" class="form-control" name="numb_nidn" value="{{ $user->numb_nidn }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor NITK</label>
                                            <input type="text" class="form-control" name="numb_nitk" value="{{ $user->numb_nitk }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Staff</label>
                                            <input type="text" class="form-control" name="numb_staff" value="{{ $user->numb_staff }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent mt-4">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route($spref . 'dashboard-render') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
            // Image preview functionality with validation
            const photoInput = document.getElementById('photo-input');
            const previewImage = document.getElementById('preview-image');
            const profileForm = document.getElementById('profileForm');

            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG)');
                        e.target.value = '';
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        e.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Handle domicile address fields visibility
            const domicileSame = document.getElementById('domicile_same');
            const domicileFields = document.getElementById('domicile_fields');

            function toggleDomicileFields() {
                if (domicileSame.value === 'Yes') {
                    domicileFields.style.display = 'none';
                } else {
                    domicileFields.style.display = 'block';
                }
            }

            // Initial check
            toggleDomicileFields();

            // Add event listener
            domicileSame.addEventListener('change', toggleDomicileFields);

            // Handle tab navigation
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    
                    // Remove active class from all tabs and links
                    document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('show', 'active'));
                    document.querySelectorAll('.list-group-item').forEach(item => item.classList.remove('active'));
                    
                    // Add active class to current tab and link
                    this.classList.add('active');
                    target.classList.add('show', 'active');
                });
            });

            // Form submission with loading state
            profileForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.textContent = 'Saving...';
                submitBtn.disabled = true;
                
                // Allow form to submit normally
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }
                });
            }, 5000);
        });
    </script>
@endsection
