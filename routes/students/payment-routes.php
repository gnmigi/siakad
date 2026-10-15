<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Private\Mahasantri\PaymentController;

/*
|--------------------------------------------------------------------------
| Student Payment Routes
|--------------------------------------------------------------------------
|
| Routes for student payment functionality
|
*/

Route::middleware(['auth:mahasiswa'])->group(function () {
    
    // Payment Dashboard
    Route::get('/payment', [PaymentController::class, 'index'])->name('mahasiswa.payment.index');
    
    // Modern Core API Routes (harus di atas untuk menghindari konflik)
    Route::get('/payment/checkout/{tagihan}', [PaymentController::class, 'checkoutModern'])->name('mahasiswa.payment.checkout-modern');
    Route::post('/payment/create-transaction', [PaymentController::class, 'createTransaction'])->name('mahasiswa.payment.create-transaction');
    Route::get('/payment/status/{transaction}', [PaymentController::class, 'statusModern'])->name('mahasiswa.payment.status-modern');
    Route::get('/payment/check-status/{transaction}', [PaymentController::class, 'checkStatusModern'])->name('mahasiswa.payment.check-status-modern');
    
    // Payment Result Pages
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('mahasiswa.payment.success');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('mahasiswa.payment.pending');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('mahasiswa.payment.failed');
    
    // Create Payment
    Route::get('/payment/create/{tagihan}', [PaymentController::class, 'create'])->name('mahasiswa.payment.create');
    Route::post('/payment', [PaymentController::class, 'store'])->name('mahasiswa.payment.store');
    
    // Payment History
    Route::get('/payment-history', [PaymentController::class, 'history'])->name('mahasiswa.payment.history');
    
    // AJAX Routes
    Route::get('/payment/{transaction}/status', [PaymentController::class, 'checkStatus'])->name('mahasiswa.payment.check-status');
    Route::get('/payment/{transaction}/snap-token', [PaymentController::class, 'getSnapToken'])->name('mahasiswa.payment.snap-token');
    
    // Upload Payment Proof
    Route::post('/payment/{transaction}/upload-proof', [PaymentController::class, 'uploadProof'])->name('mahasiswa.payment.upload-proof');
    
    // Cancel Payment
    Route::post('/payment/{transaction}/cancel', [PaymentController::class, 'cancel'])->name('mahasiswa.payment.cancel');
    
    // View Receipt (Web)
    Route::get('/payment/{transaction}/view-receipt', [PaymentController::class, 'viewReceipt'])->name('mahasiswa.payment.view-receipt');
    
    // Download Receipt (PDF)
    Route::get('/payment/{transaction}/receipt', [PaymentController::class, 'downloadReceipt'])->name('mahasiswa.payment.receipt');
    
    // Payment Details (harus di bawah untuk menghindari konflik dengan routes spesifik)
    Route::get('/payment/{transaction}', [PaymentController::class, 'show'])->name('mahasiswa.payment.show');
    
});
