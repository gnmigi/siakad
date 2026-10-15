<?php

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Master\Publikasi\BeritaController;
use App\Http\Controllers\Master\Publikasi\PengumumanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\RootController::class, 'renderHomePage'])->name('root.home-index');
Route::get('/welcome', [App\Http\Controllers\RootController::class, 'renderWelcome'])->middleware('prevent.multiple.setup')->name('root.welcome');
Route::post('/api/setup', [App\Http\Controllers\SetupController::class, 'processSetup'])->middleware('prevent.multiple.setup')->name('setup.process');

Route::get('/portal', \App\Http\Controllers\PortalController::class)->name('portal');

// SEO and Sitemap Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-index.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-berita.xml', [App\Http\Controllers\SitemapController::class, 'berita'])->name('sitemap.berita');
Route::get('/sitemap-pengumuman.xml', [App\Http\Controllers\SitemapController::class, 'pengumuman'])->name('sitemap.pengumuman');

use App\Http\Controllers\RootController;

// Public routes for Berita, Pengumuman, and Program Studi full view pages
Route::get('/berita/{slug}/view', [RootController::class, 'viewBerita'])->name('root.viewBerita');
Route::get('/pengumuman/{slug}/view', [RootController::class, 'viewPengumuman'])->name('root.viewPengumuman');
Route::get('/program-studi/{code}/view', [RootController::class, 'viewProgramStudi'])->name('root.viewProgramStudi');

// Public routes for "see all" pages with pagination
Route::get('/pengumuman', [RootController::class, 'allPengumuman'])->name('root.allPengumuman');
Route::get('/berita', [RootController::class, 'allBerita'])->name('root.allBerita');

// Public routes for institution pages - Redirect to infinite scroll version
Route::get('/profil', [RootController::class, 'institutionInfiniteScroll'])->name('root.institution.profile');
Route::get('/visi-misi', function() {
    return redirect('/profil?page=vision-mission');
})->name('root.institution.vision-mission');
Route::get('/struktur', function() {
    return redirect('/profil?page=structure');
})->name('root.institution.structure');
Route::get('/fasilitas', function() {
    return redirect('/profil?page=facilities');
})->name('root.institution.facilities');
Route::get('/gallery', function() {
    return redirect('/profil?page=gallery');
})->name('root.institution.gallery');

// AJAX routes for gallery load more functionality
Route::get('/ajax/gallery/homepage/load-more', [RootController::class, 'loadMoreHomepageGallery'])->name('ajax.gallery.homepage.load-more');
Route::get('/ajax/gallery/institution/load-more', [RootController::class, 'loadMoreInstitutionGallery'])->name('ajax.gallery.institution.load-more');
Route::get('/ajax/gallery/detail/{code}', [RootController::class, 'getGalleryDetail'])->name('ajax.gallery.detail');

// Gallery detail page route
Route::get('/galeri/{code}', [RootController::class, 'viewGalleryDetail'])->name('root.gallery.detail');

// Legacy routes for backward compatibility
Route::get('/profil-legacy', [RootController::class, 'institutionProfile'])->name('root.institution.profile-legacy');
Route::get('/visi-misi-legacy', [RootController::class, 'institutionVisionMission'])->name('root.institution.vision-mission-legacy');
Route::get('/struktur-legacy', [RootController::class, 'institutionStructure'])->name('root.institution.structure-legacy');
Route::get('/fasilitas-legacy', [RootController::class, 'institutionFacilities'])->name('root.institution.facilities-legacy');
Route::get('/gallery-legacy', [RootController::class, 'institutionGallery'])->name('root.institution.gallery-legacy');

// Infinite scroll institution pages
Route::get('/profil-infinite', [RootController::class, 'institutionInfiniteScroll'])->name('root.institution.infinite-scroll');
Route::get('/api/institution/{pageType}', [RootController::class, 'getInstitutionPageContent'])->name('root.institution.ajax-content');

// PMB (Penerimaan Mahasiswa Baru) routes
Route::get('/pmb/daftar', [App\Http\Controllers\PMBController::class, 'showRegistrationForm'])->name('pmb.registration.form');
Route::post('/pmb/daftar', [App\Http\Controllers\PMBController::class, 'submitRegistration'])->name('pmb.registration.submit');
Route::get('/pmb/sukses/{code}', [App\Http\Controllers\PMBController::class, 'registrationSuccess'])->name('pmb.registration.success');
Route::get('/api/pmb/jalurs/{periode_id}', [App\Http\Controllers\PMBController::class, 'getJalursByPeriode'])->name('pmb.jalurs.by-periode');

