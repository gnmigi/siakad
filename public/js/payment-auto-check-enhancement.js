/**
 * Enhanced Auto-Check for Payment Status
 * Adds more aggressive auto-checking when payment page loads
 */

// Enhanced auto-check function that runs immediately when page loads
function initializeEnhancedAutoCheck() {
    // Only run for pending or processing transactions
    if (window.transactionStatus === 'pending' || window.transactionStatus === 'processing') {
        console.log('🚀 Initializing enhanced auto-check for payment status...');

        // Show a subtle notification that auto-check is running
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🔄 Auto-Check Aktif',
                text: 'Sistem akan otomatis mengecek status pembayaran Anda',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // IMMEDIATE CHECK: Langsung cek status saat masuk halaman (tanpa delay)
        console.log('🚀 Performing IMMEDIATE status check (no delay)...');
        if (typeof performAutoStatusCheck === 'function') {
            performAutoStatusCheck(false);
        } else {
            // Fallback jika function belum ready
            enhancedStatusCheck();
        }

        // Follow-up checks dengan interval yang lebih cepat
        setTimeout(function () {
            console.log('🔄 Performing quick follow-up check (2s)...');
            if (typeof performAutoStatusCheck === 'function') {
                performAutoStatusCheck(false);
            } else {
                enhancedStatusCheck();
            }
        }, 2000);

        setTimeout(function () {
            console.log('🔄 Performing second follow-up check (5s)...');
            if (typeof performAutoStatusCheck === 'function') {
                performAutoStatusCheck(false);
            } else {
                enhancedStatusCheck();
            }
        }, 5000);

        setTimeout(function () {
            console.log('🔄 Performing third follow-up check (10s)...');
            if (typeof performAutoStatusCheck === 'function') {
                performAutoStatusCheck(false);
            } else {
                enhancedStatusCheck();
            }
        }, 10000);

        // Show progress indicator
        showAutoCheckProgress();
    }
}

// Show a progress indicator for auto-check
function showAutoCheckProgress() {
    // Create a small progress indicator
    const progressDiv = document.createElement('div');
    progressDiv.id = 'auto-check-progress';
    progressDiv.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            font-size: 12px;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        ">
            <div style="
                width: 12px;
                height: 12px;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 1s linear infinite;
            "></div>
            Auto-Check Aktif
        </div>
        <style>
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>
    `;

    document.body.appendChild(progressDiv);

    // Remove after 20 seconds
    setTimeout(function () {
        const element = document.getElementById('auto-check-progress');
        if (element) {
            element.remove();
        }
    }, 20000);
}

// Enhanced status check with better error handling
function enhancedStatusCheck() {
    if (typeof window.transactionId === 'undefined' || !window.transactionId) {
        console.warn('Transaction ID not found for enhanced status check');
        return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '';

    fetch(`/mahasiswa/payment/${window.transactionId}/status`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin'
    })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Invalid response format');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.status === 'verified') {
                console.log('✅ Payment verified via enhanced auto-check!');

                // Remove progress indicator
                const progressElement = document.getElementById('auto-check-progress');
                if (progressElement) {
                    progressElement.remove();
                }

                // Show success notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '🎉 Pembayaran Berhasil!',
                        html: 'Pembayaran Anda telah berhasil diverifikasi secara otomatis!<br><br>Halaman akan dimuat ulang dalam 3 detik...',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    // Fallback if SweetAlert not available
                    alert('Pembayaran berhasil diverifikasi! Halaman akan dimuat ulang.');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else if (data.success && data.status !== window.transactionStatus) {
                console.log('📊 Status changed via enhanced check:', window.transactionStatus, '->', data.status);
                window.transactionStatus = data.status;

                if (data.status === 'verified') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            }
        })
        .catch(error => {
            console.log('Enhanced auto-check error:', error);
            // Don't show error to user for background checks
        });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeEnhancedAutoCheck);
} else {
    // DOM already loaded
    initializeEnhancedAutoCheck();
}

// Export functions for global use
window.initializeEnhancedAutoCheck = initializeEnhancedAutoCheck;
window.enhancedStatusCheck = enhancedStatusCheck;
