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
        // Add attachments field to pengumumen table
        Schema::table('pengumumen', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('content');
        });

        // Add attachments field to beritas table
        Schema::table('beritas', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumumen', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
