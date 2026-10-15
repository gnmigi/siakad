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
            $table->datetime('graduated_at')->nullable()->after('terminated_by');
            $table->text('graduation_note')->nullable()->after('graduated_at');
            $table->unsignedBigInteger('graduated_by')->nullable()->after('graduation_note');
            
            // Add foreign key constraint for graduated_by
            $table->foreign('graduated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['graduated_by']);
            $table->dropColumn(['graduated_at', 'graduation_note', 'graduated_by']);
        });
    }
};
