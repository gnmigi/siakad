<?php

use Illuminate\Support\Facades\Route;

// MASTER CORE ROUTES - Protected by auth middleware
Route::middleware(['auth'])->group(function () {
    // JSON endpoint khusus AJAX verifikasi transfer (diletakkan paling awal agar tidak tertimpa route wildcard lain)
    Route::get('/keuangan/pembayaran-manual/verifikasi/data', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'verificationQueueData'])
        ->name('keuangan.pembayaran-manual-verification-data');
    // MASTER CORE ROUTES - Using Private\User\RootController (same as other user types)

    Route::get('/dashboard', [App\Http\Controllers\Private\User\RootController::class, 'renderDashboard'])->name('dashboard-render');
    Route::get('/profile', [App\Http\Controllers\Private\User\RootController::class, 'renderProfile'])->name('profile-render');
    Route::post('/profile', [App\Http\Controllers\Private\User\RootController::class, 'handleProfile'])->name('profile-handle');
    Route::post('/profile/upload-photo', [App\Http\Controllers\Private\User\RootController::class, 'uploadPhoto'])->name('profile-upload-photo');
    Route::get('/dashboard/chart-data', [App\Http\Controllers\Private\User\RootController::class, 'getDashboardChartData'])->name('dashboard-chart-data');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('handle-logout');
    Route::get('/absensi', [App\Http\Controllers\Private\User\Pages\AbsensiController::class, 'renderAbsensi'])->name('absensi-render');
    Route::post('/absensi', [App\Http\Controllers\Private\User\Pages\AbsensiController::class, 'handleAbsensi'])->name('absensi-handle');
    Route::patch('/absensi/{code}', [App\Http\Controllers\Private\User\Pages\AbsensiController::class, 'updateAbsensi'])->name('master.absensi-update');

    // MASTER AKADEMIK => TAHUN AKADEMIK
    Route::get('/akademik/tahun-akademik',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'renderTaka'])->name('akademik.taka-render');
    Route::post('/akademik/tahun-akademik',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'handleTaka'])->name('akademik.taka-handle');
    Route::patch('/akademik/tahun-akademik/{code}',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'updateTaka'])->name('akademik.taka-update');
    Route::delete('/akademik/tahun-akademik/{code}',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'deleteTaka'])->name('akademik.taka-delete');
    
    // Auto Advance Academic Year Routes
    Route::get('/akademik/tahun-akademik/preview-advancement',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'previewAdvancement'])->name('akademik.taka-preview-advancement');
    Route::post('/akademik/tahun-akademik/auto-advance',[App\Http\Controllers\Master\Akademik\TahunAkademikController::class, 'autoAdvanceAcademicYear'])->name('akademik.taka-auto-advance');

    // MASTER AKADEMIK => PROGRAM STUDI
    Route::get('/akademik/program-studi',[App\Http\Controllers\Master\Akademik\ProgramStudiController::class, 'renderProdi'])->name('akademik.prodi-render');
    Route::post('/akademik/program-studi',[App\Http\Controllers\Master\Akademik\ProgramStudiController::class, 'handleProdi'])->name('akademik.prodi-handle');
    Route::patch('/akademik/program-studi/{code}',[App\Http\Controllers\Master\Akademik\ProgramStudiController::class, 'updateProdi'])->name('akademik.prodi-update');
    Route::delete('/akademik/program-studi/{code}',[App\Http\Controllers\Master\Akademik\ProgramStudiController::class, 'deleteProdi'])->name('akademik.prodi-delete');

    // MASTER AKADEMIK => FAKULTAS (DISABLED - No longer used)
    // Route::get('/akademik/fakultas',[App\Http\Controllers\Master\Akademik\FakultasController::class, 'renderFakultas'])->name('akademik.fakultas-render');
    // Route::post('/akademik/fakultas',[App\Http\Controllers\Master\Akademik\FakultasController::class, 'handleFakultas'])->name('akademik.fakultas-handle');
    // Route::patch('/akademik/fakultas/{code}',[App\Http\Controllers\Master\Akademik\FakultasController::class, 'updateFakultas'])->name('akademik.fakultas-update');
    // Route::delete('/akademik/fakultas/{code}',[App\Http\Controllers\Master\Akademik\FakultasController::class, 'deleteFakultas'])->name('akademik.fakultas-delete');

    // MASTER AKADEMIK => KURIKULUM
    Route::get('/akademik/kurikulum',[App\Http\Controllers\Master\Akademik\KurikulumController::class, 'renderKurikulum'])->name('akademik.kurikulum-render');
    Route::post('/akademik/kurikulum',[App\Http\Controllers\Master\Akademik\KurikulumController::class, 'handleKurikulum'])->name('akademik.kurikulum-handle');
    Route::patch('/akademik/kurikulum/{code}',[App\Http\Controllers\Master\Akademik\KurikulumController::class, 'updateKurikulum'])->name('akademik.kurikulum-update');
    Route::delete('/akademik/kurikulum/{code}',[App\Http\Controllers\Master\Akademik\KurikulumController::class, 'deleteKurikulum'])->name('akademik.kurikulum-delete');
    
    // MASTER AKADEMIK => MATA KULIAH
    Route::get('/akademik/mata-kuliah',[App\Http\Controllers\Master\Akademik\MataKuliahController::class, 'renderMataKuliah'])->name('akademik.mata-kuliah-render');
    Route::post('/akademik/mata-kuliah',[App\Http\Controllers\Master\Akademik\MataKuliahController::class, 'handleMataKuliah'])->name('akademik.mata-kuliah-handle');
    Route::patch('/akademik/mata-kuliah/{code}',[App\Http\Controllers\Master\Akademik\MataKuliahController::class, 'updateMataKuliah'])->name('akademik.mata-kuliah-update');
    Route::delete('/akademik/mata-kuliah/{code}',[App\Http\Controllers\Master\Akademik\MataKuliahController::class, 'deleteMataKuliah'])->name('akademik.mata-kuliah-delete');
    Route::get('/akademik/mata-kuliah/filter',[App\Http\Controllers\Master\Akademik\MataKuliahController::class, 'getMataKuliahFiltered'])->name('akademik.mata-kuliah-filter');

    // MASTER AKADEMIK => DATA AKADEMIK MAHASISWA
    Route::get('/akademik/data-akademik-mahasiswa',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'renderDataAkademikMahasiswa'])->name('akademik.data-akademik-mahasiswa-render');
    Route::patch('/akademik/data-akademik-mahasiswa/{code}',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'updateDataAkademikMahasiswa'])->name('akademik.data-akademik-mahasiswa-update');
    Route::patch('/akademik/data-akademik-mahasiswa-profile/{code}',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'updateProfileMahasiswa'])->name('akademik.data-akademik-mahasiswa-profile-update');
    Route::post('/akademik/data-akademik-mahasiswa-terminal/{code}',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'toggleTerminal'])->name('akademik.data-akademik-mahasiswa-terminal-toggle');
    Route::post('/akademik/data-akademik-mahasiswa-bulk-terminal',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'bulkTerminal'])->name('akademik.data-akademik-mahasiswa-bulk-terminal');
    Route::get('/akademik/data-akademik-mahasiswa-export',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'exportDataAkademikMahasiswa'])->name('akademik.data-akademik-mahasiswa-export');
    Route::post('/akademik/data-akademik-mahasiswa-import',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'importDataAkademikMahasiswa'])->name('akademik.data-akademik-mahasiswa-import');
    Route::get('/akademik/data-akademik-mahasiswa-import-results',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'getImportResults'])->name('akademik.data-akademik-mahasiswa-import-results');
    Route::get('/akademik/get-takhasus-options',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'getTakhasusOptions'])->name('akademik.get-takhasus-options');
    Route::get('/akademik/data-akademik-mahasiswa-search',[App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'searchMahasiswa'])->name('akademik.data-akademik-mahasiswa-search');
    
    // MASTER AKADEMIK => KELAS
    Route::get('/akademik/kelas',[App\Http\Controllers\Master\Akademik\KelasController::class, 'renderKelas'])->name('akademik.kelas-render');
    Route::post('/akademik/kelas',[App\Http\Controllers\Master\Akademik\KelasController::class, 'handleKelas'])->name('akademik.kelas-handle');
    Route::patch('/akademik/kelas/{code}',[App\Http\Controllers\Master\Akademik\KelasController::class, 'updateKelas'])->name('akademik.kelas-update');
    Route::delete('/akademik/kelas/{code}',[App\Http\Controllers\Master\Akademik\KelasController::class, 'deleteKelas'])->name('akademik.kelas-delete');
    
    // MASTER AKADEMIK => KELAS MAHASISWA MANAGEMENT
    Route::get('/akademik/kelas/{code}/mahasiswa',[App\Http\Controllers\Master\Akademik\KelasController::class, 'viewStudents'])->name('akademik.kelas-students-view');
    Route::post('/akademik/kelas/{code}/mahasiswa/add',[App\Http\Controllers\Master\Akademik\KelasController::class, 'addStudents'])->name('akademik.kelas-students-add');
    Route::delete('/akademik/kelas/{code}/mahasiswa',[App\Http\Controllers\Master\Akademik\KelasController::class, 'removeStudents'])->name('akademik.kelas-students-remove');
    Route::delete('/akademik/kelas/{code}/mahasiswa/{studentId}',[App\Http\Controllers\Master\Akademik\KelasController::class, 'removeStudent'])->name('akademik.kelas-student-remove');
    Route::get('/akademik/kelas/{code}/mahasiswa/available',[App\Http\Controllers\Master\Akademik\KelasController::class, 'getAvailableStudents'])->name('akademik.kelas-students-available');
    
    // MASTER AKADEMIK => JADWAL KULIAH
    Route::get('/akademik/jadwal-kuliah',[App\Http\Controllers\Master\Akademik\JadwalKuliahController::class, 'renderJadwalKuliah'])->name('akademik.jadwal-kuliah-render');
    Route::post('/akademik/jadwal-kuliah',[App\Http\Controllers\Master\Akademik\JadwalKuliahController::class, 'handleJadwalKuliah'])->name('akademik.jadwal-kuliah-handle');
    Route::patch('/akademik/jadwal-kuliah/{code}',[App\Http\Controllers\Master\Akademik\JadwalKuliahController::class, 'updateJadwalKuliah'])->name('akademik.jadwal-kuliah-update');
    Route::delete('/akademik/jadwal-kuliah/{code}',[App\Http\Controllers\Master\Akademik\JadwalKuliahController::class, 'deleteJadwalKuliah'])->name('akademik.jadwal-kuliah-delete');
    Route::get('akademik/get-waktu-kuliah/{jenis_kelas_id}', [App\Http\Controllers\Master\Akademik\JadwalKuliahController::class, 'getWaktuKuliahByJenisKelas'])->name('akademik.get-waktu-kuliah');

    // MASTER AKADEMIK => JENIS KELAS
    Route::get('/akademik/jenis-kelas',[App\Http\Controllers\Master\Akademik\JenisKelasController::class, 'renderJenisKelas'])->name('akademik.jenis-kelas-render');
    Route::post('/akademik/jenis-kelas',[App\Http\Controllers\Master\Akademik\JenisKelasController::class, 'handleJenisKelas'])->name('akademik.jenis-kelas-handle');
    Route::patch('/akademik/jenis-kelas/{code}',[App\Http\Controllers\Master\Akademik\JenisKelasController::class, 'updateJenisKelas'])->name('akademik.jenis-kelas-update');
    Route::delete('/akademik/jenis-kelas/{code}',[App\Http\Controllers\Master\Akademik\JenisKelasController::class, 'deleteJenisKelas'])->name('akademik.jenis-kelas-delete');
    
    // MASTER AKADEMIK => WAKTU KULIAH
    Route::get('/akademik/waktu-kuliah',[App\Http\Controllers\Master\Akademik\WaktuKuliahController::class, 'renderWaktuKuliah'])->name('akademik.waktu-kuliah-render');
    Route::post('/akademik/waktu-kuliah',[App\Http\Controllers\Master\Akademik\WaktuKuliahController::class, 'handleWaktuKuliah'])->name('akademik.waktu-kuliah-handle');
    Route::patch('/akademik/waktu-kuliah/{code}',[App\Http\Controllers\Master\Akademik\WaktuKuliahController::class, 'updateWaktuKuliah'])->name('akademik.waktu-kuliah-update');
    Route::delete('/akademik/waktu-kuliah/{code}',[App\Http\Controllers\Master\Akademik\WaktuKuliahController::class, 'deleteWaktuKuliah'])->name('akademik.waktu-kuliah-delete');

    // MASTER PMB => SYARAT PENDAFTARAN
    Route::get('/pmb/syarat',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'index'])->name('pmb.syarat-render');
    Route::post('/pmb/syarat',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'store'])->name('pmb.syarat-handle');
    Route::patch('/pmb/syarat/{id}',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'update'])->name('pmb.syarat-update');
    Route::delete('/pmb/syarat/{id}',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'destroy'])->name('pmb.syarat-delete');
    Route::get('/pmb/syarat/{id}/toggle-active',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'toggleActive'])->name('pmb.syarat-toggle-active');
    
    // MASTER PMB => REVIEW UPLOAD DOKUMEN
    Route::get('/pmb/syarat/uploads',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'uploads'])->name('pmb.syarat-uploads');
    Route::post('/pmb/syarat/uploads/{id}/approve',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'approveUpload'])->name('pmb.syarat-approve-upload');
    Route::post('/pmb/syarat/uploads/{id}/reject',[App\Http\Controllers\Master\PMB\SyaratPendaftaranController::class, 'rejectUpload'])->name('pmb.syarat-reject-upload');

    // MASTER PMB => VALIDASI DOKUMEN PENDAFTAR
    Route::patch('/pmb/pendaftar/{code}/validasi', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'validasiDokumen'])->name('pmb.pendaftar-validasi');
    Route::patch('/pmb/pendaftar/{code}/batch-validasi', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'batchValidasiDokumen'])->name('pmb.pendaftar-batch-validasi');

    // MASTER PMB => PMB MANAGEMENT (NEW UNIFIED INTERFACE)
    Route::get('/pmb/management', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'index'])->name('pmb.management-index');
    
    
    // MASTER PMB => DASHBOARD PMB
    Route::get('/dashboard-pmb', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'index'])->name('pmb.dashboard');
    Route::get('/pmb/dashboard', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'index'])->name('pmb.dashboard-alt');
    Route::post('/pmb/management/periode', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'storePeriode'])->name('pmb.management.store-periode');
    Route::patch('/pmb/management/periode/{code}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'updatePeriode'])->name('pmb.management.update-periode');
    Route::delete('/pmb/management/periode/{code}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'deletePeriode'])->name('pmb.management.delete-periode');
    Route::post('/pmb/management/jalur', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'storeJalur'])->name('pmb.management.store-jalur');
    Route::patch('/pmb/management/jalur/{id}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'updateJalur'])->name('pmb.management.update-jalur');
    Route::delete('/pmb/management/jalur/{id}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'deleteJalur'])->name('pmb.management.delete-jalur');
    Route::post('/pmb/management/update-status', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'updatePendaftarStatus'])->name('pmb.management.update-status');
    Route::get('/pmb/management/export', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'exportPendaftar'])->name('pmb.management.export');
    
    // MASTER PMB => PENDAFTAR MANAGEMENT
    Route::get('/pmb/management/pendaftar/{id}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'viewPendaftar'])->name('pmb.management.view-pendaftar');
    Route::delete('/pmb/management/pendaftar/{id}', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'deletePendaftar'])->name('pmb.management.delete-pendaftar');
    Route::post('/pmb/management/pendaftar/{id}/approve', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'approvePendaftar'])->name('pmb.management.approve-pendaftar');
    Route::post('/pmb/management/pendaftar/{id}/reject', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'rejectPendaftar'])->name('pmb.management.reject-pendaftar');
    Route::post('/pmb/management/convert-to-mahasiswa', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'convertToMahasiswa'])->name('pmb.management.convert-to-mahasiswa');
    Route::post('/pmb/management/convert', [App\Http\Controllers\Master\PMB\PMBManagementController::class, 'convertToMahasiswa'])->name('pmb.management.convert');

    // MASTER PENGGUNA => USERS 
    Route::get('/pengguna/users',[App\Http\Controllers\Master\Pengguna\UsersController::class, 'renderUsers'])->name('pengguna.users-render');
    Route::get('/pengguna/users/{code}/views',[App\Http\Controllers\Master\Pengguna\UsersController::class, 'viewUsers'])->name('pengguna.users-views');
    Route::post('/pengguna/users',[App\Http\Controllers\Master\Pengguna\UsersController::class, 'handleUsers'])->name('pengguna.users-handle');
    Route::patch('/pengguna/users/{code}',[App\Http\Controllers\Master\Pengguna\UsersController::class, 'updateUsers'])->name('pengguna.users-update');
    Route::patch('/pengguna/users/{code}/profile', [App\Http\Controllers\Master\Pengguna\UsersController::class, 'handleProfile'])->name('pengguna.users-profile');
    Route::delete('/pengguna/users/{code}',[App\Http\Controllers\Master\Pengguna\UsersController::class, 'deleteUsers'])->name('pengguna.users-delete');

    // MASTER PENGGUNA => USERS 
    Route::get('/pengguna/dosen',[App\Http\Controllers\Master\Pengguna\DosenController::class, 'renderDosen'])->name('pengguna.dosen-render');
    Route::get('/pengguna/dosen/{code}/views',[App\Http\Controllers\Master\Pengguna\DosenController::class, 'viewDosen'])->name('pengguna.dosen-views');
    Route::post('/pengguna/dosen',[App\Http\Controllers\Master\Pengguna\DosenController::class, 'handleDosen'])->name('pengguna.dosen-handle');
    Route::patch('/pengguna/dosen/{code}',[App\Http\Controllers\Master\Pengguna\DosenController::class, 'updateDosen'])->name('pengguna.dosen-update');
    Route::patch('/pengguna/dosen/{code}/profile', [App\Http\Controllers\Master\Pengguna\DosenController::class, 'handleProfile'])->name('pengguna.dosen-profile');
    Route::delete('/pengguna/dosen/{code}',[App\Http\Controllers\Master\Pengguna\DosenController::class, 'deleteDosen'])->name('pengguna.dosen-delete');

    // MASTER PENGGUNA => MAHASISWA
    Route::get('/pengguna/mahasiswa',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'renderMahasiswa'])->name('pengguna.mahasiswa-render');
    Route::get('/pengguna/mahasiswa/{code}/views',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'viewMahasiswa'])->name('pengguna.mahasiswa-views');
    Route::post('/pengguna/mahasiswa',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'handleMahasiswa'])->name('pengguna.mahasiswa-handle');
    Route::patch('/pengguna/mahasiswa/{code}',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'updateMahasiswa'])->name('pengguna.mahasiswa-update');
    Route::patch('/pengguna/mahasiswa/{code}/profile', [App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'handleProfile'])->name('pengguna.mahasiswa-profile');
    Route::delete('/pengguna/mahasiswa/{code}',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'deleteMahasiswa'])->name('pengguna.mahasiswa-delete');
    Route::delete('/pengguna/mahasiswa/{code}/soft-delete',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'softDeleteMahasiswa'])->name('pengguna.mahasiswa-soft-delete');
    Route::patch('/pengguna/mahasiswa/{code}/restore',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'restoreMahasiswa'])->name('pengguna.mahasiswa-restore');
    Route::patch('/pengguna/mahasiswa/{code}/reset-password',[App\Http\Controllers\Master\Pengguna\MahasiswaController::class, 'resetPassword'])->name('pengguna.mahasiswa-reset-password');

    // MASTER PUBLIKASI => KATEGORI
    Route::get('/publikasi/kategori', [App\Http\Controllers\Master\Publikasi\KategoriController::class, 'renderKategori'])->name('publikasi.kategori-render');
    Route::post('/publikasi/kategori', [App\Http\Controllers\Master\Publikasi\KategoriController::class, 'handleKategori'])->name('publikasi.kategori-handle');
    Route::patch('/publikasi/kategori/{code}', [App\Http\Controllers\Master\Publikasi\KategoriController::class, 'updateKategori'])->name('publikasi.kategori-update');
    Route::delete('/publikasi/kategori/{code}', [App\Http\Controllers\Master\Publikasi\KategoriController::class, 'deleteKategori'])->name('publikasi.kategori-delete');

    // MASTER PUBLIKASI => BERITA
    Route::get('/publikasi/berita', [App\Http\Controllers\Master\Publikasi\BeritaController::class, 'renderBerita'])->name('publikasi.berita-render');
    Route::get('/publikasi/berita/{slug}/view', [App\Http\Controllers\Master\Publikasi\BeritaController::class, 'viewBerita'])->name('publikasi.berita-view');
    Route::post('/publikasi/berita', [App\Http\Controllers\Master\Publikasi\BeritaController::class, 'handleBerita'])->name('publikasi.berita-handle');
    Route::patch('/publikasi/berita/{code}', [App\Http\Controllers\Master\Publikasi\BeritaController::class, 'updateBerita'])->name('publikasi.berita-update');
    Route::delete('/publikasi/berita/{code}', [App\Http\Controllers\Master\Publikasi\BeritaController::class, 'deleteBerita'])->name('publikasi.berita-delete');

    // MASTER PUBLIKASI => PENGUMUMAN
    Route::get('/publikasi/pengumuman', [App\Http\Controllers\Master\Publikasi\PengumumanController::class, 'renderPengumuman'])->name('publikasi.pengumuman-render');
    Route::get('/publikasi/pengumuman/{slug}/view', [App\Http\Controllers\Master\Publikasi\PengumumanController::class, 'viewPengumuman'])->name('publikasi.pengumuman-view');
    Route::post('/publikasi/pengumuman', [App\Http\Controllers\Master\Publikasi\PengumumanController::class, 'handlePengumuman'])->name('publikasi.pengumuman-handle');
    Route::patch('/publikasi/pengumuman/{code}', [App\Http\Controllers\Master\Publikasi\PengumumanController::class, 'updatePengumuman'])->name('publikasi.pengumuman-update');
    Route::delete('/publikasi/pengumuman/{code}', [App\Http\Controllers\Master\Publikasi\PengumumanController::class, 'deletePengumuman'])->name('publikasi.pengumuman-delete');

    // MASTER PUBLIKASI => GALERI
    Route::get('/publikasi/galeri', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'renderGaleri'])->name('publikasi.galeri-render');
    Route::get('/publikasi/galeri/{code}/view', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'viewGaleri'])->name('publikasi.galeri-view');
    Route::post('/publikasi/galeri', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'handleGaleri'])->name('publikasi.galeri-handle');
    Route::patch('/publikasi/galeri/{code}', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'updateGaleri'])->name('publikasi.galeri-update');
    Route::delete('/publikasi/galeri/{code}', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'deleteGaleri'])->name('publikasi.galeri-delete');

    // MASTER PUBLIKASI => GALERI FOTO
    Route::post('/publikasi/galeri/{code}/foto', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'handleFoto'])->name('publikasi.galeri-foto-handle');
    Route::delete('/publikasi/galeri/foto/{code}', [App\Http\Controllers\Master\Publikasi\GaleriController::class, 'deleteFoto'])->name('publikasi.galeri-foto-delete');

    // MASTER PUBLIKASI => PROGRAM STUDI
    Route::get('/publikasi/program-studi', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'index'])->name('master.publikasi.program-studi.index');
    Route::get('/publikasi/program-studi/create', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'create'])->name('master.publikasi.program-studi.create');
    Route::post('/publikasi/program-studi', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'store'])->name('master.publikasi.program-studi.store');
    Route::get('/publikasi/program-studi/{programStudi}', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'show'])->name('master.publikasi.program-studi.show');
    Route::get('/publikasi/program-studi/{programStudi}/edit', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'edit'])->name('master.publikasi.program-studi.edit');
    Route::put('/publikasi/program-studi/{programStudi}', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'update'])->name('master.publikasi.program-studi.update');
    Route::delete('/publikasi/program-studi/{programStudi}', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'destroy'])->name('master.publikasi.program-studi.destroy');
    Route::patch('/publikasi/program-studi/{programStudi}/toggle-status', [App\Http\Controllers\Master\Publikasi\ProgramStudiController::class, 'toggleStatus'])->name('master.publikasi.program-studi.toggle-status');

    // MASTER PENGATURAN => WEB SETTINGS
    Route::get('/pengaturan/web-settings', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'renderIndex'])->name('pengaturan.web-settings-render');
    Route::patch('/pengaturan/web-settings', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'handleSettings'])->name('pengaturan.web-settings-handle');
    Route::get('/pengaturan/export-settings', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'exportDatabase'])->name('pengaturan.export-database');
    Route::post('/pengaturan/import-settings', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'importDatabase'])->name('pengaturan.import-database');
    Route::get('/pengaturan/web-settings/whatsapp-status', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'getWhatsappStatus'])->name('pengaturan.web-settings.whatsapp-status');
    
    // MASTER PENGATURAN => HOMEPAGE SETTINGS
    Route::get('/pengaturan/homepage', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'renderHomepageSetting'])->name('pengaturan.homepage-render');
    Route::post('/pengaturan/homepage', [App\Http\Controllers\Master\Pengaturan\WebSettingController::class, 'updateHomepageSetting'])->name('pengaturan.homepage-update');

    // MASTER PENGATURAN => LOG AKTIVITAS
    Route::get('/pengaturan/log-aktivitas', [App\Http\Controllers\Master\Pengaturan\LogAktivitasController::class, 'renderLogAktivitas'])->name('pengaturan.log-aktivitas-render');
    Route::get('/pengaturan/log-aktivitas/{id}/view', [App\Http\Controllers\Master\Pengaturan\LogAktivitasController::class, 'viewLogAktivitas'])->name('pengaturan.log-aktivitas-view');
    Route::get('/pengaturan/log-aktivitas/filter', [App\Http\Controllers\Master\Pengaturan\LogAktivitasController::class, 'filterLogAktivitas'])->name('pengaturan.log-aktivitas-filter');
    Route::delete('/pengaturan/log-aktivitas/{id}', [App\Http\Controllers\Master\Pengaturan\LogAktivitasController::class, 'deleteLogAktivitas'])->name('pengaturan.log-aktivitas-delete');

    // MASTER INFRASTRUKTUR => GEDUNG
    Route::get('/infrastruktur/gedung', [App\Http\Controllers\Master\Infrastruktur\GedungController::class, 'renderGedung'])->name('infrastruktur.gedung-render');
    Route::post('/infrastruktur/gedung', [App\Http\Controllers\Master\Infrastruktur\GedungController::class, 'handleGedung'])->name('infrastruktur.gedung-handle');
    Route::patch('/infrastruktur/gedung/{code}', [App\Http\Controllers\Master\Infrastruktur\GedungController::class, 'updateGedung'])->name('infrastruktur.gedung-update');
    Route::delete('/infrastruktur/gedung/{code}', [App\Http\Controllers\Master\Infrastruktur\GedungController::class, 'deleteGedung'])->name('infrastruktur.gedung-delete');

    // MASTER INFRASTRUKTUR => RUANG
    Route::get('/infrastruktur/ruang', [App\Http\Controllers\Master\Infrastruktur\RuangController::class, 'renderRuang'])->name('infrastruktur.ruang-render');
    Route::post('/infrastruktur/ruang', [App\Http\Controllers\Master\Infrastruktur\RuangController::class, 'handleRuang'])->name('infrastruktur.ruang-handle');
    Route::patch('/infrastruktur/ruang/{code}', [App\Http\Controllers\Master\Infrastruktur\RuangController::class, 'updateRuang'])->name('infrastruktur.ruang-update');
    Route::delete('/infrastruktur/ruang/{code}', [App\Http\Controllers\Master\Infrastruktur\RuangController::class, 'deleteRuang'])->name('infrastruktur.ruang-delete');

    // MASTER INFRASTRUKTUR => KATEGORI BARANG
    Route::get('/infrastruktur/kategori-barang', [App\Http\Controllers\Master\Infrastruktur\KategoriBarangController::class, 'renderKategoriBarang'])->name('infrastruktur.kategori-barang-render');
    Route::post('/infrastruktur/kategori-barang', [App\Http\Controllers\Master\Infrastruktur\KategoriBarangController::class, 'handleKategoriBarang'])->name('infrastruktur.kategori-barang-handle');
    Route::patch('/infrastruktur/kategori-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\KategoriBarangController::class, 'updateKategoriBarang'])->name('infrastruktur.kategori-barang-update');
    Route::delete('/infrastruktur/kategori-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\KategoriBarangController::class, 'deleteKategoriBarang'])->name('infrastruktur.kategori-barang-delete');

    // MASTER INFRASTRUKTUR => BARANG
    Route::get('/infrastruktur/barang', [App\Http\Controllers\Master\Infrastruktur\BarangController::class, 'renderBarang'])->name('infrastruktur.barang-render');
    Route::post('/infrastruktur/barang', [App\Http\Controllers\Master\Infrastruktur\BarangController::class, 'handleBarang'])->name('infrastruktur.barang-handle');
    Route::patch('/infrastruktur/barang/{code}', [App\Http\Controllers\Master\Infrastruktur\BarangController::class, 'updateBarang'])->name('infrastruktur.barang-update');
    Route::delete('/infrastruktur/barang/{code}', [App\Http\Controllers\Master\Infrastruktur\BarangController::class, 'deleteBarang'])->name('infrastruktur.barang-delete');

    // MASTER INFRASTRUKTUR => MUTASI BARANG
    Route::get('/infrastruktur/mutasi-barang', [App\Http\Controllers\Master\Infrastruktur\MutasiBarangController::class, 'renderMutasiBarang'])->name('infrastruktur.mutasi-barang-render');
    Route::post('/infrastruktur/mutasi-barang', [App\Http\Controllers\Master\Infrastruktur\MutasiBarangController::class, 'handleMutasiBarang'])->name('infrastruktur.mutasi-barang-handle');
    Route::patch('/infrastruktur/mutasi-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\MutasiBarangController::class, 'updateMutasiBarang'])->name('infrastruktur.mutasi-barang-update');
    Route::delete('/infrastruktur/mutasi-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\MutasiBarangController::class, 'deleteMutasiBarang'])->name('infrastruktur.mutasi-barang-delete');

    // MASTER INFRASTRUKTUR => PENGADAAN BARANG
    Route::get('/infrastruktur/pengadaan-barang', [App\Http\Controllers\Master\Infrastruktur\PengadaanBarangController::class, 'renderPengadaanBarang'])->name('infrastruktur.pengadaan-barang-render');
    Route::post('/infrastruktur/pengadaan-barang', [App\Http\Controllers\Master\Infrastruktur\PengadaanBarangController::class, 'handlePengadaanBarang'])->name('infrastruktur.pengadaan-barang-handle');
    Route::patch('/infrastruktur/pengadaan-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\PengadaanBarangController::class, 'updatePengadaanBarang'])->name('infrastruktur.pengadaan-barang-update');
    Route::delete('/infrastruktur/pengadaan-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\PengadaanBarangController::class, 'deletePengadaanBarang'])->name('infrastruktur.pengadaan-barang-delete');

    // MASTER INFRASTRUKTUR => INVENTARIS BARANG
    Route::get('/infrastruktur/inventaris-barang', [App\Http\Controllers\Master\Infrastruktur\InventarisBarangController::class, 'renderInventarisBarang'])->name('infrastruktur.inventaris-barang-render');
    Route::post('/infrastruktur/inventaris-barang', [App\Http\Controllers\Master\Infrastruktur\InventarisBarangController::class, 'handleInventarisBarang'])->name('infrastruktur.inventaris-barang-handle');
    Route::patch('/infrastruktur/inventaris-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\InventarisBarangController::class, 'updateInventarisBarang'])->name('infrastruktur.inventaris-barang-update');
    Route::delete('/infrastruktur/inventaris-barang/{code}', [App\Http\Controllers\Master\Infrastruktur\InventarisBarangController::class, 'deleteInventarisBarang'])->name('infrastruktur.inventaris-barang-delete');

    // MASTER KEUANGAN => SALDO
    Route::get('/keuangan/saldo', [App\Http\Controllers\Master\Keuangan\SaldoController::class, 'renderSaldo'])->name('keuangan.saldo-render');
    Route::post('/keuangan/saldo', [App\Http\Controllers\Master\Keuangan\SaldoController::class, 'handleSaldo'])->name('keuangan.saldo-handle');
    Route::patch('/keuangan/saldo/{code}', [App\Http\Controllers\Master\Keuangan\SaldoController::class, 'updateSaldo'])->name('keuangan.saldo-update');
    Route::delete('/keuangan/saldo/{code}', [App\Http\Controllers\Master\Keuangan\SaldoController::class, 'deleteSaldo'])->name('keuangan.saldo-delete');

    // MASTER KEUANGAN => TAGIHAN KULIAH GROUP
    Route::get('/keuangan/tagihan-kuliah-group', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'renderTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-render');
    Route::post('/keuangan/tagihan-kuliah-group', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'handleTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-handle');
    Route::patch('/keuangan/tagihan-kuliah-group/{code}', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'updateTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-update');
    Route::delete('/keuangan/tagihan-kuliah-group/{code}', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'deleteTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-delete');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/publish', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'publishTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-publish');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/archive', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'archiveTagihanKuliahGroup'])->name('keuangan.tagihan-kuliah-group-archive');
    Route::get('/keuangan/tagihan-kuliah-group/{code}/detail', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'viewTagihanDetail'])->name('keuangan.tagihan-kuliah-group-detail');
    
    // MASTER KEUANGAN => TAGIHAN KULIAH GROUP - BULK OPERATIONS
    Route::get('/keuangan/tagihan-kuliah-group/{code}/bulk-operations', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'bulkOperationsView'])->name('keuangan.tagihan-kuliah-group-bulk-operations');
    Route::get('/keuangan/tagihan-kuliah-group/{code}/tagihan', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'getGroupTagihan'])->name('keuangan.tagihan-kuliah-group-get-tagihan');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/bulk-delete', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'bulkDeleteTagihan'])->name('keuangan.tagihan-kuliah-group-bulk-delete');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/bulk-update-amount', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'bulkUpdateAmount'])->name('keuangan.tagihan-kuliah-group-bulk-update-amount');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/bulk-update-due-date', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'bulkUpdateDueDate'])->name('keuangan.tagihan-kuliah-group-bulk-update-due-date');
    Route::post('/keuangan/tagihan-kuliah-group/{code}/recall', [App\Http\Controllers\Master\Keuangan\TagihanKuliahGroupController::class, 'recallGroup'])->name('keuangan.tagihan-kuliah-group-recall');

    // MASTER KEUANGAN => TAGIHAN INDIVIDUAL
    Route::get('/keuangan/tagihan-individual', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'index'])->name('keuangan.tagihan-individual-index');
    Route::get('/keuangan/tagihan-individual/{id}/mahasiswa', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'viewMahasiswaTagihan'])->name('keuangan.tagihan-individual-mahasiswa');
    Route::get('/keuangan/tagihan-individual/ajax/mahasiswa/{id}/tagihan', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'getMahasiswaTagihan'])->name('keuangan.tagihan-individual-ajax-tagihan');
    Route::post('/keuangan/tagihan-individual', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'store'])->name('keuangan.tagihan-individual-store');
    Route::put('/keuangan/tagihan-individual/{id}', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'update'])->name('keuangan.tagihan-individual-update');
    Route::delete('/keuangan/tagihan-individual/{id}', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'destroy'])->name('keuangan.tagihan-individual-destroy');
    Route::post('/keuangan/tagihan-individual/{id}/mark-paid', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'markAsPaid'])->name('keuangan.tagihan-individual-mark-paid');
    Route::get('/keuangan/tagihan-individual/export', [App\Http\Controllers\Master\Keuangan\TagihanIndividualController::class, 'export'])->name('keuangan.tagihan-individual-export');

    // MASTER KEUANGAN => PAYMENT METHODS
    Route::get('/keuangan/payment-methods', [App\Http\Controllers\Master\Keuangan\PaymentMethodController::class, 'index'])->name('keuangan.payment-methods-index');
    Route::post('/keuangan/payment-methods', [App\Http\Controllers\Master\Keuangan\PaymentMethodController::class, 'store'])->name('keuangan.payment-methods-store');
    Route::patch('/keuangan/payment-methods/{code}', [App\Http\Controllers\Master\Keuangan\PaymentMethodController::class, 'update'])->name('keuangan.payment-methods-update');
    Route::delete('/keuangan/payment-methods/{code}', [App\Http\Controllers\Master\Keuangan\PaymentMethodController::class, 'destroy'])->name('keuangan.payment-methods-destroy');
    Route::post('/keuangan/payment-methods/{code}/toggle-status', [App\Http\Controllers\Master\Keuangan\PaymentMethodController::class, 'toggleStatus'])->name('keuangan.payment-methods-toggle-status');

    // MASTER KEUANGAN => BANK ACCOUNTS
    Route::get('/keuangan/bank-accounts', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'index'])->name('keuangan.bank-accounts-index');
    Route::post('/keuangan/bank-accounts', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'store'])->name('keuangan.bank-accounts-store');
    Route::patch('/keuangan/bank-accounts/{code}', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'update'])->name('keuangan.bank-accounts-update');
    Route::delete('/keuangan/bank-accounts/{code}', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'destroy'])->name('keuangan.bank-accounts-destroy');
    Route::post('/keuangan/bank-accounts/{code}/toggle-status', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'toggleStatus'])->name('keuangan.bank-accounts-toggle-status');
    Route::post('/keuangan/bank-accounts/{code}/set-primary', [App\Http\Controllers\Master\Keuangan\BankAccountController::class, 'setPrimary'])->name('keuangan.bank-accounts-set-primary');

    // MASTER KEUANGAN => BILLING TEMPLATES
    Route::get('/keuangan/billing-templates', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'index'])->name('keuangan.billing-templates.index');
    Route::post('/keuangan/billing-templates', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'store'])->name('keuangan.billing-templates.store');
    Route::get('/keuangan/billing-templates/get-form-data', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'getFormData'])->name('keuangan.billing-templates.get-form-data');
    Route::get('/keuangan/billing-templates/{id}', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'show'])->name('keuangan.billing-templates.show');
    Route::put('/keuangan/billing-templates/{id}', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'update'])->name('keuangan.billing-templates.update');
    Route::delete('/keuangan/billing-templates/{id}', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'destroy'])->name('keuangan.billing-templates.destroy');
    Route::get('/keuangan/billing-templates/{id}/preview', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'preview'])->name('keuangan.billing-templates.preview');
    Route::post('/keuangan/billing-templates/{id}/generate', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'generate'])->name('keuangan.billing-templates.generate');
    Route::post('/keuangan/billing-templates/bulk-action', [App\Http\Controllers\Master\Keuangan\BillingTemplateController::class, 'bulkAction'])->name('keuangan.billing-templates.bulk-action');

    // MASTER KEUANGAN => PEMBAYARAN MANUAL
    Route::prefix('keuangan/pembayaran-manual')->name('keuangan.pembayaran-manual-')->group(function () {
        // Main CRUD routes
        Route::get('/', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'index'])->name('index');
        Route::get('/history', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'history'])->name('history');
        Route::get('/create', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'create'])->name('create');

        // Verification queue routes
        Route::get('/verifikasi', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'verificationQueue'])->name('verification-queue');
        Route::post('/verifikasi/{verificationId}/approve', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'approveVerification'])->name('verification-approve');
        Route::post('/verifikasi/{verificationId}/reject', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'rejectVerification'])->name('verification-reject');
        Route::post('/verifikasi/approve-bulk', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'approveVerificationBulk'])->name('verification-approve-bulk');
        Route::post('/verifikasi/reject-bulk', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'rejectVerificationBulk'])->name('verification-reject-bulk');

        Route::post('/', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'destroy'])->name('destroy');
        
        // AJAX routes for dynamic data
        Route::get('/ajax/search-mahasiswa', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'searchMahasiswa'])->name('search-mahasiswa');
        Route::get('/ajax/mahasiswa/{mahasiswaId}/tagihan', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'getMahasiswaTagihan'])->name('get-mahasiswa-tagihan');
        
        // Receipt download
        Route::get('/{id}/receipt', [App\Http\Controllers\Master\Keuangan\PembayaranManualController::class, 'downloadReceipt'])->name('download-receipt');
    });

    // MASTER KEUANGAN => LAPORAN KEUANGAN
    Route::prefix('keuangan/laporan')->name('keuangan.laporan-')->group(function () {
        Route::get('/', [App\Http\Controllers\Master\Keuangan\LaporanKeuanganController::class, 'index'])->name('index');
        Route::get('/detail', [App\Http\Controllers\Master\Keuangan\LaporanKeuanganController::class, 'detail'])->name('detail');
        Route::get('/export', [App\Http\Controllers\Master\Keuangan\LaporanKeuanganController::class, 'export'])->name('export');
    });

    // MASTER KEUANGAN => ANALISIS PEMBAYARAN
    Route::prefix('keuangan/analisis')->name('keuangan.analisis-')->group(function () {
        Route::get('/', [App\Http\Controllers\Master\Keuangan\AnalisisPembayaranController::class, 'index'])->name('index');
        Route::get('/trends', [App\Http\Controllers\Master\Keuangan\AnalisisPembayaranController::class, 'trends'])->name('trends');
        Route::get('/export', [App\Http\Controllers\Master\Keuangan\AnalisisPembayaranController::class, 'export'])->name('export');
    });

    // AKADEMIK - KRS MANAGEMENT
    Route::get('/akademik/krs', [App\Http\Controllers\Master\Akademik\KrsController::class, 'index'])->name('akademik.krs-render');

    // PINDAHKAN RUTE EXPORT & BULK KE SINI (Di atas {id})
    Route::get('/akademik/krs/export', [App\Http\Controllers\Master\Akademik\KrsController::class, 'export'])->name('akademik.krs-export');
    Route::post('/akademik/krs/bulk-approve', [App\Http\Controllers\Master\Akademik\KrsController::class, 'bulkApprove'])->name('akademik.krs-bulk-approve');

    // RUTE DENGAN PARAMETER {id} DITARUH DI BAWAHNYA
    Route::get('/akademik/krs/{id}', [App\Http\Controllers\Master\Akademik\KrsController::class, 'show'])->name('akademik.krs-show');
    Route::patch('/akademik/krs/{id}/approve', [App\Http\Controllers\Master\Akademik\KrsController::class, 'approve'])->name('akademik.krs-approve');
    Route::patch('/akademik/krs/{id}/reject', [App\Http\Controllers\Master\Akademik\KrsController::class, 'reject'])->name('akademik.krs-reject');
    Route::patch('/akademik/krs/{id}/reopen', [App\Http\Controllers\Master\Akademik\KrsController::class, 'reopen'])->name('akademik.krs-reopen');

    // AKADEMIK - AUTO TERMINAL MANAGEMENT (integrated with existing system)
    Route::post('/akademik/data-akademik-mahasiswa-auto-terminal', [App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'autoTerminalCheck'])->name('akademik.data-akademik-mahasiswa-auto-terminal');
    Route::get('/akademik/data-akademik-mahasiswa-terminal-status/{code}', [App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'getTerminalStatus'])->name('akademik.data-akademik-mahasiswa-terminal-status');
    
    // AKADEMIK - GRADUATION MANAGEMENT
    Route::post('/akademik/data-akademik-mahasiswa/{code}/graduate', [App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'graduateStudent'])->name('akademik.data-akademik-mahasiswa-graduate');
    Route::post('/akademik/data-akademik-mahasiswa/bulk-graduate', [App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'bulkGraduate'])->name('akademik.data-akademik-mahasiswa-bulk-graduate');
    Route::get('/akademik/data-akademik-mahasiswa/graduation-candidates', [App\Http\Controllers\Master\Akademik\DataAkademikMahasiswaController::class, 'getGraduationCandidates'])->name('akademik.data-akademik-mahasiswa-graduation-candidates');

    // MASTER AKADEMIK => BOBOT NILAI SETTINGS
    Route::get('/akademik/bobot-nilai', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'index'])->name('akademik.bobot-nilai-render');
    Route::post('/akademik/bobot-nilai', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'update'])->name('akademik.bobot-nilai-update');
    Route::post('/akademik/bobot-nilai/reset', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'resetToDefault'])->name('akademik.bobot-nilai-reset');
    
    // GRADE SCALE MANAGEMENT
    Route::post('/akademik/bobot-nilai/grade-scales', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'updateGradeScales'])->name('akademik.grade-scales-update');
    Route::post('/akademik/bobot-nilai/grade-scales/reset', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'resetGradeScalesToDefault'])->name('akademik.grade-scales-reset');
    
    Route::get('/akademik/bobot-nilai/api/settings', [App\Http\Controllers\Master\Akademik\BobotNilaiController::class, 'getSettings'])->name('akademik.bobot-nilai-api-settings');

    // MASTER AKADEMIK => REKAP ABSENSI
    Route::get('/akademik/rekap-absensi', [App\Http\Controllers\Master\Akademik\AbsensiController::class, 'rekap'])->name('akademik.rekap-absensi-render');
    Route::get('/akademik/rekap-absensi/export', [App\Http\Controllers\Master\Akademik\AbsensiController::class, 'exportExcel'])->name('akademik.rekap-absensi-export');
    // TAMBAHKAN RUTE INI UNTUK EXPORT KELAS & SEMESTER
    Route::get('/akademik/rekap-absensi/export-semester', [App\Http\Controllers\Master\Akademik\AbsensiController::class, 'exportExcelSemester'])->name('akademik.rekap-absensi-export-semester');
    Route::post('/akademik/rekap-absensi/import', [App\Http\Controllers\Master\Akademik\AbsensiController::class, 'importExcel'])->name('akademik.rekap-absensi-import');

    
    // MASTER AKADEMIK => DOSEN PENASEHAT
    Route::get('/akademik/dosen-penasehat', [App\Http\Controllers\Master\Akademik\DosenPenasehatController::class, 'index'])->name('akademik.dosen-penasehat-render');
    Route::get('/akademik/dosen-penasehat/search', [App\Http\Controllers\Master\Akademik\DosenPenasehatController::class, 'searchAjax'])->name('akademik.dosen-penasehat-search');
    Route::post('/akademik/dosen-penasehat/assign', [App\Http\Controllers\Master\Akademik\DosenPenasehatController::class, 'assign'])->name('akademik.dosen-penasehat-assign');
    Route::post('/akademik/dosen-penasehat/remove', [App\Http\Controllers\Master\Akademik\DosenPenasehatController::class, 'remove'])->name('akademik.dosen-penasehat-remove');
    Route::post('/akademik/dosen-penasehat/bulk-assign', [App\Http\Controllers\Master\Akademik\DosenPenasehatController::class, 'bulkAssign'])->name('akademik.dosen-penasehat-bulk-assign');

    // MASTER AKADEMIK => GRADUATION SETTINGS
    Route::get('/akademik/graduation-settings', [App\Http\Controllers\Master\Akademik\GraduationSettingsController::class, 'index'])->name('akademik.graduation-settings-render');
    Route::put('/akademik/graduation-settings', [App\Http\Controllers\Master\Akademik\GraduationSettingsController::class, 'update'])->name('akademik.graduation-settings-update');

    // MASTER PUBLIKASI => INSTITUSI PAGES
    Route::get('/institusi/profile', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'renderProfile'])->name('institusi.profile-render');
    Route::post('/institusi/profile', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'handleProfile'])->name('institusi.profile-handle');
    
    Route::get('/institusi/vision-mission', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'renderVisionMission'])->name('institusi.vision-mission-render');
    Route::post('/institusi/vision-mission', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'handleVisionMission'])->name('institusi.vision-mission-handle');
    
    Route::get('/institusi/structure', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'renderStructure'])->name('institusi.structure-render');
    Route::post('/institusi/structure', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'handleStructure'])->name('institusi.structure-handle');
    
    Route::get('/institusi/facilities', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'renderFacilities'])->name('institusi.facilities-render');
    Route::post('/institusi/facilities', [App\Http\Controllers\Master\Publikasi\InstitusiController::class, 'handleFacilities'])->name('institusi.facilities-handle');
});
