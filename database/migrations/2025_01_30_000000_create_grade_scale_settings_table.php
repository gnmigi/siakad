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
        Schema::create('grade_scale_settings', function (Blueprint $table) {
            $table->id();
            $table->string('grade_letter', 5); // A, A-, B+, B, B-, C+, C, C-, D+, D, E
            $table->decimal('min_score', 5, 2); // Minimum score for this grade
            $table->decimal('max_score', 5, 2); // Maximum score for this grade
            $table->decimal('grade_point', 3, 2); // GPA value (0.00 - 4.00)
            $table->string('color_class', 50)->default('text-secondary'); // CSS class for display
            $table->integer('order')->default(1); // Display order
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['is_active', 'order']);
            $table->index(['min_score', 'max_score']);
            $table->unique(['grade_letter', 'is_active']); // Prevent duplicate active grades
        });

        // Insert default grade scale values
        DB::table('grade_scale_settings')->insert([
            [
                'grade_letter' => 'A',
                'min_score' => 85.00,
                'max_score' => 100.00,
                'grade_point' => 4.00,
                'color_class' => 'text-success',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'A-',
                'min_score' => 80.00,
                'max_score' => 84.99,
                'grade_point' => 3.70,
                'color_class' => 'text-success',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'B+',
                'min_score' => 75.00,
                'max_score' => 79.99,
                'grade_point' => 3.30,
                'color_class' => 'text-info',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'B',
                'min_score' => 70.00,
                'max_score' => 74.99,
                'grade_point' => 3.00,
                'color_class' => 'text-info',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'B-',
                'min_score' => 65.00,
                'max_score' => 69.99,
                'grade_point' => 2.70,
                'color_class' => 'text-primary',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'C+',
                'min_score' => 60.00,
                'max_score' => 64.99,
                'grade_point' => 2.30,
                'color_class' => 'text-warning',
                'order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'C',
                'min_score' => 55.00,
                'max_score' => 59.99,
                'grade_point' => 2.00,
                'color_class' => 'text-warning',
                'order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'C-',
                'min_score' => 50.00,
                'max_score' => 54.99,
                'grade_point' => 1.70,
                'color_class' => 'text-orange',
                'order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'D+',
                'min_score' => 45.00,
                'max_score' => 49.99,
                'grade_point' => 1.30,
                'color_class' => 'text-danger',
                'order' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'D',
                'min_score' => 40.00,
                'max_score' => 44.99,
                'grade_point' => 1.00,
                'color_class' => 'text-danger',
                'order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_letter' => 'E',
                'min_score' => 0.00,
                'max_score' => 39.99,
                'grade_point' => 0.00,
                'color_class' => 'text-dark',
                'order' => 11,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scale_settings');
    }
};
