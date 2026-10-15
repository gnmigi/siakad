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
        Schema::table('program_studis', function (Blueprint $table) {
            // Add Takhasus field only if it doesn't exist
            if (!Schema::hasColumn('program_studis', 'takhasus')) {
                $table->string('takhasus')->nullable()->after('objectives');
            }
            
            // Make fakultas_id nullable (optional)
            $table->integer('fakultas_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            // Remove Takhasus field
            $table->dropColumn('takhasus');
            
            // Make fakultas_id required again
            $table->integer('fakultas_id')->nullable(false)->change();
        });
    }
};
