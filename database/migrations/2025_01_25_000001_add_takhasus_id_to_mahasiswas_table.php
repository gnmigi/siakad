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
            $table->unsignedBigInteger('takhasus_id')->nullable()->after('takhasus');
            $table->foreign('takhasus_id')->references('id')->on('takhasus_program_studis')->onDelete('set null');
            $table->index('takhasus_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['takhasus_id']);
            $table->dropIndex(['takhasus_id']);
            $table->dropColumn('takhasus_id');
        });
    }
};
