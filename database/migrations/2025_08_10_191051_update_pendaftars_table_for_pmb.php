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
            // Add periode_id for period selection
            if (!Schema::hasColumn('pendaftars', 'periode_id')) {
                $table->unsignedBigInteger('periode_id')->nullable()->after('jalur_id');
            }
            
            // Authentication field
            if (!Schema::hasColumn('pendaftars', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            // Personal data
            if (!Schema::hasColumn('pendaftars', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('name');
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
            if (!Schema::hasColumn('pendaftars', 'photo')) {
                $table->string('photo')->default('default.jpg')->after('agama');
            }
            
            // Address data
            if (!Schema::hasColumn('pendaftars', 'alamat')) {
                $table->text('alamat')->nullable()->after('photo');
            }
            
            // Parent data
            if (!Schema::hasColumn('pendaftars', 'nama_ayah')) {
                $table->string('nama_ayah')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('pendaftars', 'nama_ibu')) {
                $table->string('nama_ibu')->nullable()->after('nama_ayah');
            }
            
            // Education background
            if (!Schema::hasColumn('pendaftars', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable()->after('nama_ibu');
            }
            if (!Schema::hasColumn('pendaftars', 'tahun_lulus')) {
                $table->year('tahun_lulus')->nullable()->after('asal_sekolah');
            }
            
            // Additional data
            if (!Schema::hasColumn('pendaftars', 'nik')) {
                $table->string('nik')->nullable()->after('tahun_lulus');
            }
            
            // Status tracking
            if (!Schema::hasColumn('pendaftars', 'status_validasi')) {
                $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('pendaftars', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('status_validasi');
            }
            if (!Schema::hasColumn('pendaftars', 'tanggal_diterima')) {
                $table->date('tanggal_diterima')->nullable()->after('catatan_admin');
            }
            if (!Schema::hasColumn('pendaftars', 'nim_diberikan')) {
                $table->string('nim_diberikan')->nullable()->after('tanggal_diterima');
            }
        });
        
        // Foreign key constraint for periode_id is skipped since it might already exist
        // The constraint was likely added in a previous migration or manually
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn([
                'periode_id', 'password', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
                'agama', 'photo', 'alamat', 'nama_ayah', 'nama_ibu', 'asal_sekolah', 
                'tahun_lulus', 'nik', 'status_validasi', 'catatan_admin', 
                'tanggal_diterima', 'nim_diberikan'
            ]);
        });
    }
};
