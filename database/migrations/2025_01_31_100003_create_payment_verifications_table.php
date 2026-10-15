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
        Schema::create('payment_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code')->unique();        // KODE VERIFIKASI
            
            // RELASI
            $table->unsignedBigInteger('payment_transaction_id'); // ID TRANSAKSI PEMBAYARAN
            $table->unsignedBigInteger('verified_by')->nullable(); // ID USER YANG MEMVERIFIKASI
            
            // INFORMASI VERIFIKASI
            $table->enum('verification_type', [
                'automatic',        // VERIFIKASI OTOMATIS
                'manual',          // VERIFIKASI MANUAL
                'gateway_callback', // CALLBACK DARI GATEWAY
                'bank_statement'   // DARI MUTASI REKENING
            ]);
            
            $table->enum('status', [
                'pending',         // MENUNGGU VERIFIKASI
                'approved',        // DISETUJUI
                'rejected',        // DITOLAK
                'need_review'      // PERLU REVIEW MANUAL
            ])->default('pending');
            
            // DATA VERIFIKASI
            $table->bigInteger('verified_amount')->nullable();     // JUMLAH YANG DIVERIFIKASI
            $table->timestamp('payment_date')->nullable();         // TANGGAL PEMBAYARAN SEBENARNYA
            $table->string('bank_reference')->nullable();          // REFERENSI BANK
            $table->string('sender_name')->nullable();             // NAMA PENGIRIM
            $table->string('sender_account')->nullable();          // REKENING PENGIRIM
            
            // BUKTI DAN CATATAN
            $table->json('verification_data')->nullable();         // DATA VERIFIKASI (JSON)
            $table->text('notes')->nullable();                     // CATATAN VERIFIKASI
            $table->text('rejection_reason')->nullable();          // ALASAN PENOLAKAN
            $table->string('proof_file')->nullable();              // FILE BUKTI
            
            // WAKTU
            $table->timestamp('verified_at')->nullable();          // WAKTU VERIFIKASI
            $table->timestamp('auto_verified_at')->nullable();     // WAKTU VERIFIKASI OTOMATIS
            
            // AUDIT TRACKING
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // INDEXES
            $table->index(['status', 'created_at']);
            $table->index(['payment_transaction_id']);
            $table->index(['verification_type', 'status']);
            
            // FOREIGN KEYS
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
            $table->foreign('verified_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_verifications');
    }
};
