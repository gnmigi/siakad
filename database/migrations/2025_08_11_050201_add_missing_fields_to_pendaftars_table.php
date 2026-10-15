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
        Schema::create('pendaftars', function (Blueprint $table) {
            $table->id();
            
            // PMB Relations
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->unsignedBigInteger('jalur_id')->nullable();
            $table->unsignedBigInteger('mahasiswa_id')->default(0);
            $table->unsignedBigInteger('jenis_id')->default(1);
            $table->unsignedBigInteger('gelombang_id')->default(1);
            
            // Program Studi Choices
            $table->unsignedBigInteger('prodi_1');
            $table->unsignedBigInteger('prodi_2')->nullable();
            $table->unsignedBigInteger('prodi_3')->nullable();
            $table->unsignedBigInteger('prodi_id')->nullable(); // Final assigned prodi
            
            // Basic Info
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password')->nullable();
            
            // Personal Data
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('agama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nik', 16)->nullable()->unique();
            
            // Parent Data
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            
            // Education Data
            $table->string('asal_sekolah')->nullable();
            $table->integer('tahun_lulus')->nullable();
            
            // Registration Info
            $table->string('code')->unique();
            $table->string('numb_reg')->unique();
            $table->date('register_date');
            $table->enum('status', ['Pending', 'Lulus', 'Gagal', 'Batal'])->default('Pending');
            $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->default('pending');
            
            // Admin Management
            $table->text('catatan_admin')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->string('nim_diberikan')->nullable();
            $table->string('photo')->nullable();
            
            // Audit Fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('periode_id')->references('id')->on('periode_pendaftarans')->onDelete('set null');
            $table->foreign('jalur_id')->references('id')->on('jalur_pendaftarans')->onDelete('set null');
            $table->foreign('prodi_1')->references('id')->on('program_studis')->onDelete('cascade');
            $table->foreign('prodi_2')->references('id')->on('program_studis')->onDelete('set null');
            $table->foreign('prodi_3')->references('id')->on('program_studis')->onDelete('set null');
            $table->foreign('prodi_id')->references('id')->on('program_studis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftars');
    }
};
