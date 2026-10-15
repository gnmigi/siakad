<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DOSEN
Route::group(['prefix' => 'dosen', 'middleware' => ['dsn-access:1'], 'as' => 'dosen.'],function(){
     // GLOBAL MENU AUTHENTIKASI
     Route::post('/logout', [App\Http\Controllers\AuthController::class, 'handleLogout'])->name('handle-logout');
 
     Route::get('/profile',[App\Http\Controllers\Private\Dosen\RootController::class, 'renderProfile'])->name('profile-render');
    Route::patch('/profile',[App\Http\Controllers\Private\Dosen\RootController::class, 'handleProfile'])->name('profile-handle');
    Route::post('/profile/upload-photo', [App\Http\Controllers\Private\User\RootController::class, 'uploadPhoto'])->name('profile-upload-photo');
 
     // GLOBAL MENU
     Route::get('/home',[App\Http\Controllers\Private\Dosen\RootController::class, 'renderDashboard'])->name('home-index');
 
     // DOSEN DASHBOARD
     Route::get('/dashboard',[App\Http\Controllers\Private\Dosen\RootController::class, 'renderDashboard'])->name('dashboard-render');
 
     // DOSEN ABSENSI
     Route::get('/absensi',[App\Http\Controllers\Private\Dosen\AbsensiController::class, 'renderAbsensi'])->name('absensi-render');
     Route::post('/absensi',[App\Http\Controllers\Private\Dosen\AbsensiController::class, 'handleAbsensi'])->name('absensi-handle');
     Route::patch('/absensi/{code}',[App\Http\Controllers\Private\Dosen\AbsensiController::class, 'updateAbsensi'])->name('absensi-update');

     // MATA KULIAH MANAGEMENT
     Route::get('/mata-kuliah',[App\Http\Controllers\Private\Dosen\MataKuliahController::class, 'renderMataKuliah'])->name('mata-kuliah-render');

     // NILAI MANAGEMENT
     Route::prefix('nilai')->name('nilai.')->group(function () {
         Route::get('/input',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'renderInputNilai'])->name('input-render');
         Route::post('/input',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'handleInputNilai'])->name('input-handle');
         Route::get('/rekap',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'renderRekapNilai'])->name('rekap-render');
         Route::get('/upload',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'renderUploadNilai'])->name('upload-render');
         Route::post('/upload',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'handleUploadExcel'])->name('upload-handle');
         Route::get('/upload/template',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'downloadUploadTemplate'])->name('upload-template');
         Route::get('/mahasiswa-by-matkul',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'getMahasiswaByMataKuliah'])->name('mahasiswa-by-matkul');
         Route::get('/get-existing',[App\Http\Controllers\Private\Dosen\NilaiController::class, 'getExistingGrades'])->name('get-existing');
     });

// ABSENSI MAHASANTRI
    Route::prefix('absensi-kelas')->name('absensi-kelas.')->group(function () {
        Route::get('/', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'index'])->name('index');
        Route::get('/pertemuan/{mata_kuliah_id}', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'show'])->name('show');
        Route::post('/simpan', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'store'])->name('store');
        
        // --- TAMBAHAN BARU UNTUK REKAP & EXCEL ---
        Route::get('/rekap/view', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'rekap'])->name('rekap');
        Route::get('/rekap/export', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'exportExcel'])->name('export');
        Route::post('/rekap/import', [App\Http\Controllers\Private\Dosen\AbsensiKelasController::class, 'importExcel'])->name('import');
    });
    // PRIVATE FUNCTION => DATA AKADEMIK - JADWAL MENGAJAR
    // Route::get('/data-akademik/jadwal',[App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'index'])->name('akademik.jadwal-index');
    // Route::get('/data-akademik/jadwal/{code}/absen',[App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'viewAbsen'])->name('akademik.jadwal-view-absen');
    // Route::get('/data-akademik/jadwal/{code}/feedback',[App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'viewFeedBack'])->name('akademik.jadwal-view-feedback');
    // Route::patch('/data-akademik/jadwal/absen/{code}/update',[App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'updateAbsen'])->name('akademik.jadwal-absen-update');

    // PRIVATE FUNCTION => DATA AKADEMIK - Kelola Tugas
    // Route::get('/data-akademik/kelola-tugas',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'index'])->name('akademik.stask-index');
    // Route::get('/data-akademik/kelola-tugas/tambah',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'create'])->name('akademik.stask-create');
    // Route::get('/data-akademik/kelola-tugas/view/{code}',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'view'])->name('akademik.stask-view');
    // Route::get('/data-akademik/kelola-tugas/view/detail/{code}',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'viewDetail'])->name('akademik.stask-view-detail');
    // Route::get('/data-akademik/kelola-tugas/edit/{code}',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'edit'])->name('akademik.stask-edit');
    // Route::get('/data-akademik/kelola-tugas/edit/{code}/score',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'editScore'])->name('akademik.stask-edit-score');
    // Route::post('/data-akademik/kelola-tugas/store',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'store'])->name('akademik.stask-store');
    // Route::patch('/data-akademik/kelola-tugas/update/{code}',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'update'])->name('akademik.stask-update');
    // Route::patch('/data-akademik/kelola-tugas/update/{code}/score',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'updateScore'])->name('akademik.stask-update-score');
    // Route::delete('/data-akademik/kelola-tugas/delete/{code}',[App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'destroy'])->name('akademik.stask-destroy');

    // PRIVATE FUNCTION => GRAPHIC AJAX FUNCTION
    // Route::get('/services/ajax/graphic/{code}/kepuasan-mengajar',[App\Http\Controllers\Services\Ajax\GraphicController::class, 'getKepuasanMengajar'])->name('services.ajax.graphic.kepuasan-mengajar');
    // Route::get('/services/ajax/graphic/kepuasan-mengajar/dosen',[App\Http\Controllers\Services\Ajax\GraphicController::class, 'getKepuasanMengajarDosen'])->name('services.ajax.graphic.kepuasan-mengajar-dosen');


});

