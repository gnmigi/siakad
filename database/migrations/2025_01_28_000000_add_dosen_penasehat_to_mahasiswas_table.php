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
            $table->unsignedBigInteger('dosen_penasehat_id')->nullable()->after('takhasus_id');
            $table->foreign('dosen_penasehat_id')->references('id')->on('dosens')->onDelete('set null');
            $table->index('dosen_penasehat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function reverse(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['dosen_penasehat_id']);
            $table->dropIndex(['dosen_penasehat_id']);
            $table->dropColumn('dosen_penasehat_id');
        });
    }
};
