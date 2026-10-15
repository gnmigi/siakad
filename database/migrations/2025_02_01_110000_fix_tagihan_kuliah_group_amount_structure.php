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
            // Add new fields for amount clarity
            $table->decimal('amount_per_student', 15, 2)->default(0)->after('amount')->comment('Amount per individual student');
            $table->decimal('total_amount', 15, 2)->default(0)->after('amount_per_student')->comment('Total amount for all students in group');
            $table->integer('expected_student_count')->default(0)->after('total_amount')->comment('Expected number of students for this group');
            $table->enum('amount_type', ['per_student', 'total'])->default('per_student')->after('expected_student_count')->comment('How the original amount field should be interpreted');
            
            // Add index for better performance
            $table->index(['status', 'amount_type']);
            $table->index(['template_id', 'taka_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_kuliah_groups', function (Blueprint $table) {
            $table->dropIndex(['status', 'amount_type']);
            $table->dropIndex(['template_id', 'taka_id']);
            $table->dropColumn([
                'amount_per_student',
                'total_amount', 
                'expected_student_count',
                'amount_type'
            ]);
        });
    }
};
