
// WORKING SNAP JS TEST - Transaction ID: 10
console.log('🧪 Testing Snap Payment for Transaction ID: 10');

function testSnapPayment() {
    console.log('🚀 Starting Snap Payment Test...');
    
    fetch('/test-snap-token/10', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('📡 Response Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('✅ Response Data:', data);
        
        if (data.success && data.snap_token) {
            console.log('🎉 Got Snap Token:', data.snap_token.substring(0, 20) + '...');
            
            // Load Midtrans Snap JS
            const script = document.createElement('script');
            script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
            script.setAttribute('data-client-key', 'Mid-client-2sVG7TyPbyiNssv3');
            script.onload = function() {
                console.log('✅ Snap JS Loaded Successfully');
                
                // Open Snap popup
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('🎉 Payment Success:', result);
                        alert('🎉 Pembayaran Berhasil! Amount: Rp ' + data.amount.toLocaleString());
                    },
                    onPending: function(result) {
                        console.log('⏳ Payment Pending:', result);
                        alert('⏳ Pembayaran Sedang Diproses');
                    },
                    onError: function(result) {
                        console.log('❌ Payment Error:', result);
                        alert('❌ Pembayaran Gagal: ' + (result.status_message || 'Unknown error'));
                    },
                    onClose: function() {
                        console.log('🔒 Snap Popup Closed');
                    }
                });
            };
            script.onerror = function() {
                console.error('❌ Failed to load Midtrans Snap JS');
                alert('❌ Failed to load Midtrans Snap JS');
            };
            document.head.appendChild(script);
        } else {
            console.error('❌ API Error:', data);
            alert('❌ Error: ' + (data.message || 'Snap token not found'));
        }
    })
    .catch(error => {
        console.error('❌ Fetch Error:', error);
        alert('❌ Network Error: ' + error.message);
    });
}

// Auto-run the test
console.log('🎯 Auto-running Snap payment test...');
testSnapPayment();
