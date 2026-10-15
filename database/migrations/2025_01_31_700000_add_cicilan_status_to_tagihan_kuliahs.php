<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'Cicilan' status to the ENUM
        DB::statement("ALTER TABLE tagihan_kuliahs MODIFY COLUMN status ENUM('Pending', 'Sukses', 'Gagal', 'Cicilan') NOT NULL DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Cicilan' status from the ENUM
        DB::statement("ALTER TABLE tagihan_kuliahs MODIFY COLUMN status ENUM('Pending', 'Sukses', 'Gagal') NOT NULL DEFAULT 'Pending'");
    }
};
