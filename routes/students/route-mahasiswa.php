<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Private\Mahasantri\RootController;
use App\Http\Controllers\Private\Mahasantri\KrsController;
use App\Http\Controllers\Private\Mahasantri\KhsController;

/*
|--------------------------------------------------------------------------
| Mahasantri Routes
|--------------------------------------------------------------------------
|
| Here is where you can register mahasiswa routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "mahasiswa" middleware group.
|
*/

// Mahasantri Routes (Require Authentication)
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::middleware(['mhs.access'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [RootController::class, 'renderDashboard'])->name('dashboard-render');
        
        // Profile
        Route::get('/profile', [RootController::class, 'renderProfile'])->name('profile-render');
        Route::patch('/profile', [RootController::class, 'handleProfile'])->name('profile-handle');
        
        // KRS Routes - Protected by terminal check
        Route::prefix('krs')->name('krs.')->middleware(['terminal:krs'])->group(function () {
            Route::get('/', [KrsController::class, 'index'])->name('index');
            Route::get('/create', [KrsController::class, 'create'])->name('create');
            Route::post('/', [KrsController::class, 'store'])->name('store');
            Route::get('/{id}', [KrsController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [KrsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [KrsController::class, 'update'])->name('update');
            Route::get('/{id}/print', [KrsController::class, 'print'])->name('print');
            Route::get('/{id}/export-pdf', [KrsController::class, 'exportPdf'])->name('export-pdf');
            Route::post('/{id}/submit', [KrsController::class, 'submit'])->name('submit');
        });

        // KHS Routes - Protected by terminal check
        Route::prefix('khs')->name('khs.')->middleware(['terminal:khs'])->group(function () {
            Route::get('/', [KhsController::class, 'index'])->name('index');
            Route::get('/print', [KhsController::class, 'print'])->name('print');
            Route::get('/export-pdf', [KhsController::class, 'exportPdf'])->name('export-pdf');
        });

        // Transkrip Routes - Protected by terminal check
        Route::prefix('transkrip')->name('transkrip.')->middleware(['terminal:transkrip'])->group(function () {
            Route::get('/', [KhsController::class, 'transkrip'])->name('index');
            Route::get('/export-pdf', [KhsController::class, 'transkripPdf'])->name('export-pdf');
        });
        
        // Status Kelulusan Routes
        Route::prefix('kelulusan')->name('kelulusan.')->group(function () {
            Route::get('/', [KhsController::class, 'kelulusan'])->name('index');
        });
        
        // Payment Routes
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'index'])->name('index');
            
            // Modern Core API Routes (harus di atas untuk menghindari konflik)
            Route::get('/checkout/{tagihan}', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'checkoutModern'])->name('checkout-modern');
            Route::post('/create-transaction', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'createTransaction'])->name('create-transaction');
            Route::get('/status/{transaction}', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'statusModern'])->name('status-modern');
            Route::get('/check-status/{transaction}', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'checkStatusModern'])->name('check-status-modern');
            
            // Payment Result Pages
            Route::get('/success', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'success'])->name('success');
            Route::get('/pending', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'pending'])->name('pending');
            Route::get('/failed', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'failed'])->name('failed');
            
            Route::get('/create/{tagihan}', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'store'])->name('store');
            Route::get('/{transaction}/snap-token', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'getSnapToken'])->name('snap-token');
            Route::post('/{transaction}/upload-proof', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'uploadProof'])->name('upload-proof');
            Route::post('/{transaction}/cancel', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'cancel'])->name('cancel');
            Route::get('/{transaction}/view-receipt', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'viewReceipt'])->name('view-receipt');
            Route::get('/{transaction}/receipt', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'downloadReceipt'])->name('receipt');
            Route::get('/tagihan/{tagihan}/accumulated-receipt', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'viewAccumulatedReceipt'])->name('view-accumulated-receipt');
            Route::get('/tagihan/{tagihan}/accumulated-receipt/download', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'downloadAccumulatedReceipt'])->name('download-accumulated-receipt');
            Route::get('/{transaction}/status', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'checkStatus'])->name('check-status');
            
            // Payment Details (harus di bawah untuk menghindari konflik dengan routes spesifik)
            Route::get('/{transaction}', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'show'])->name('show');
        });
        
        // Payment History Routes
        Route::get('/payment-history', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'history'])->name('payment.history');
        
        // Logout - Use main AuthController
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout');
        Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout-get');
    });
});

// Alumni Routes (Require Authentication)
Route::prefix('alumni')->name('alumni.')->group(function () {
    Route::middleware(['mhs.access'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [RootController::class, 'renderAlumniDashboard'])->name('dashboard-render');
        
        // Profile
        Route::get('/profile', [RootController::class, 'renderProfile'])->name('profile-render');
        Route::patch('/profile', [RootController::class, 'handleProfile'])->name('profile-handle');
        
        // Alumni-specific routes (read-only academic records)
        Route::prefix('transkrip')->name('transkrip.')->group(function () {
            Route::get('/', [KhsController::class, 'transkrip'])->name('index');
            Route::get('/export-pdf', [KhsController::class, 'transkripPdf'])->name('export-pdf');
        });
        
        // Alumni KHS (historical records)
        Route::prefix('khs')->name('khs.')->group(function () {
            Route::get('/', [KhsController::class, 'index'])->name('index');
            Route::get('/print', [KhsController::class, 'print'])->name('print');
            Route::get('/export-pdf', [KhsController::class, 'exportPdf'])->name('export-pdf');
        });
        
        // Status Kelulusan Routes (for alumni)
        Route::prefix('kelulusan')->name('kelulusan.')->group(function () {
            Route::get('/', [KhsController::class, 'kelulusan'])->name('index');
        });
        
        // Payment History Routes (alumni can view payment history)
        Route::get('/payment-history', [\App\Http\Controllers\Private\Mahasantri\PaymentController::class, 'history'])->name('payment.history');
        
        // Logout - Use main AuthController
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout');
        Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout-get');
    });
});
