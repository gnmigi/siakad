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
            // Registration Control Settings
            $table->boolean('registration_enabled')->default(false)->after('max_login_attempts')->comment('Enable/disable public registration feature');
            $table->string('registration_title')->nullable()->after('registration_enabled')->default('Daftar Sekarang')->comment('Registration section title');
            $table->text('registration_description')->nullable()->after('registration_title')->comment('Registration section description');
            $table->string('registration_button_text')->nullable()->after('registration_description')->default('Daftar Sekarang')->comment('Registration button text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'registration_enabled',
                'registration_title',
                'registration_description',
                'registration_button_text'
            ]);
        });
    }
};
