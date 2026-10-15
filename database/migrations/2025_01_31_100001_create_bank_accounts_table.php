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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                     // KODE REKENING
            $table->string('bank_name');                          // NAMA BANK
            $table->string('bank_code');                          // KODE BANK
            $table->string('account_number');                     // NOMOR REKENING
            $table->string('account_name');                       // NAMA PEMILIK REKENING
            $table->string('branch')->nullable();                 // CABANG BANK
            $table->text('description')->nullable();              // DESKRIPSI
            $table->boolean('is_active')->default(true);          // STATUS AKTIF
            $table->boolean('is_primary')->default(false);        // REKENING UTAMA
            $table->string('logo')->nullable();                   // LOGO BANK
            $table->json('config')->nullable();                   // KONFIGURASI KHUSUS
            
            // UNTUK INTEGRASI PAYMENT GATEWAY
            $table->string('gateway_account_id')->nullable();     // ID AKUN DI GATEWAY
            $table->string('virtual_account_prefix')->nullable(); // PREFIX VA
            
            // AUDIT TRACKING
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
