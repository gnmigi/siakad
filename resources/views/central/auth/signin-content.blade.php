@extends('core-themes.core-mainpage')
@section('content')
    <div class="login-container">
        <div class="login-half-bg" style="background-image: url('{{ $webs->signin_background_image }}');"></div>
        
        <div class="login-form-container">
            <div class="container-tight">
                <div class="text-center mb-4">
                    <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                        <img src="{{ $webs->school_logo_hori }}" style="max-height: 64px; max-width: 200px; height: auto; width: auto; object-fit: contain;" alt="Maal Amin">
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Masuk ke Akun</h2>
                        <form action="{{ route('auth.handle-signin') }}" method="post" autocomplete="on" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email / Username / Nomor Telepon / NIM</label>
                                <input type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" placeholder="Email, Username, Nomor Telepon, atau NIM" autocomplete="on" />
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label">
                                    Kata Sandi
                                    <span class="form-label-description">
                                        <a href="{{ route('auth.forgot-password') }}">Lupa Sandi?</a>
                                    </span>
                                </label>
                                <div class="input-group input-group-flat">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Kata sandi Anda" autocomplete="off" />
                                    <span class="input-group-text">
                                        <a href="javascript:void(0)" class="link-secondary toggle-password" title="Tampilkan sandi" data-bs-toggle="tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" />
                                    <span class="form-check-label">Ingat saya di perangkat ini</span>
                                </label>
                            </div>
                            @if($webs->enable_captcha == true || $webs->enable_captcha == 1 || $webs->enable_captcha == "1")
                            <div class="mb-3">
                                <x-turnstile-widget theme="auto" language="id"/>
                                @error('cf-turnstile-response')
                                    <div class="text-danger mt-1">
                                        <small>{{ $message }}</small>
                                    </div>
                                @enderror
                            </div>
                            @endif
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">Masuk</button>
                            </div>
                        </form>
                    </div>
                    <!--<div class="hr-text">atau</div>-->
                    <!--<div class="card-body">-->
                    <!--    <div class="row g-2">-->
                    <!--        <div class="col-12 col-md-6">-->
                    <!--            <a href="#" class="btn btn-4 w-100">-->
                    <!--                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-github icon-2">-->
                    <!--                    <path d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5" />-->
                    <!--                </svg>-->
                    <!--                Masuk dengan Github-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--        <div class="col-12 col-md-6 mt-2 mt-md-0">-->
                    <!--            <a href="#" class="btn btn-4 w-100">-->
                    <!--                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-x icon-2">-->
                    <!--                    <path d="M4 4l11.733 16h4.267l-11.733 -16z" />-->
                    <!--                    <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />-->
                    <!--                </svg>-->
                    <!--                Masuk dengan X-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
                <!--<div class="text-center text-secondary mt-3">Belum punya akun? <a href="./sign-up.html" tabindex="-1">Daftar sekarang</a></div>-->
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
    
    /* Desktop: rounded di kiri atas dan kiri bawah */
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
    
    /* Responsive styles - Mobile */
    @media (max-width: 991.98px) {
        .login-container {
            flex-direction: column;
        }
        
        .login-half-bg {
            position: relative;
            width: 100%;
            height: 250px;
            border-radius: 20px 20px 0 0; /* Rounded top corners */
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
    }
    
    /* Smaller devices */
    @media (max-width: 767.98px) {
        .login-half-bg {
            height: 220px;
        }
        
        .login-form-container {
            padding: 1.25rem;
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
        
        .form-label-description {
            display: block;
            margin-top: 0.3rem;
        }
    }
    
    @media (max-width: 400px) {
        .login-half-bg {
            height: 180px;
            border-radius: 12px 12px 0 0;
        }
        
        .h2 {
            font-size: 1.5rem;
        }
        
        .navbar-brand img {
            max-height: 50px;
        }
        
        .card {
            border-radius: 12px;
        }
    }
    
    /* Animation effects */
    .login-container:hover .login-half-bg {
        transform: scale(1.01);
    }
</style>
@endsection

@section('custom-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('input[type="password"]');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the eye icon
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
    });

    // Automatically reload page if retrieved from Back-Forward Cache to refresh CSRF token
    window.addEventListener('pageshow', function(event) {
        var historyTraversal = event.persisted || 
                               (typeof window.performance != 'undefined' && 
                                window.performance.navigation.type === 2);
        if (historyTraversal) {
            window.location.reload();
        }
    });

    // Auto-reload after long period of inactivity when tab is focused
    let lastActiveTime = Date.now();
    const updateActivity = () => { lastActiveTime = Date.now(); };
    document.addEventListener('mousemove', updateActivity);
    document.addEventListener('keydown', updateActivity);
    
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // If idle for more than 30 minutes (1,800,000 ms), reload page to refresh CSRF token
            if (Date.now() - lastActiveTime > 1800000) {
                window.location.reload();
            }
        }
    });
</script>
@endsection
