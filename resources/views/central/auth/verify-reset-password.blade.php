@extends('core-themes.core-mainpage')

@section('content')
<div class="login-container">
    <div class="login-half-bg" style="background-image: url('{{ $webs->signin_background_image }}');"></div>
    
    <div class="login-form-container">
        <div class="container-tight">
            <div class="text-center mb-4">
                <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ $webs->school_logo_hori }}" style="max-height: 64px; max-width: 200px; height: auto; width: auto; object-fit: contain;" alt="Logo">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg text-primary">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="m7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <h2 class="h2 text-center">Reset Password</h2>
                        <p class="text-secondary mb-2">
                            Kami telah mengirim kode verifikasi 6 digit ke:
                        </p>
                        <p class="text-primary fw-bold mb-0">{{ $email }}</p>
                        
                        <div class="countdown-info mt-3">
                            <small class="text-muted">
                                Sisa waktu: <span id="countdown" class="text-primary fw-bold">10 menit</span>
                            </small>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('auth.verify-reset-password') }}" method="POST" id="verifyForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <div class="mb-3">
                            <label class="form-label">🔢 Kode Verifikasi</label>
                            <input type="text" 
                                   class="form-control verification-input text-center" 
                                   name="verification_code" 
                                   placeholder="0 0 0 0 0 0"
                                   maxlength="6" 
                                   pattern="[0-9]{6}"
                                   autocomplete="off"
                                   required>
                            <small class="text-muted">Masukkan kode 6 digit yang dikirim ke email Anda</small>
                            @error('verification_code')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">🔐 Password Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" 
                                       class="form-control" 
                                       name="password" 
                                       placeholder="Masukkan password baru..."
                                       minlength="6"
                                       required>
                                <span class="input-group-text">
                                    <a href="javascript:void(0)" class="link-secondary toggle-password" title="Tampilkan password">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            @error('password')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">🔐 Konfirmasi Password Baru</label>
                            <input type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   placeholder="Konfirmasi password baru..."
                                   minlength="6"
                                   required>
                            @error('password_confirmation')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                ✅ Reset Password
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <div class="resend-section" style="display: none;">
                            <form action="{{ route('auth.resend-reset-code') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <button type="submit" class="btn btn-link p-0 text-decoration-none">
                                    Tidak menerima kode?
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-secondary mt-3">
                <a href="{{ route('auth.render-signin') }}" class="text-decoration-none">
                    ← Kembali ke halaman login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-css')
<style>
    .login-container {
        display: flex;
        min-height: 100vh;
        position: relative;
        background: var(--tblr-bg-surface);
    }
    
    .login-half-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 0;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.08);
    }
    
    @media (min-width: 992px) {
        .login-half-bg {
            border-radius: 20px 0 0 20px;
        }
    }
    
    .login-form-container {
        width: 50%;
        margin-left: 50%;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: var(--tblr-bg-surface);
        z-index: 1;
    }
    
    .container-tight {
        max-width: 500px;
        width: 100%;
    }
    
    .verification-input {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.5rem !important;
        padding: 1rem !important;
        border: 2px solid var(--tblr-border-color) !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
        background: var(--tblr-bg-surface-secondary) !important;
        color: var(--tblr-body-color) !important;
    }
    
    .verification-input:focus {
        border-color: var(--tblr-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--tblr-primary-rgb), 0.25) !important;
        background: var(--tblr-bg-surface) !important;
    }
    
    /* Dark mode specific styles */
    [data-bs-theme="dark"] .verification-input {
        background: var(--tblr-dark) !important;
        border-color: var(--tblr-border-color-dark) !important;
        color: var(--tblr-body-color) !important;
    }
    
    [data-bs-theme="dark"] .verification-input:focus {
        background: var(--tblr-bg-surface-dark) !important;
        border-color: var(--tblr-primary) !important;
    }
    
    .countdown-info {
        padding: 0.75rem 1rem;
        background: rgba(var(--tblr-primary-rgb), 0.1);
        border-radius: 8px;
        border: 1px solid rgba(var(--tblr-primary-rgb), 0.2);
        display: inline-block;
        margin: 0 auto;
    }
    
    /* Responsive styles */
    @media (max-width: 991.98px) {
        .login-container {
            flex-direction: column;
        }
        
        .login-half-bg {
            position: relative;
            width: 100%;
            height: 250px;
            border-radius: 20px 20px 0 0;
            box-shadow: none;
            margin-top: 1rem;
        }
        
        .login-form-container {
            width: 100%;
            margin-left: 0;
            min-height: auto;
            padding: 1.5rem;
            margin-top: -20px;
        }
        
        .container-tight {
            margin-top: -30px;
        }
        
        .card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .verification-input {
            font-size: 1.25rem !important;
            letter-spacing: 0.3rem !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .login-half-bg {
            height: 200px;
            border-radius: 15px 15px 0 0;
        }
        
        .login-form-container {
            padding: 1rem;
        }
        
        .card {
            border-radius: 15px;
        }
        
        .verification-input {
            font-size: 1.1rem !important;
            letter-spacing: 0.2rem !important;
            padding: 0.75rem !important;
        }
        
        .countdown-info {
            padding: 0.5rem 0.75rem;
        }
    }
</style>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer - Fixed calculation
    let timeLeft = 600; // 10 minutes in seconds
    const countdownElement = document.getElementById('countdown');
    const countdownContainer = document.querySelector('.countdown-info');
    const resendContainer = document.querySelector('.resend-section');
    
    function updateCountdown() {
        if (timeLeft <= 0) {
            countdownContainer.style.display = 'none';
            resendContainer.style.display = 'block';
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownElement.textContent = `${minutes} menit ${seconds.toString().padStart(2, '0')} detik`;
        timeLeft--;
    }
    
    // Update countdown every second
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
    
    // Auto-format verification code input
    const verificationInput = document.querySelector('.verification-input');
    if (verificationInput) {
        verificationInput.addEventListener('input', function(e) {
            // Only allow numbers
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 digits
            if (value.length > 6) {
                value = value.slice(0, 6);
            }
            
            this.value = value;
        });
        
        verificationInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
            this.value = numbers;
        });
    }
    
    // Toggle password visibility
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('input[name="password"]');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('svg');
            if (type === 'password') {
                icon.innerHTML = `
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                `;
            } else {
                icon.innerHTML = `
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    <path d="M3 3l18 18" />
                `;
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('verifyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const verificationCode = verificationInput.value;
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirmation = document.querySelector('input[name="password_confirmation"]').value;
            
            if (verificationCode.length !== 6) {
                e.preventDefault();
                alert('Kode verifikasi harus 6 digit');
                verificationInput.focus();
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter');
                document.querySelector('input[name="password"]').focus();
                return;
            }
            
            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok');
                document.querySelector('input[name="password_confirmation"]').focus();
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Memproses...';
        });
    }
});
</script>
@endsection