// PMB Email Verification routes
Route::get('/pmb/verifikasi-email', [App\Http\Controllers\PMBController::class, 'showEmailVerification'])->name('pmb.email.verification');
Route::post('/pmb/verifikasi-email', [App\Http\Controllers\PMBController::class, 'verifyEmail'])->name('pmb.email.verify');
Route::post('/pmb/kirim-ulang-kode', [App\Http\Controllers\PMBController::class, 'resendVerificationCode'])->name('pmb.email.resend');

// Calon Mahasiswa (Camaba) routes - protected by pendaftar guard
Route::middleware(['auth.pendaftar'])->prefix('camaba')->name('camaba.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Private\CalonMahasiswa\RootController::class, 'renderDashboard'])->name('dashboard-render');
    Route::get('/profile', [App\Http\Controllers\Private\CalonMahasiswa\RootController::class, 'renderProfile'])->name('profile-render');
    Route::patch('/profile', [App\Http\Controllers\Private\CalonMahasiswa\RootController::class, 'handleProfile'])->name('profile-handle');
    Route::get('/syarat-pendaftaran', [App\Http\Controllers\Private\CalonMahasiswa\RootController::class, 'requirementsRender'])->name('requirements-render');
    Route::post('/syarat-pendaftaran/upload', [App\Http\Controllers\Private\CalonMahasiswa\RootController::class, 'requirementsUpload'])->name('requirements-upload');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout');
});

// LATEST DEVELOPMENT
Route::middleware(['guest', 'first.setup'])->group(function () {

    // AUTH - SIGNIN
    Route::get('/signin', [App\Http\Controllers\AuthController::class, 'renderSignin'])->middleware('prevent.back.cache')->name('auth.render-signin');
    Route::post('/signin', [App\Http\Controllers\AuthController::class, 'handleSignin'])->name('auth.handle-signin');
    // AUTH - SIGNUP
    // Route::get('/signup', [App\Http\Controllers\AuthController::class, 'renderSignup'])->name('auth.render-signup');
    // Route::post('/signup', [App\Http\Controllers\AuthController::class, 'handleSignup'])->name('auth.handle-signup');
    // AUTH - FORGOT PASSWORD
    // Unified Forgot Password System (for Dosen & Mahasiswa)
    Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->middleware('prevent.back.cache')->name('auth.forgot-password');
    Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'handleForgotPassword'])->name('auth.handle-forgot-password');
    Route::get('/verify-reset-password', [App\Http\Controllers\AuthController::class, 'showVerifyResetForm'])->middleware('prevent.back.cache')->name('auth.verify-reset-password');
    Route::post('/verify-reset-password', [App\Http\Controllers\AuthController::class, 'verifyAndResetPassword'])->name('auth.verify-reset-password');
    Route::post('/resend-reset-code', [App\Http\Controllers\AuthController::class, 'resendResetCode'])->name('auth.resend-reset-code');
    // AUTH - LOGOUT
    // Route::get('/logout', [App\Http\Controllers\AuthController::class, 'handleLogout'])->name('auth.handle-logout');


    // HALAMAN UTAMA / FRONTEND
    // Route::get('/old', [App\Http\Controllers\Root\HomeController::class, 'index'])->name('root.home-index');
    Route::get('/post/view/{slug}', [App\Http\Controllers\Root\HomeController::class, 'postView'])->name('root.post-view');
    Route::get('/advice', [App\Http\Controllers\Root\HomeController::class, 'adviceIndex'])->name('root.home-advice');
    Route::get('/download', [App\Http\Controllers\Root\HomeController::class, 'downloadIndex'])->name('root.home-download');
    Route::get('/album-foto', [App\Http\Controllers\Root\HomeController::class, 'galleryIndex'])->name('root.gallery-index');
    Route::get('/album-foto/search', [App\Http\Controllers\Root\HomeController::class, 'gallerySearch'])->name('root.gallery-search');
    Route::get('/album-foto/show/{slug}', [App\Http\Controllers\Root\HomeController::class, 'galleryShow'])->name('root.gallery-show');
    Route::get('/admission/{slug}', [App\Http\Controllers\Root\HomeController::class, 'prodiIndex'])->name('root.home-prodi');
    Route::get('/program-kuliah/{code}', [App\Http\Controllers\Root\HomeController::class, 'prokuIndex'])->name('root.home-proku');
    Route::post('/advice/store', [App\Http\Controllers\Root\HomeController::class, 'adviceStore'])->name('root.home-advice-store');
});

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'handleLogout'])
    ->name('auth.handle-logout')
    ->middleware('auth:web,dosen,mahasiswa');

// ERROR PAGE
Route::get('/error/verify', [App\Http\Controllers\Root\ErrorController::class, 'ErrorVerify'])->name('error.verify');
Route::get('/error/access', [App\Http\Controllers\Root\ErrorController::class, 'ErrorAccess'])->name('error.access');
Route::get('/error/notfound', [App\Http\Controllers\Root\ErrorController::class, 'ErrorNotFound'])->name('error.notfound');

