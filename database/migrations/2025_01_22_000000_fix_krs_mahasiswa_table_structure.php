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
        Schema::table('krs_mahasiswa', function (Blueprint $table) {
            // Add missing fields for rejection functionality
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->timestamp('submitted_at')->nullable()->after('rejection_reason');
        });

        // Update enum values to match controller expectations
        DB::statement("ALTER TABLE krs_mahasiswa MODIFY COLUMN status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft'");
        
        // Update existing data to use new status values
        DB::table('krs_mahasiswa')->where('status', 'disetujui')->update(['status' => 'approved']);
        DB::table('krs_mahasiswa')->where('status', 'ditolak')->update(['status' => 'rejected']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values back to Indonesian
        DB::table('krs_mahasiswa')->where('status', 'approved')->update(['status' => 'disetujui']);
        DB::table('krs_mahasiswa')->where('status', 'rejected')->update(['status' => 'ditolak']);
        
        // Revert enum values
        DB::statement("ALTER TABLE krs_mahasiswa MODIFY COLUMN status ENUM('draft', 'submitted', 'disetujui', 'ditolak') DEFAULT 'draft'");
        
        Schema::table('krs_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['rejected_by', 'rejected_at', 'rejection_reason', 'submitted_at']);
        });
    }
};
