<?php

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G623844075'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'Mid-client-2sVG7TyPbyiNssv3'),
    'server_key' => env('MIDTRANS_SERVER_KEY', 'Mid-server-aZprWyycJBjuaePEnetxxiQy'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    
    // API URLs
    'sandbox_base_url' => 'https://api.sandbox.midtrans.com/v2',
    'production_base_url' => 'https://api.midtrans.com/v2',
    
    // Snap URLs
    'sandbox_snap_url' => 'https://app.sandbox.midtrans.com/snap/snap.js',
    'production_snap_url' => 'https://app.midtrans.com/snap/snap.js',
    
    // Notification URL
    'notification_url' => env('APP_URL') . '/api/midtrans/notification',
    'finish_url' => env('APP_URL') . '/mahasantri/payment/success',
    'unfinish_url' => env('APP_URL') . '/mahasantri/payment/pending',
    'error_url' => env('APP_URL') . '/mahasantri/payment/failed',
];
