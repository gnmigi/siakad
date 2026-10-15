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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                     // KODE METODE PEMBAYARAN
            $table->string('name');                               // NAMA METODE PEMBAYARAN
            $table->string('type');                               // TYPE: bank_transfer, e_wallet, virtual_account, cash, credit_card
            $table->string('provider')->nullable();               // PROVIDER: midtrans, xendit, manual
            $table->string('icon')->nullable();                   // ICON URL/PATH
            $table->text('description')->nullable();              // DESKRIPSI METODE PEMBAYARAN
            $table->json('config')->nullable();                   // KONFIGURASI KHUSUS (JSON)
            $table->boolean('is_active')->default(true);          // STATUS AKTIF
            $table->boolean('auto_verify')->default(false);       // VERIFIKASI OTOMATIS
            $table->decimal('fee_percentage', 5, 2)->default(0);  // BIAYA PERSENTASE
            $table->bigInteger('fee_fixed')->default(0);          // BIAYA TETAP
            $table->integer('sort_order')->default(0);            // URUTAN TAMPILAN
            
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
        Schema::dropIfExists('payment_methods');
    }
};
