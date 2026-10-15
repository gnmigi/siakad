@extends('core-themes.core-mainpage')
@section('content')
    <div class="login-container">
        <div class="login-half-bg" style="background-image: url('{{ $webs->signin_background_image }}');"></div>
        
        <div class="login-form-container">
            <div class="container-tight">
                <div class="text-center mb-4">
                    <a href="{{ route('root.home-index') }}" aria-label="Ma'al Amin" class="navbar-brand navbar-brand-autodark">
                        <img src="{{ $webs->school_logo_hori }}" style="max-height: 64px; max-width: 200px; height: auto; width: auto; object-fit: contain;" alt="Ma'al Amin Logo">
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">🔐 Lupa Password</h2>
                        <p class="text-center text-secondary mb-4">
                            Masukkan email Anda untuk menerima kode verifikasi reset password
                        </p>
                        
                        @include('sweetalert::alert')
                        
                        <form action="{{ route('auth.handle-forgot-password') }}" method="post" autocomplete="on" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">📧 Email</label>
                                <input type="email" class="form-control" name="email" 
                                       placeholder="Masukkan email Anda..." 
                                       value="{{ old('email') }}" 
                                       autocomplete="email" required />
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">
                                    Sistem akan mendeteksi otomatis apakah Anda dosen atau mahasiswa berdasarkan email.
                                </small>
                            </div>

                            @if($webs->enable_captcha == true || $webs->enable_captcha == 1 || $webs->enable_captcha == "1")
                            <div class="mb-3">
                                <label class="form-label">🛡️ Verifikasi Anti-Bot</label>
                                <x-turnstile-widget theme="auto" language="id"/>
                                @error('cf-turnstile-response')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @endif

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    📨 Kirim Kode Verifikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center text-secondary mt-3">
                    Sudah ingat password? 
                    <a href="{{ route('auth.render-signin') }}" tabindex="-1">Login di sini</a>
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
    // Prevent back button cache issues
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    
    // Handle browser back button
    window.addEventListener('popstate', function(event) {
        // Redirect to signin page when back button is pressed
        window.location.replace("{{ route('auth.render-signin') }}");
    });
    
    // Push a state to handle back button
    if (window.history && window.history.pushState) {
        window.history.pushState(null, null, window.location.href);
    }
});
</script>
@endsection
