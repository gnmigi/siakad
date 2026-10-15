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
            $table->boolean('wa_admin_payment_notification_enabled')->default(true);
            $table->boolean('wa_admin_krs_notification_enabled')->default(false);
            $table->string('wa_admin_numbers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'wa_admin_payment_notification_enabled',
                'wa_admin_krs_notification_enabled',
                'wa_admin_numbers'
            ]);
        });
    }
};
