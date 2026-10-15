<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            // Add prodi_id for program studi selection
            if (!Schema::hasColumn('pendaftars', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable()->after('jalur_id');
            }
            
            // Authentication fields
            if (!Schema::hasColumn('pendaftars', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            // Personal data
            if (!Schema::hasColumn('pendaftars', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('no_hp');
            }
            if (!Schema::hasColumn('pendaftars', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('pendaftars', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('pendaftars', 'agama')) {
                $table->string('agama')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('pendaftars', 'kewarganegaraan')) {
                $table->string('kewarganegaraan')->default('Indonesia')->after('agama');
            }
            if (!Schema::hasColumn('pendaftars', 'golongan_darah')) {
                $table->string('golongan_darah')->nullable()->after('kewarganegaraan');
            }
            if (!Schema::hasColumn('pendaftars', 'tinggi_badan')) {
                $table->integer('tinggi_badan')->nullable()->after('golongan_darah');
            }
            if (!Schema::hasColumn('pendaftars', 'berat_badan')) {
                $table->integer('berat_badan')->nullable()->after('tinggi_badan');
            }
            if (!Schema::hasColumn('pendaftars', 'photo')) {
                $table->string('photo')->default('default.jpg')->after('berat_badan');
            }
            
            // KTP Address
            if (!Schema::hasColumn('pendaftars', 'alamat_ktp')) {
                $table->text('alamat_ktp')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('pendaftars', 'rt_ktp')) {
                $table->string('rt_ktp')->nullable()->after('alamat_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'rw_ktp')) {
                $table->string('rw_ktp')->nullable()->after('rt_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'kelurahan_ktp')) {
                $table->string('kelurahan_ktp')->nullable()->after('rw_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'kecamatan_ktp')) {
                $table->string('kecamatan_ktp')->nullable()->after('kelurahan_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'kota_ktp')) {
                $table->string('kota_ktp')->nullable()->after('kecamatan_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'provinsi_ktp')) {
                $table->string('provinsi_ktp')->nullable()->after('kota_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'kode_pos_ktp')) {
                $table->string('kode_pos_ktp')->nullable()->after('provinsi_ktp');
            }
            
            // Domicile Address
            if (!Schema::hasColumn('pendaftars', 'alamat_sama')) {
                $table->enum('alamat_sama', ['Ya', 'Tidak'])->default('Ya')->after('kode_pos_ktp');
            }
            if (!Schema::hasColumn('pendaftars', 'alamat_domisili')) {
                $table->text('alamat_domisili')->nullable()->after('alamat_sama');
            }
            if (!Schema::hasColumn('pendaftars', 'rt_domisili')) {
                $table->string('rt_domisili')->nullable()->after('alamat_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'rw_domisili')) {
                $table->string('rw_domisili')->nullable()->after('rt_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'kelurahan_domisili')) {
                $table->string('kelurahan_domisili')->nullable()->after('rw_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'kecamatan_domisili')) {
                $table->string('kecamatan_domisili')->nullable()->after('kelurahan_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'kota_domisili')) {
                $table->string('kota_domisili')->nullable()->after('kecamatan_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'provinsi_domisili')) {
                $table->string('provinsi_domisili')->nullable()->after('kota_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'kode_pos_domisili')) {
                $table->string('kode_pos_domisili')->nullable()->after('provinsi_domisili');
            }
            
            // Parent/Guardian data
            if (!Schema::hasColumn('pendaftars', 'nama_ayah')) {
                $table->string('nama_ayah')->nullable()->after('kode_pos_domisili');
            }
            if (!Schema::hasColumn('pendaftars', 'pekerjaan_ayah')) {
                $table->string('pekerjaan_ayah')->nullable()->after('nama_ayah');
            }
            if (!Schema::hasColumn('pendaftars', 'penghasilan_ayah')) {
                $table->string('penghasilan_ayah')->nullable()->after('pekerjaan_ayah');
            }
            if (!Schema::hasColumn('pendaftars', 'nama_ibu')) {
                $table->string('nama_ibu')->nullable()->after('penghasilan_ayah');
            }
            if (!Schema::hasColumn('pendaftars', 'pekerjaan_ibu')) {
                $table->string('pekerjaan_ibu')->nullable()->after('nama_ibu');
            }
            if (!Schema::hasColumn('pendaftars', 'penghasilan_ibu')) {
                $table->string('penghasilan_ibu')->nullable()->after('pekerjaan_ibu');
            }
            if (!Schema::hasColumn('pendaftars', 'nama_wali')) {
                $table->string('nama_wali')->nullable()->after('penghasilan_ibu');
            }
            if (!Schema::hasColumn('pendaftars', 'pekerjaan_wali')) {
                $table->string('pekerjaan_wali')->nullable()->after('nama_wali');
            }
            if (!Schema::hasColumn('pendaftars', 'penghasilan_wali')) {
                $table->string('penghasilan_wali')->nullable()->after('pekerjaan_wali');
            }
            
            // Education background
            if (!Schema::hasColumn('pendaftars', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable()->after('penghasilan_wali');
            }
            if (!Schema::hasColumn('pendaftars', 'jurusan_sekolah')) {
                $table->string('jurusan_sekolah')->nullable()->after('asal_sekolah');
            }
            if (!Schema::hasColumn('pendaftars', 'tahun_lulus')) {
                $table->year('tahun_lulus')->nullable()->after('jurusan_sekolah');
            }
            if (!Schema::hasColumn('pendaftars', 'nilai_rata_rata')) {
                $table->decimal('nilai_rata_rata', 4, 2)->nullable()->after('tahun_lulus');
            }
            
            // Additional data
            if (!Schema::hasColumn('pendaftars', 'nomor_kk')) {
                $table->string('nomor_kk')->nullable()->after('nilai_rata_rata');
            }
            if (!Schema::hasColumn('pendaftars', 'nik')) {
                $table->string('nik')->nullable()->after('nomor_kk');
            }
            if (!Schema::hasColumn('pendaftars', 'nisn')) {
                $table->string('nisn')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('pendaftars', 'hobi')) {
                $table->text('hobi')->nullable()->after('nisn');
            }
            if (!Schema::hasColumn('pendaftars', 'cita_cita')) {
                $table->text('cita_cita')->nullable()->after('hobi');
            }
            if (!Schema::hasColumn('pendaftars', 'motivasi')) {
                $table->text('motivasi')->nullable()->after('cita_cita');
            }
            
            // Status and tracking
            if (!Schema::hasColumn('pendaftars', 'status_validasi')) {
                $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('pendaftars', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('status_validasi');
            }
            if (!Schema::hasColumn('pendaftars', 'tanggal_diterima')) {
                $table->date('tanggal_diterima')->nullable()->after('catatan_admin');
            }
            if (!Schema::hasColumn('pendaftars', 'kelas_diterima')) {
                $table->string('kelas_diterima')->nullable()->after('tanggal_diterima');
            }
            if (!Schema::hasColumn('pendaftars', 'nim_diberikan')) {
                $table->string('nim_diberikan')->nullable()->after('kelas_diterima');
            }
        });
        
        // Foreign key constraint for prodi_id is skipped since it already exists
        // The constraint was likely added in a previous migration or manually
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn([
                'prodi_id', 'password', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 
                'kewarganegaraan', 'golongan_darah', 'tinggi_badan', 'berat_badan', 'photo',
                'alamat_ktp', 'rt_ktp', 'rw_ktp', 'kelurahan_ktp', 'kecamatan_ktp', 'kota_ktp', 
                'provinsi_ktp', 'kode_pos_ktp', 'alamat_sama', 'alamat_domisili', 'rt_domisili', 
                'rw_domisili', 'kelurahan_domisili', 'kecamatan_domisili', 'kota_domisili', 
                'provinsi_domisili', 'kode_pos_domisili', 'nama_ayah', 'pekerjaan_ayah', 
                'penghasilan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'nama_wali', 
                'pekerjaan_wali', 'penghasilan_wali', 'asal_sekolah', 'jurusan_sekolah', 
                'tahun_lulus', 'nilai_rata_rata', 'nomor_kk', 'nik', 'nisn', 'hobi', 'cita_cita', 
                'motivasi', 'status_validasi', 'catatan_admin', 'tanggal_diterima', 
                'kelas_diterima', 'nim_diberikan'
            ]);
        });
    }
};
