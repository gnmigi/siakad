// EMERGENCY SNAP FIX - Direct JavaScript Solution
console.log('🚨 Emergency Snap Fix Loaded');

function emergencySnapFix() {
    console.log('🔧 Emergency Snap Fix Activated');

    // Try to find transaction ID from URL
    let transactionId = null;
    const urlMatch = window.location.pathname.match(/payment\/(\d+)/);
    if (urlMatch) {
        transactionId = urlMatch[1];
    }

    // Method 2: From data attributes
    if (!transactionId) {
        const element = document.querySelector('[data-transaction-id]');
        if (element) {
            transactionId = element.getAttribute('data-transaction-id');
        }
    }

    // Method 3: From window variable
    if (!transactionId && window.transactionId) {
        transactionId = window.transactionId;
    }

    // If not found, ask user
    if (!transactionId) {
        transactionId = prompt('Enter Transaction ID (check URL or page source):');
    }

    if (!transactionId) {
        alert('❌ Transaction ID not found');
        return;
    }

    console.log('🎯 Using Transaction ID:', transactionId);

    // Use the working test endpoint
    fetch('/test-snap-token/' + transactionId, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            console.log('📡 Response Status:', response.status);
            console.log('📡 Content-Type:', response.headers.get('content-type'));

            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON - Authentication issue');
            }

            return response.json();
        })
        .then(data => {
            console.log('✅ Response Data:', data);

            if (data.success && data.snap_token) {
                console.log('🎉 Got Snap Token:', data.snap_token.substring(0, 20) + '...');

                // Load Midtrans Snap JS if not loaded
                if (typeof window.snap === 'undefined') {
                    console.log('📦 Loading Midtrans Snap JS...');
                    const script = document.createElement('script');
                    script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                    script.setAttribute('data-client-key', 'Mid-client-2sVG7TyPbyiNssv3');
                    script.onload = function () {
                        console.log('✅ Snap JS Loaded');
                        openSnapPopupEmergency(data.snap_token);
                    };
                    script.onerror = function () {
                        alert('❌ Failed to load Midtrans Snap JS');
                    };
                    document.head.appendChild(script);
                } else {
                    console.log('✅ Snap JS Already Loaded');
                    openSnapPopupEmergency(data.snap_token);
                }
            } else {
                alert('❌ Error: ' + (data.message || 'Snap token not found'));
                console.error('❌ API Error:', data);
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);

            if (error.message.includes('HTML instead of JSON')) {
                alert('❌ Authentication Error: Please refresh the page and make sure you are logged in');
            } else {
                alert('❌ Network Error: ' + error.message);
            }
        });
}

function openSnapPopupEmergency(snapToken) {
    try {
        console.log('🚀 Opening Snap Popup...');

        window.snap.pay(snapToken, {
            onSuccess: function (result) {
                console.log('🎉 Payment Success:', result);
                alert('🎉 Pembayaran Berhasil!');
                window.location.reload();
            },
            onPending: function (result) {
                console.log('⏳ Payment Pending:', result);
                alert('⏳ Pembayaran Sedang Diproses');
                window.location.reload();
            },
            onError: function (result) {
                console.log('❌ Payment Error:', result);
                alert('❌ Pembayaran Gagal: ' + (result.status_message || 'Unknown error'));
            },
            onClose: function () {
                console.log('🔒 Snap Popup Closed');
            }
        });
    } catch (error) {
        console.error('❌ Snap Popup Error:', error);
        alert('❌ Error opening payment popup: ' + error.message);
    }
}

// Replace existing functions
window.openSnapPayment = emergencySnapFix;
window.openMidtransSnap = emergencySnapFix;

// Add button click handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Find and replace button click handlers
    const buttons = document.querySelectorAll('button, a');
    buttons.forEach(button => {
        const text = button.textContent || button.innerText;
        if (text.includes('Snap') || text.includes('Pembayaran') || text.includes('Bayar')) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                emergencySnapFix();
            });
            console.log('🔧 Added emergency handler to button:', text.trim());
        }
    });
});

// Global emergency function
window.emergencySnapFix = emergencySnapFix;

console.log('✅ Emergency Snap Fix Ready - Call emergencySnapFix() or click payment buttons');
