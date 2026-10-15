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
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Ubah prodi_id menjadi nullable untuk calon mahasiswa
            $table->integer('prodi_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Kembalikan ke kondisi semula
            $table->integer('prodi_id')->default(0)->change();
            $table->string('numb_nim')->nullable()->unique()->change();
        });
    }
};
