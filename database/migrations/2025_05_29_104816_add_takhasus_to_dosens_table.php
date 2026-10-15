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
        Schema::table('dosens', function (Blueprint $table) {
            // Add Takhasus field only if it doesn't exist
            if (!Schema::hasColumn('dosens', 'takhasus')) {
                $table->string('takhasus')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            // Remove Takhasus field
            $table->dropColumn('takhasus');
        });
    }
};
