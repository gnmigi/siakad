<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('pertemuan_kuliahs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
        $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
        $table->integer('pertemuan_ke'); // Menyimpan angka 1 sampai 14
        $table->date('tanggal');
        $table->text('materi')->nullable(); // Topik yang dibahas hari itu
        $table->enum('status', ['Selesai', 'Belum Mulai'])->default('Selesai');
        $table->timestamps();
    });
}
};
