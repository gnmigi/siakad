<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// --- API MOBILE NATIVE ---
Route::get('/settings', [App\Http\Controllers\Api\SettingController::class, 'getSettings']);

Route::group(['prefix' => 'v1'], function () {
    // Public Routes
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

        // DOSEN ROUTES
        Route::prefix('dosen')->group(function () {
            Route::get('/profile', [App\Http\Controllers\Api\Dosen\DosenController::class, 'getProfile']);
            Route::get('/dashboard', [App\Http\Controllers\Api\Dosen\DosenController::class, 'getDashboard']);
        });

        // MAHASISWA ROUTES
        Route::prefix('mahasiswa')->group(function () {
            Route::get('/profile', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getProfile']);
            Route::get('/dashboard', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getDashboard']);

            // Academic Records
            Route::get('/krs', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getKrs']);
            Route::post('/krs', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'saveKrs']);
            Route::get('/khs', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getKhs']);
            Route::get('/transkrip', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getTranskrip']);

            // Financials
            Route::get('/tagihan', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getTagihan']);
            Route::get('/tagihan/history', [App\Http\Controllers\Api\Mahasiswa\MahasiswaController::class, 'getPaymentHistory']);
        });
    });
});


// Payment Gateway Webhooks - Direct routes for Midtrans
Route::post('/webhook/midtrans', [App\Http\Controllers\Api\PaymentWebhookController::class, 'midtrans'])->name('api.webhook.midtrans');

// Payment Gateway Webhooks - Grouped routes
Route::group(['prefix' => 'payment'], function () {
    Route::post('/webhook/midtrans', [App\Http\Controllers\Api\PaymentWebhookController::class, 'midtrans'])->name('api.payment.webhook.midtrans');
    Route::get('/callback', [App\Http\Controllers\Api\PaymentWebhookController::class, 'callback'])->name('payment.callback');
});

// Manual sync endpoint for testing
Route::post('/webhook/manual-sync', [App\Http\Controllers\Api\PaymentWebhookController::class, 'manualSync']);
// Payment status check routes
Route::post('/payment/check-status', [App\Http\Controllers\Api\PaymentStatusController::class, 'checkStatus']);
Route::get('/payment/system-status', [App\Http\Controllers\Api\PaymentStatusController::class, 'systemStatus']);

// WhatsApp Gateway Webhook integration
Route::post('/webhook/whatsapp', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])->name('api.webhook.whatsapp');

