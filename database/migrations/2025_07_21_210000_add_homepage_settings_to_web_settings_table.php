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
        Schema::table('web_settings', function (Blueprint $table) {
            // Hero Section Settings
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_background_image')->nullable();
            
            // Statistics Settings
            $table->string('stat_programs_count')->default('50+');
            $table->string('stat_programs_label')->default('Program Studi');
            $table->string('stat_students_count')->default('15K+');
            $table->string('stat_students_label')->default('Mahasantri Aktif');
            $table->string('stat_lecturers_count')->default('500+');
            $table->string('stat_lecturers_label')->default('Dosen Berkualitas');
            $table->string('stat_partners_count')->default('100+');
            $table->string('stat_partners_label')->default('Mitra Industri');
            
            // Academic Calendar Settings
            $table->json('academic_calendar')->nullable();
            
            // Features Section Settings
            $table->json('features_section')->nullable();
            
            // Programs Section Settings
            $table->json('programs_section')->nullable();
            
            // News Section Settings
            $table->string('news_main_title')->nullable();
            $table->text('news_main_content')->nullable();
            $table->string('news_main_image')->nullable();
            $table->string('news_main_category')->default('Event');
            $table->date('news_main_date')->nullable();
            
            // Gallery Section Settings
            $table->json('gallery_section')->nullable();
            
            // FAQ Section Settings
            $table->json('faq_section')->nullable();
            
            // CTA Section Settings
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->string('cta_button_text')->default('Daftar Sekarang');
            $table->string('cta_button_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_subtitle', 
                'hero_background_image',
                'stat_programs_count',
                'stat_programs_label',
                'stat_students_count',
                'stat_students_label',
                'stat_lecturers_count',
                'stat_lecturers_label',
                'stat_partners_count',
                'stat_partners_label',
                'academic_calendar',
                'features_section',
                'programs_section',
                'news_main_title',
                'news_main_content',
                'news_main_image',
                'news_main_category',
                'news_main_date',
                'gallery_section',
                'faq_section',
                'cta_title',
                'cta_subtitle',
                'cta_button_text',
                'cta_button_link'
            ]);
        });
    }
};
