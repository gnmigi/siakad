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
            $table->boolean('is_terminated')->default(false)->after('updated_by')->comment('Status terminal mahasiswa');
            $table->text('termination_reason')->nullable()->after('is_terminated')->comment('Alasan terminal');
            $table->timestamp('terminated_at')->nullable()->after('termination_reason')->comment('Tanggal terminal ditetapkan');
            $table->unsignedBigInteger('terminated_by')->nullable()->after('terminated_at')->comment('User yang menetapkan terminal');
            
            // Add foreign key constraint for terminated_by
            $table->foreign('terminated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['terminated_by']);
            $table->dropColumn(['is_terminated', 'termination_reason', 'terminated_at', 'terminated_by']);
        });
    }
};