// HAK AKSES DEPARTEMENT WEB ADMINISTRATOR
require __DIR__.'/users/route-web-admin.php';
// HAK AKSES DEPARTEMENT AKADEMIK
require __DIR__.'/users/route-akademik.php';
// HAK AKSES DEPARTEMENT FINANSIAL
require __DIR__.'/users/route-finance.php';
// HAK AKSES DEPARTEMENT KEMAHASISWAAN
require __DIR__.'/users/route-kemahasiswaan.php';
// HAK AKSES DEPARTEMENT INFRASTRUKTUR & IT
require __DIR__.'/users/route-it.php';
// HAK AKSES DEPARTEMENT PERPUSTAKAAN
require __DIR__.'/users/route-library.php';
// HAK AKSES DEPARTEMENT UMUM
require __DIR__.'/users/route-umum.php';
// HAK AKSES DEPARTEMENT ADMISI
require __DIR__.'/users/route-admisi.php';
// HAK AKSES DOSEN
require __DIR__.'/lectures/route-dosen.php';
// HAK AKSES MAHASISWA
require __DIR__.'/students/route-mahasiswa.php';

// Include Master Core Routes
require __DIR__.'/master-core.php';
// Route logout sederhana
Route::match(['GET', 'POST'], '/logout', function() {
    if (Auth::check()) {
        $user = Auth::user();
        \App\Services\LogAktivitasService::logAuthEvent($user, 'user', 'logout', 'Berhasil logout dari sistem');
        Auth::logout();
    } elseif (Auth::guard('dosen')->check()) {
        $dosen = Auth::guard('dosen')->user();
        \App\Services\LogAktivitasService::logAuthEvent($dosen, 'dosen', 'logout', 'Berhasil logout dari sistem');
        Auth::guard('dosen')->logout();
    } elseif (Auth::guard('mahasiswa')->check()) {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        \App\Services\LogAktivitasService::logAuthEvent($mahasiswa, 'mahasiswa', 'logout', 'Berhasil logout dari sistem');
        Auth::guard('mahasiswa')->logout();
    }
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect()->route('auth.render-signin');
})->name('auth.handle-logout');
// Debug routes for 422 error troubleshooting
Route::get('/debug/payment-test', [App\Http\Controllers\DebugController::class, 'testPaymentEndpoint']);
Route::post('/debug/payment-test', [App\Http\Controllers\DebugController::class, 'testPaymentEndpoint']);
Route::get('/debug/session-info', [App\Http\Controllers\DebugController::class, 'getSessionInfo']);
// DEBUG: Test payment endpoint
Route::post('/debug-payment', function(Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Debug endpoint working',
        'data' => $request->all(),
        'csrf_token' => csrf_token()
    ]);
})->name('debug.payment');

// API endpoint to get Midtrans Snap payment method ID dynamically
Route::middleware(['auth:mahasiswa'])->get('/api/payment-methods/midtrans-snap', function() {
    try {
        $snapMethod = \App\Models\Keuangan\PaymentMethod::where('type', 'midtrans_snap')->first();
        
        if ($snapMethod) {
            return response()->json([
                'success' => true,
                'payment_method_id' => $snapMethod->id,
                'payment_method_name' => $snapMethod->name,
                'payment_method_code' => $snapMethod->code ?? 'MIDTRANS_SNAP',
                'message' => 'Payment method found'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Midtrans Snap payment method not found'
            ], 404);
        }
    } catch (\Exception $e) {
        Log::error('Get payment method API error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving payment method: ' . $e->getMessage()
        ], 500);
    }
})->name('api.payment-methods.midtrans-snap');

