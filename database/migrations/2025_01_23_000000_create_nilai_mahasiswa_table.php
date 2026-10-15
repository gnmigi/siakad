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
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->decimal('tugas', 5, 2)->nullable()->default(0);
            $table->decimal('quiz', 5, 2)->nullable()->default(0);
            $table->decimal('uts', 5, 2)->nullable()->default(0);
            $table->decimal('uas', 5, 2)->nullable()->default(0);
            $table->decimal('nilai_akhir', 5, 2)->nullable()->default(0);
            $table->string('huruf_nilai', 2)->nullable();
            $table->enum('status', ['Draft', 'Final'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint to prevent duplicate grades for same student-course combination
            $table->unique(['mata_kuliah_id', 'mahasiswa_id'], 'unique_mahasiswa_matkul');
            
            // Indexes for better performance
            $table->index(['dosen_id', 'mata_kuliah_id']);
            $table->index(['mahasiswa_id', 'mata_kuliah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};
