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
        // Create krs_mahasiswa table
        Schema::create('krs_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->onDelete('cascade');
            $table->integer('semester');
            $table->date('tanggal_pengisian');
            $table->enum('status', ['draft', 'submitted', 'disetujui', 'ditolak'])->default('draft');
            $table->integer('total_sks')->default(0);
            $table->decimal('ip_semester', 3, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['mahasiswa_id', 'semester']);
            $table->index(['tahun_akademik_id']);
        });

        // Create krs_detail_mahasiswa table
        Schema::create('krs_detail_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_mahasiswa_id')->constrained('krs_mahasiswa')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('rencana_studi_id')->nullable()->constrained('rencana_studis')->onDelete('set null');
            $table->integer('sks');
            $table->string('nilai_huruf', 2)->nullable();
            $table->decimal('nilai_angka', 4, 2)->nullable();
            $table->enum('status', ['aktif', 'batal', 'selesai'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['krs_mahasiswa_id']);
            $table->index(['mata_kuliah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_detail_mahasiswa');
        Schema::dropIfExists('krs_mahasiswa');
    }
};
