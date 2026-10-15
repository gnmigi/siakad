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
        Schema::create('institution_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_type')->unique(); // 'profile', 'vision_mission', 'structure', 'facilities'
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->json('additional_data')->nullable(); // For storing extra data like vision, mission separately
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_pages');
    }
};
