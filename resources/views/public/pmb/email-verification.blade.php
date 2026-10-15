@extends('core-themes.core-mainpage')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                            <path d="M3 7l9 6l9 -6"></path>
                        </svg>
                        Verifikasi Email
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="avatar avatar-xl mx-auto mb-3" style="background-color: #e3f2fd;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl text-primary" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 18h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5"></path>
                                <path d="M3 6l9 6l9 -6"></path>
                                <path d="M15 18h6"></path>
                                <path d="M18 15l3 3l-3 3"></path>
                            </svg>
                        </div>
                        <h4>Kode Verifikasi Telah Dikirim</h4>
                        <p class="text-muted">
                            Kami telah mengirim kode verifikasi 6 digit ke email:<br>
                            <strong>{{ $email }}</strong>
                        </p>
                    </div>

                    @if(isset($status) && $status['status'] == 'pending')
                        <div class="alert alert-info">
                            <strong>Status:</strong> {{ $status['message'] }}<br>
                            <small>Sisa waktu: <span id="countdown">{{ isset($status['time_remaining']) ? round($status['time_remaining'] / 60, 1) : 10 }}</span> menit</small><br>
                            <small>Percobaan: {{ $status['attempts'] ?? 0 }}/{{ $status['max_attempts'] ?? 3 }}</small>
                        </div>
                    @endif

                    <form action="{{ route('pmb.email.verify') }}" method="POST" id="verificationForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Masukkan Kode Verifikasi</label>
                            <div class="row g-2 justify-content-center">
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code1" required>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code2" required>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code3" required>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code4" required>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code5" required>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control text-center verification-input" maxlength="1" name="code6" required>
                                </div>
                            </div>
                            <input type="hidden" name="verification_code" id="verification_code">
                            @error('verification_code')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="verifyBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l5 5l10 -10"></path>
                                </svg>
                                Verifikasi Email
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-2">Tidak menerima kode?</p>
                        <button type="button" class="btn btn-outline-primary" id="resendBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                            </svg>
                            Kirim Ulang Kode
                        </button>
                    </div>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <a href="{{ route('pmb.registration.form') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Kembali ke Form Pendaftaran
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                            <path d="M12 8l.01 0"></path>
                            <path d="M11 12l1 0l0 4l1 0"></path>
                        </svg>
                        Bantuan
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>📧 Cek folder spam/junk</strong><br>
                            <small class="text-muted">Email mungkin masuk ke folder spam</small>
                        </li>
                        <li class="mb-2">
                            <strong>⏰ Kode berlaku 10 menit</strong><br>
                            <small class="text-muted">Segera masukkan kode sebelum kedaluwarsa</small>
                        </li>
                        <li class="mb-2">
                            <strong>🔄 Maksimal 3 percobaan</strong><br>
                            <small class="text-muted">Jika gagal, minta kode baru</small>
                        </li>
                        <li>
                            <strong>📞 Butuh bantuan?</strong><br>
                            <small class="text-muted">Hubungi admin di {{ $webs->school_phone ?? '021-12345678' }}</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.verification-input');
    const verificationCodeInput = document.getElementById('verification_code');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const form = document.getElementById('verificationForm');

    // Auto-focus and move to next input
    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Move to next input
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            // Update hidden input
            updateVerificationCode();
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').slice(0, 6);
            
            digits.split('').forEach((digit, i) => {
                if (inputs[i]) {
                    inputs[i].value = digit;
                }
            });
            
            updateVerificationCode();
            
            // Focus last filled input or next empty
            const lastIndex = Math.min(digits.length - 1, inputs.length - 1);
            inputs[lastIndex].focus();
        });
    });

    function updateVerificationCode() {
        const code = Array.from(inputs).map(input => input.value).join('');
        verificationCodeInput.value = code;
        
        // Enable/disable verify button
        verifyBtn.disabled = code.length !== 6;
    }

    // Resend code functionality
    resendBtn.addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

        // Create form data for proper Laravel request
        const formData = new FormData();
        formData.append('email', '{{ $email }}');
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        fetch('{{ route("pmb.email.resend") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Clear inputs
                inputs.forEach(input => input.value = '');
                inputs[0].focus();
                updateVerificationCode();
                
                // Show success message
                showAlert('success', data.message || 'Kode verifikasi baru telah dikirim!');
                
                // Start cooldown
                startResendCooldown(btn, 60);
            } else {
                showAlert('error', data.message || 'Gagal mengirim kode verifikasi');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Resend Error:', error);
            showAlert('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    function startResendCooldown(btn, seconds) {
        let remaining = seconds;
        const originalText = btn.innerHTML;
        
        const interval = setInterval(() => {
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 7v5l3 3"></path></svg>Tunggu ${remaining}s`;
            remaining--;
            
            if (remaining < 0) {
                clearInterval(interval);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }, 1000);
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert alert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = cardBody.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Countdown timer
    @if(isset($status) && $status['status'] == 'pending' && isset($status['time_remaining']))
    let timeRemaining = {{ $status['time_remaining'] }}; // in seconds
    const countdownElement = document.getElementById('countdown');
    
    if (countdownElement && timeRemaining > 0) {
        // Convert seconds to minutes for display
        countdownElement.textContent = Math.ceil(timeRemaining / 60);
        
        const countdownInterval = setInterval(() => {
            timeRemaining--;
            
            // Update display in minutes
            const minutesLeft = Math.ceil(timeRemaining / 60);
            countdownElement.textContent = minutesLeft;
            
            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
                countdownElement.textContent = '0';
                showAlert('error', 'Kode verifikasi telah kedaluwarsa. Silakan minta kode baru.');
            }
        }, 1000); // Update every second
    }
    @endif

    // Focus first input on load
    inputs[0].focus();
});
</script>
@endsection
