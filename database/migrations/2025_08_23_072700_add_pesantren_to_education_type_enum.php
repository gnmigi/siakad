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
        // Update mahasiswas table
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");

        // Update dosens table
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");

        // Update users table
        DB::statement("ALTER TABLE users MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE users MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Pesantren', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert mahasiswas table
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE mahasiswas MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");

        // Revert dosens table
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE dosens MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");

        // Revert users table
        DB::statement("ALTER TABLE users MODIFY COLUMN edu1_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') DEFAULT 'SMA/SMK'");
        DB::statement("ALTER TABLE users MODIFY COLUMN edu2_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN edu3_type ENUM('SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor') NULL");
    }
};