// FIXED: Test Snap Token Endpoint with Authentication
Route::middleware(['auth:mahasiswa'])->get('/test-snap-token/{transaction}', function($transactionId) {
    try {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error_code' => 'AUTH_REQUIRED'
            ], 401);
        }
        
        // Get transaction with ownership validation
        $transaction = \App\Models\Keuangan\PaymentTransaction::where('id', $transactionId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or access denied',
                'error_code' => 'TRANSACTION_NOT_FOUND'
            ], 404);
        }
        
        // Try to get existing Snap token from gateway response
        $gatewayResponse = $transaction->gateway_response ?? [];
        
        if (isset($gatewayResponse['token']) && !empty($gatewayResponse['token'])) {
            return response()->json([
                'success' => true,
                'snap_token' => $gatewayResponse['token'],
                'transaction_id' => $transactionId,
                'transaction_code' => $transaction->transaction_code,
                'source' => 'existing_token',
                'message' => 'Token retrieved successfully'
            ]);
        }
        
        // If no token exists, try to create a new Snap payment
        try {
            $gatewayService = app(\App\Services\PaymentGatewayService::class);
            
            // Find or create a Midtrans Snap payment method
            $snapPaymentMethod = \App\Models\Keuangan\PaymentMethod::where('type', 'midtrans_snap')->first();
            if (!$snapPaymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Midtrans Snap payment method not configured',
                    'error_code' => 'PAYMENT_METHOD_NOT_FOUND'
                ], 400);
            }
            
            // Update transaction payment method if needed
            if ($transaction->payment_method_id !== $snapPaymentMethod->id) {
                $transaction->update(['payment_method_id' => $snapPaymentMethod->id]);
            }
            
            // Create Snap payment via gateway service
            $result = $gatewayService->createPayment($transaction, $snapPaymentMethod);
            
            if ($result['success'] && isset($result['data']['token'])) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $result['data']['token'],
                    'transaction_id' => $transactionId,
                    'transaction_code' => $transaction->transaction_code,
                    'source' => 'new_token',
                    'message' => 'New token generated successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to generate Snap token',
                'error_code' => 'TOKEN_GENERATION_FAILED'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Snap token generation error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'mahasiswa_id' => $mahasiswa->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Snap token: ' . $e->getMessage(),
                'error_code' => 'TOKEN_GENERATION_ERROR'
            ], 500);
        }
        
    } catch (\Exception $e) {
        Log::error('Test Snap token endpoint error: ' . $e->getMessage(), [
            'transaction_id' => $transactionId,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'System error: ' . $e->getMessage(),
            'error_code' => 'SYSTEM_ERROR'
        ], 500);
    }
})->name('test.snap-token');

// DIRECT TEST ROUTE FOR LAPORAN KEUANGAN
Route::middleware(['auth'])->group(function () {
    Route::get('/finance/laporan-test', function() {
        return view('master.keuangan.laporan-keuangan-index');
    })->name('finance.laporan-test');
    
    Route::get('/finance/analisis-test', function() {
        return view('master.keuangan.analisis-pembayaran-index');
    })->name('finance.analisis-test');
});

// ULTRA SIMPLE FINANCE ROUTES - NO COMPLEX VIEWS
Route::middleware(['auth'])->group(function () {
    Route::get('/finance/laporan-simple', function() {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Keuangan - Finance Dashboard</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body>
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center mb-4">🎉 Laporan Keuangan Berhasil!</h1>
                        <div class="alert alert-success">
                            <h4>✅ Sistem Finance Department Berfungsi!</h4>
                            <p>Selamat! Menu Laporan Keuangan sudah berhasil diakses.</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Total Pemasukan</h5>
                                        <h3>Rp 50,000,000</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Total Transaksi</h5>
                                        <h3>150</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>Rata-rata</h5>
                                        <h3>Rp 333,333</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5>Pending</h5>
                                        <h3>Rp 25,000,000</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Grafik Pembayaran Harian</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="/finance/home" class="btn btn-primary">Kembali ke Dashboard</a>
                            <a href="/finance/analisis-simple" class="btn btn-success">Analisis Pembayaran</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                const ctx = document.getElementById("paymentChart").getContext("2d");
                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: ["Jan 1", "Jan 2", "Jan 3", "Jan 4", "Jan 5"],
                        datasets: [{
                            label: "Pembayaran Harian",
                            data: [1500000, 2300000, 1800000, 2700000, 3200000],
                            borderColor: "rgb(75, 192, 192)",
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </body>
        </html>';
    })->name('finance.laporan-simple');
    
    Route::get('/finance/analisis-simple', function() {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Analisis Pembayaran - Finance Dashboard</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body>
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center mb-4">📊 Analisis Pembayaran</h1>
                        <div class="alert alert-info">
                            <h4>✅ Analisis Pembayaran Berfungsi!</h4>
                            <p>Dashboard analisis pembayaran sudah berhasil diakses.</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Success Rate</h5>
                                        <h3>85.5%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body">
                                        <h5>Avg Payment Time</h5>
                                        <h3>3.2 days</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-dark text-white">
                                    <div class="card-body">
                                        <h5>Peak Hour</h5>
                                        <h3>14:00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Efficiency</h5>
                                        <h3>78.9%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Trend Pembayaran Bulanan</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="trendChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="/finance/home" class="btn btn-primary">Kembali ke Dashboard</a>
                            <a href="/finance/laporan-simple" class="btn btn-success">Laporan Keuangan</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                const ctx = document.getElementById("trendChart").getContext("2d");
                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May"],
                        datasets: [{
                            label: "Pembayaran (Juta Rupiah)",
                            data: [45, 52, 48, 55, 60],
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </body>
        </html>';
    })->name('finance.analisis-simple');
});
