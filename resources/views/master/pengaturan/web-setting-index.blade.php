@extends('core-themes.core-backpage')

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

        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
        }
    </style>
@endsection

@section('content')
    @php
        $isFinance = (auth()->user() && auth()->user()->raw_type === 2);
    @endphp
    <div class="card">
        <div class="row g-0">
            <div class="col-12 col-md-3 border-end">
                <div class="card-body">
                    <h4 class="subheader">Web Settings</h4>
                    <div class="list-group list-group-transparent" id="settingsTabs" role="tablist">
                        @if(!$isFinance)
                        <a class="list-group-item list-group-item-action d-flex align-items-center active" id="identity-tab" data-bs-toggle="tab" href="#identity" role="tab">
                            <i class="fas fa-university me-2"></i> Identity
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab">
                            <i class="fas fa-address-book me-2"></i> Contact
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="social-tab" data-bs-toggle="tab" href="#social" role="tab">
                            <i class="fas fa-share-alt me-2"></i> Social Media
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="system-tab" data-bs-toggle="tab" href="#system" role="tab">
                            <i class="fas fa-cogs me-2"></i> System
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" id="email-verification-tab" data-bs-toggle="tab" href="#email-verification" role="tab">
                            <i class="fas fa-envelope-circle-check me-2"></i> Email Verification
                        </a>
                        @endif
                        <a class="list-group-item list-group-item-action d-flex align-items-center {{ $isFinance ? 'active' : '' }}" id="whatsapp-tab" data-bs-toggle="tab" href="#whatsapp" role="tab">
                            <i class="fab fa-whatsapp me-2"></i> Pengaturan WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9 d-flex flex-column">
                <div class="card-body">
                    <h2 class="mb-4">Web Settings</h2>

                    <form action="{{ route($spref . 'pengaturan.web-settings-handle') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="tab-content" id="settingsTabsContent">
                            @if(!$isFinance)
                            <!-- Identity Tab -->
                            <div class="tab-pane fade show active" id="identity" role="tabpanel">
                                <h3 class="card-title">Institution Identity</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Application Name</label>
                                        <input type="text" class="form-control" name="school_apps" value="{{ $webs->school_apps }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Institution Name</label>
                                        <input type="text" class="form-control" name="school_name" value="{{ $webs->school_name }}" required>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Head of Institution</label>
                                        <input type="text" class="form-control" name="school_head" value="{{ $webs->school_head }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Website URL</label>
                                        <input type="url" class="form-control" name="school_link" value="{{ $webs->school_link }}" required>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label required-field">Institution Description</label>
                                        <textarea class="form-control" name="school_desc" rows="4" required>{{ $webs->school_desc }}</textarea>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Vertical Logo</label>
                                        <div class="mb-2">
                                            <img src="{{ $webs->school_logo_vert }}" alt="Vertical Logo" class="logo-preview" id="preview-vert-logo">
                                        </div>
                                        <input type="file" class="form-control" name="school_logo_vert" id="vert-logo-input" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Horizontal Logo</label>
                                        <div class="mb-2">
                                            <img src="{{ $webs->school_logo_hori }}" alt="Horizontal Logo" class="logo-preview" id="preview-hori-logo">
                                        </div>
                                        <input type="file" class="form-control" name="school_logo_hori" id="hori-logo-input" accept="image/*">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Signin Background Image</label>
                                        <div class="mb-2">
                                            @if($webs->signin_background_image)
                                                <img src="{{ $webs->signin_background_image }}" alt="Signin Background" class="img-fluid" id="preview-signin-bg" style="max-height: 200px; border-radius: 10px;">
                                            @else
                                                <div class="alert alert-info" id="preview-signin-bg">
                                                    <i class="fas fa-info-circle me-2"></i>Menggunakan gambar default untuk background signin
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control" name="signin_background_image" id="signin-bg-input" accept="image/*">
                                        <small class="form-text text-muted">Upload gambar background untuk halaman signin. Maksimal 10MB. Format: JPG, PNG. Resolusi yang disarankan: 1920x1080px</small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Kop Surat</label>
                                        <div class="mb-2">
                                            @if($webs->kop_surat)
                                                <img src="{{ asset('storage/images/kop-surat/' . $webs->kop_surat) }}" alt="Kop Surat" class="img-fluid" id="preview-kop-surat" style="max-height: 200px;">
                                            @else
                                                <div class="alert alert-info" id="preview-kop-surat">
                                                    <i class="fas fa-info-circle me-2"></i>Belum ada kop surat yang diupload
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control" name="kop_surat" id="kop-surat-input" accept="image/*">
                                        <small class="form-text text-muted">Upload gambar kop surat untuk digunakan pada print KRS. Maksimal 5MB. Format: JPG, PNG</small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jabatan Penandatangan</label>
                                        <input type="text" class="form-control" name="signature_title" value="{{ $webs->signature_title ?? 'Wakil Mudir Bidang Akademik' }}" placeholder="Wakil Mudir Bidang Akademik">
                                        <small class="form-text text-muted">Jabatan yang akan muncul di print KRS</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Penandatangan</label>
                                        <input type="text" class="form-control" name="signature_name" value="{{ $webs->signature_name }}" placeholder="Nama lengkap penandatangan">
                                        <small class="form-text text-muted">Nama yang akan muncul di print KRS</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <h3 class="card-title">Contact Information</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="school_email" value="{{ $webs->school_email }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="school_phone" value="{{ $webs->school_phone }}">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="school_address" rows="3">{{ $webs->school_address }}</textarea>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Longitude</label>
                                        <input type="text" class="form-control" name="school_longitude" value="{{ $webs->school_longitude }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Latitude</label>
                                        <input type="text" class="form-control" name="school_latitude" value="{{ $webs->school_latitude }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Tab -->
                            <div class="tab-pane fade" id="social" role="tabpanel">
                                <h3 class="card-title">Social Media Links</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="social_fb" value="{{ $webs->social_fb }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="social_ig" value="{{ $webs->social_ig }}">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="social_in" value="{{ $webs->social_in }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Twitter/X</label>
                                        <input type="url" class="form-control" name="social_tw" value="{{ $webs->social_tw }}">
                                    </div>
                                </div>
                            </div>

                            <!-- System Tab -->
                            <div class="tab-pane fade" id="system" role="tabpanel">
                                <h3 class="card-title">System Settings</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Current Academic Year</label>
                                        <input type="number" class="form-control" name="taka_now" value="{{ $webs->taka_now }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Max Login Attempts</label>
                                        <input type="number" class="form-control" name="max_login_attempts" value="{{ $webs->max_login_attempts }}">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" {{ $webs->maintenance_mode ? 'checked' : '' }}>
                                            <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_captcha" id="enable_captcha" value="1" {{ $webs->enable_captcha ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_captcha">Enable Captcha</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Verification Tab -->
                            <div class="tab-pane fade" id="email-verification" role="tabpanel">
                                <h3 class="card-title">Email Verification Settings</h3>
                                <p class="text-muted">Configure email verification for student registration to prevent bot registrations.</p>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="email_verification_enabled" id="email_verification_enabled" value="1" {{ $webs->email_verification_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_verification_enabled">
                                                <strong>Enable Email Verification</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">When enabled, users will need to verify their email address with a code sent to their email.</small>
                                    </div>
                                </div>

                                <div class="row mt-3" id="email-verification-settings" style="{{ $webs->email_verification_enabled ? '' : 'display: none;' }}">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="require_email_verification_pmb" id="require_email_verification_pmb" value="1" {{ $webs->require_email_verification_pmb ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_email_verification_pmb">
                                                <strong>Require for PMB Registration</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Require email verification for new student registration (PMB).</small>
                                    </div>
                                </div>

                                <div class="row mt-3" id="verification-code-settings" style="{{ $webs->email_verification_enabled ? '' : 'display: none;' }}">
                                    <div class="col-md-4">
                                        <label class="form-label">Verification Code Length</label>
                                        <select class="form-control" name="verification_code_length">
                                            <option value="4" {{ ($webs->verification_code_length ?? 6) == 4 ? 'selected' : '' }}>4 digits</option>
                                            <option value="5" {{ ($webs->verification_code_length ?? 6) == 5 ? 'selected' : '' }}>5 digits</option>
                                            <option value="6" {{ ($webs->verification_code_length ?? 6) == 6 ? 'selected' : '' }}>6 digits</option>
                                            <option value="7" {{ ($webs->verification_code_length ?? 6) == 7 ? 'selected' : '' }}>7 digits</option>
                                            <option value="8" {{ ($webs->verification_code_length ?? 6) == 8 ? 'selected' : '' }}>8 digits</option>
                                        </select>
                                        <small class="form-text text-muted">Length of the verification code sent to email.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Code Expiry (Minutes)</label>
                                        <select class="form-control" name="verification_code_expiry_minutes">
                                            <option value="5" {{ ($webs->verification_code_expiry_minutes ?? 10) == 5 ? 'selected' : '' }}>5 minutes</option>
                                            <option value="10" {{ ($webs->verification_code_expiry_minutes ?? 10) == 10 ? 'selected' : '' }}>10 minutes</option>
                                            <option value="15" {{ ($webs->verification_code_expiry_minutes ?? 10) == 15 ? 'selected' : '' }}>15 minutes</option>
                                            <option value="30" {{ ($webs->verification_code_expiry_minutes ?? 10) == 30 ? 'selected' : '' }}>30 minutes</option>
                                            <option value="60" {{ ($webs->verification_code_expiry_minutes ?? 10) == 60 ? 'selected' : '' }}>60 minutes</option>
                                        </select>
                                        <small class="form-text text-muted">How long the verification code remains valid.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Max Attempts</label>
                                        <select class="form-control" name="max_verification_attempts">
                                            <option value="1" {{ ($webs->max_verification_attempts ?? 3) == 1 ? 'selected' : '' }}>1 attempt</option>
                                            <option value="2" {{ ($webs->max_verification_attempts ?? 3) == 2 ? 'selected' : '' }}>2 attempts</option>
                                            <option value="3" {{ ($webs->max_verification_attempts ?? 3) == 3 ? 'selected' : '' }}>3 attempts</option>
                                            <option value="5" {{ ($webs->max_verification_attempts ?? 3) == 5 ? 'selected' : '' }}>5 attempts</option>
                                            <option value="10" {{ ($webs->max_verification_attempts ?? 3) == 10 ? 'selected' : '' }}>10 attempts</option>
                                        </select>
                                        <small class="form-text text-muted">Maximum verification attempts before requiring a new code.</small>
                                    </div>
                                </div>

                                <div class="row mt-4" id="email-config-info" style="{{ $webs->email_verification_enabled ? '' : 'display: none;' }}">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h5><i class="fas fa-info-circle me-2"></i>Email Configuration Required</h5>
                                            <p class="mb-2">For email verification to work properly, make sure you have configured the following in your <code>.env</code> file:</p>
                                            <ul class="mb-0">
                                                <li><code>MAIL_HOST</code> - Your SMTP server (e.g., smtp.gmail.com)</li>
                                                <li><code>MAIL_PORT</code> - SMTP port (usually 587 for TLS)</li>
                                                <li><code>MAIL_USERNAME</code> - Your email address</li>
                                                <li><code>MAIL_PASSWORD</code> - Your email password or app password</li>
                                                <li><code>MAIL_ENCRYPTION</code> - Encryption type (tls or ssl)</li>
                                                <li><code>MAIL_FROM_ADDRESS</code> - Sender email address</li>
                                                <li><code>MAIL_FROM_NAME</code> - Sender name</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3" id="email-verification-preview" style="{{ $webs->email_verification_enabled ? '' : 'display: none;' }}">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-eye me-2"></i>Email Verification Flow Preview
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        <div class="mb-3">
                                                            <i class="fas fa-user-plus fa-3x text-primary"></i>
                                                        </div>
                                                        <h6>1. User Registration</h6>
                                                        <p class="text-muted small">User fills registration form</p>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="mb-3">
                                                            <i class="fas fa-envelope fa-3x text-warning"></i>
                                                        </div>
                                                        <h6>2. Email Sent</h6>
                                                        <p class="text-muted small">Verification code sent to email</p>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="mb-3">
                                                            <i class="fas fa-check-circle fa-3x text-success"></i>
                                                        </div>
                                                        <h6>3. Verification</h6>
                                                        <p class="text-muted small">User enters code to complete registration</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endif

                            <!-- WhatsApp Settings Tab -->
                            <div class="tab-pane fade {{ $isFinance ? 'show active' : '' }}" id="whatsapp" role="tabpanel">
                                <h3 class="card-title">Pengaturan Notifikasi WhatsApp</h3>
                                <p class="text-muted">Konfigurasikan notifikasi otomatis yang dikirimkan ke nomor WhatsApp Admin saat aksi penting dipicu oleh mahasantri.</p>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="wa_admin_payment_notification_enabled" id="wa_admin_payment_notification_enabled" value="1" {{ $webs->wa_admin_payment_notification_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="wa_admin_payment_notification_enabled">
                                                <strong>Aktifkan Notifikasi Konfirmasi Pembayaran ke Admin Keuangan</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mb-2 d-block">Kirim notifikasi ke WhatsApp Admin Keuangan ketika mahasantri mengirimkan bukti konfirmasi pembayaran.</small>
                                        
                                        <div class="mt-3 p-3 border rounded" style="background: rgba(var(--tblr-body-color-rgb), 0.03);">
                                            <label class="form-label d-flex justify-content-between align-items-center">
                                                <strong>Daftar Nomor WhatsApp Admin Keuangan</strong>
                                                <button type="button" class="btn btn-xs btn-success text-white py-1 px-2 rounded-2" onclick="addWaNumberField('wa_finance_container', 'wa_finance_numbers[]')">
                                                    <i class="fas fa-plus me-1"></i> Tambah Nomor
                                                </button>
                                            </label>
                                            
                                            <div id="wa_finance_container" class="wa-numbers-list">
                                                @php
                                                    $financeNumbers = array_filter(explode(',', $webs->wa_finance_numbers ?? ''));
                                                @endphp
                                                @forelse($financeNumbers as $num)
                                                    <div class="input-group mb-2 wa-number-item" style="max-width: 500px;">
                                                        <span class="input-group-text" style="background: transparent !important;"><i class="fab fa-whatsapp text-success"></i></span>
                                                        <input type="text" class="form-control" name="wa_finance_numbers[]" value="{{ trim($num) }}" placeholder="Contoh: 6281234567890">
                                                        <button type="button" class="btn btn-outline-danger remove-wa-number" style="background: transparent !important;"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                @empty
                                                    <div class="input-group mb-2 wa-number-item" style="max-width: 500px;">
                                                        <span class="input-group-text" style="background: transparent !important;"><i class="fab fa-whatsapp text-success"></i></span>
                                                        <input type="text" class="form-control" name="wa_finance_numbers[]" placeholder="Contoh: 6281234567890">
                                                        <button type="button" class="btn btn-outline-danger remove-wa-number" style="background: transparent !important;"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                @endforelse
                                            </div>
                                            <small class="form-text text-muted d-block mt-2">Gunakan format kode negara tanpa tanda "+" atau spasi (contoh: 628xxx).</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="wa_admin_krs_notification_enabled" id="wa_admin_krs_notification_enabled" value="1" {{ $webs->wa_admin_krs_notification_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="wa_admin_krs_notification_enabled">
                                                <strong>Aktifkan Notifikasi Pengajuan KRS ke Admin Akademik</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mb-2 d-block">Kirim notifikasi ke WhatsApp Admin Akademik ketika mahasantri melakukan submit Kartu Rencana Studi (KRS) untuk disetujui.</small>
                                        
                                        <div class="mt-3 p-3 border rounded" style="background: rgba(var(--tblr-body-color-rgb), 0.03);">
                                            <label class="form-label d-flex justify-content-between align-items-center">
                                                <strong>Daftar Nomor WhatsApp Admin Akademik</strong>
                                                <button type="button" class="btn btn-xs btn-success text-white py-1 px-2 rounded-2" onclick="addWaNumberField('wa_admin_container', 'wa_admin_numbers[]')">
                                                    <i class="fas fa-plus me-1"></i> Tambah Nomor
                                                </button>
                                            </label>
                                            
                                            <div id="wa_admin_container" class="wa-numbers-list">
                                                @php
                                                    $adminNumbers = array_filter(explode(',', $webs->wa_admin_numbers ?? ''));
                                                @endphp
                                                @forelse($adminNumbers as $num)
                                                    <div class="input-group mb-2 wa-number-item" style="max-width: 500px;">
                                                        <span class="input-group-text" style="background: transparent !important;"><i class="fab fa-whatsapp text-success"></i></span>
                                                        <input type="text" class="form-control" name="wa_admin_numbers[]" value="{{ trim($num) }}" placeholder="Contoh: 6281234567890">
                                                        <button type="button" class="btn btn-outline-danger remove-wa-number" style="background: transparent !important;"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                @empty
                                                    <div class="input-group mb-2 wa-number-item" style="max-width: 500px;">
                                                        <span class="input-group-text" style="background: transparent !important;"><i class="fab fa-whatsapp text-success"></i></span>
                                                        <input type="text" class="form-control" name="wa_admin_numbers[]" placeholder="Contoh: 6281234567890">
                                                        <button type="button" class="btn btn-outline-danger remove-wa-number" style="background: transparent !important;"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                @endforelse
                                            </div>
                                            <small class="form-text text-muted d-block mt-2">Gunakan format kode negara tanpa tanda "+" atau spasi (contoh: 628xxx).</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- WA Connection Status Card -->
                                <div class="card mt-4 border-primary">
                                    <div class="card-header bg-light">
                                        <h4 class="card-title text-primary"><i class="fab fa-whatsapp me-2"></i>Status Koneksi Gateway WhatsApp</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="wa-status-container" class="text-center py-4">
                                            <div class="spinner-border text-primary mb-3" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted">Mengecek status koneksi bot WhatsApp...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent mt-4">
                            <div class="btn-list justify-content-end">
                                <button type="button" class="btn btn-1" onclick="window.history.back()">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-2">Save Changes</button>
                            </div>
                        </div>
                    </form>

                    @if(!$isFinance)
                    <!-- Database Management Section - Moved outside main form -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Database Management</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route($spref . 'pengaturan.export-database') }}" method="GET">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i>Export Database
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route($spref . 'pengaturan.import-database') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="database_file" accept=".sql" required>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-upload me-2"></i>Import Database
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logo preview functionality
            const vertLogoInput = document.getElementById('vert-logo-input');
            const horiLogoInput = document.getElementById('hori-logo-input');
            const kopSuratInput = document.getElementById('kop-surat-input');
            const signinBgInput = document.getElementById('signin-bg-input');
            const previewVertLogo = document.getElementById('preview-vert-logo');
            const previewHoriLogo = document.getElementById('preview-hori-logo');
            const previewKopSurat = document.getElementById('preview-kop-surat');
            const previewSigninBg = document.getElementById('preview-signin-bg');

            function handleLogoPreview(input, preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            function handleKopSuratPreview(input, preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Replace the preview content with image
                            preview.innerHTML = '<img src="' + e.target.result + '" alt="Kop Surat Preview" class="img-fluid" style="max-height: 200px;">';
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (vertLogoInput && previewVertLogo) handleLogoPreview(vertLogoInput, previewVertLogo);
            if (horiLogoInput && previewHoriLogo) handleLogoPreview(horiLogoInput, previewHoriLogo);
            if (kopSuratInput && previewKopSurat) handleKopSuratPreview(kopSuratInput, previewKopSurat);
            if (signinBgInput && previewSigninBg) handleKopSuratPreview(signinBgInput, previewSigninBg);

            // Handle tab navigation
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            let waPollInterval;
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

                    // Handle WhatsApp status polling
                    if (this.id === 'whatsapp-tab') {
                        checkWhatsappStatus();
                        clearInterval(waPollInterval);
                        waPollInterval = setInterval(checkWhatsappStatus, 5000);
                    } else {
                        clearInterval(waPollInterval);
                    }
                });
            });

            // Handle email verification toggle
            const emailVerificationEnabled = document.getElementById('email_verification_enabled');
            const emailVerificationSettings = document.getElementById('email-verification-settings');
            const verificationCodeSettings = document.getElementById('verification-code-settings');
            const emailConfigInfo = document.getElementById('email-config-info');
            const emailVerificationPreview = document.getElementById('email-verification-preview');

            function toggleEmailVerificationSettings() {
                const isEnabled = emailVerificationEnabled.checked;
                const display = isEnabled ? '' : 'none';
                
                if (emailVerificationSettings) emailVerificationSettings.style.display = display;
                if (verificationCodeSettings) verificationCodeSettings.style.display = display;
                if (emailConfigInfo) emailConfigInfo.style.display = display;
                if (emailVerificationPreview) emailVerificationPreview.style.display = display;
            }

            if (emailVerificationEnabled) {
                emailVerificationEnabled.addEventListener('change', toggleEmailVerificationSettings);
                // Initialize on page load
                toggleEmailVerificationSettings();
            }

            // WhatsApp Status AJAX Polling
            const statusContainer = document.getElementById('wa-status-container');

            function checkWhatsappStatus() {
                if (!statusContainer) return;
                fetch("{{ route($spref . 'pengaturan.web-settings.whatsapp-status') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'connected') {
                            let userDetails = data.user && data.user.id ? `<br><small class="text-muted">Terhubung dengan nomor: <strong>${data.user.id.split(':')[0]}</strong></small>` : '';
                            statusContainer.innerHTML = `
                                <div class="text-success mb-3">
                                    <i class="fas fa-circle-check fa-4x text-success"></i>
                                </div>
                                <h4 class="text-success mb-1">Terhubung (Connected)</h4>
                                <p class="text-muted mb-0">Bot WhatsApp siap mengirimkan notifikasi. ${userDetails}</p>
                            `;
                        } else if (data.status === 'qr' && data.qr) {
                            statusContainer.innerHTML = `
                                <div class="mb-3">
                                    <img src="${data.qr}" alt="QR Code" class="img-thumbnail" style="max-width: 250px;">
                                </div>
                                <h4 class="text-warning mb-1">Scan QR Code untuk Menghubungkan</h4>
                                <p class="text-muted mb-0 small">Buka aplikasi WhatsApp di HP Anda -> Perangkat Tertaut -> Tautkan Perangkat, lalu scan kode di atas.<br>QR Code diperbarui otomatis.</p>
                            `;
                        } else if (data.status === 'connecting') {
                            statusContainer.innerHTML = `
                                <div class="spinner-border text-warning mb-3" role="status"></div>
                                <h4 class="text-warning mb-1">Sedang Menghubungkan...</h4>
                                <p class="text-muted mb-0">Bot sedang bersiap/menginisialisasi koneksi. Mohon tunggu...</p>
                            `;
                        } else {
                            statusContainer.innerHTML = `
                                <div class="text-danger mb-3">
                                    <i class="fas fa-triangle-exclamation fa-4x text-danger"></i>
                                </div>
                                <h4 class="text-danger mb-1">Bot Offline / Putus</h4>
                                <p class="text-muted mb-0">Server bot WhatsApp mati atau tidak dapat dijangkau dari Laravel.<br><small class="text-danger">Pastikan PM2 service <code>siakad-wa-bot</code> berjalan di VPS.</small></p>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching WA status:', error);
                        statusContainer.innerHTML = `
                            <div class="text-danger mb-3">
                                <i class="fas fa-triangle-exclamation fa-4x text-danger"></i>
                            </div>
                            <h4 class="text-danger mb-1">Gagal Menghubungi Gateway</h4>
                            <p class="text-muted mb-0">Terjadi kesalahan saat mengecek status koneksi bot WhatsApp.</p>
                        `;
                    });
            }

            // Start polling if whatsapp tab is active by default
            const activeTab = document.querySelector('.list-group-item.active');
            if (activeTab && activeTab.id === 'whatsapp-tab') {
                checkWhatsappStatus();
                waPollInterval = setInterval(checkWhatsappStatus, 5000);
            }

            // Dynamic WA number fields handler
            window.addWaNumberField = function(containerId, inputName) {
                const container = document.getElementById(containerId);
                if (!container) return;
                const div = document.createElement('div');
                div.className = 'input-group mb-2 wa-number-item';
                div.style.maxWidth = '500px';
                div.innerHTML = `
                    <span class="input-group-text" style="background: transparent !important;"><i class="fab fa-whatsapp text-success"></i></span>
                    <input type="text" class="form-control" name="${inputName}" placeholder="Contoh: 6281234567890">
                    <button type="button" class="btn btn-outline-danger remove-wa-number" style="background: transparent !important;"><i class="fas fa-trash"></i></button>
                `;
                container.appendChild(div);
            };

            // Event delegation for removing WA numbers
            document.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-wa-number');
                if (removeBtn) {
                    const item = removeBtn.closest('.wa-number-item');
                    const container = item.parentNode;
                    if (container.querySelectorAll('.wa-number-item').length > 1) {
                        item.remove();
                    } else {
                        item.querySelector('input').value = '';
                    }
                }
            });
        });
    </script>
@endsection
