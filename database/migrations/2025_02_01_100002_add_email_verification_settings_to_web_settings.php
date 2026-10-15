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
            $table->boolean('email_verification_enabled')->default(false)->after('school_phone');
            $table->integer('verification_code_length')->default(6)->after('email_verification_enabled');
            $table->integer('verification_code_expiry_minutes')->default(10)->after('verification_code_length');
            $table->integer('max_verification_attempts')->default(3)->after('verification_code_expiry_minutes');
            $table->boolean('require_email_verification_pmb')->default(true)->after('max_verification_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'email_verification_enabled',
                'verification_code_length',
                'verification_code_expiry_minutes',
                'max_verification_attempts',
                'require_email_verification_pmb'
            ]);
        });
    }
};
