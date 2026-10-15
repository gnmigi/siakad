<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('absensi_mahasiswas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pertemuan_id')->constrained('pertemuan_kuliahs')->onDelete('cascade');
        $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
        $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
        $table->timestamps();
    });
}
};
