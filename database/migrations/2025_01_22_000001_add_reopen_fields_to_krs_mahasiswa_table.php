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
        Schema::table('krs_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('reopened_by')->nullable()->after('rejection_reason');
            $table->timestamp('reopened_at')->nullable()->after('reopened_by');
            
            $table->foreign('reopened_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('krs_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['reopened_by']);
            $table->dropColumn(['reopened_by', 'reopened_at']);
        });
    }
};
