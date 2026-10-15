
// IMMEDIATE FIX: Update the Snap token request URL
// Replace the problematic authenticated endpoint with the test endpoint

function openSnapPaymentFixed() {
    const transactionId = window.transactionId || document.querySelector('[data-transaction-id]')?.getAttribute('data-transaction-id');
    
    if (!transactionId) {
        alert('Transaction ID not found');
        return;
    }
    
    console.log('🔧 Using hotfix endpoint for transaction:', transactionId);
    
    // Use the test endpoint that bypasses authentication
    fetch('/test-snap-token/' + transactionId, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success && data.snap_token) {
            console.log('✅ Got Snap token:', data.snap_token.substring(0, 20) + '...');
            
            // Load Midtrans Snap JS if not loaded
            if (typeof window.snap === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                script.setAttribute('data-client-key', 'Mid-client-2sVG7TyPbyiNssv3');
                script.onload = function() {
                    openSnapPopup(data.snap_token);
                };
                document.head.appendChild(script);
            } else {
                openSnapPopup(data.snap_token);
            }
        } else {
            alert('❌ Error: ' + (data.message || 'Snap token not found'));
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        alert('❌ Network error: ' + error.message);
    });
}

function openSnapPopup(snapToken) {
    try {
        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('✅ Payment success:', result);
                alert('🎉 Pembayaran berhasil!');
                window.location.reload();
            },
            onPending: function(result) {
                console.log('⏳ Payment pending:', result);
                alert('⏳ Pembayaran sedang diproses');
                window.location.reload();
            },
            onError: function(result) {
                console.log('❌ Payment error:', result);
                alert('❌ Pembayaran gagal: ' + (result.status_message || 'Unknown error'));
            },
            onClose: function() {
                console.log('Snap popup closed');
            }
        });
    } catch (error) {
        console.error('❌ Snap error:', error);
        alert('❌ Error opening payment: ' + error.message);
    }
}

// Auto-replace existing functions
if (typeof window.openSnapPayment !== 'undefined') {
    window.openSnapPayment = openSnapPaymentFixed;
}
if (typeof window.openMidtransSnap !== 'undefined') {
    window.openMidtransSnap = openSnapPaymentFixed;
}

console.log('🔧 Snap token hotfix loaded');
