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
        Schema::table('tagihan_kuliah_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable()->after('id')
                ->comment('Reference to billing template used to generate this group');
            
            // Add foreign key constraint
            $table->foreign('template_id')->references('id')->on('billing_templates')->onDelete('set null');
            
            // Add index for better performance
            $table->index('template_id', 'idx_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_kuliah_groups', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropIndex('idx_template_id');
            $table->dropColumn('template_id');
        });
    }
};
