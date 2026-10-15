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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();         // KODE TRANSAKSI UNIK
            $table->string('invoice_number')->unique();           // NOMOR INVOICE
            
            // RELASI
            $table->unsignedBigInteger('tagihan_id');             // ID TAGIHAN
            $table->unsignedBigInteger('mahasiswa_id');           // ID MAHASISWA
            $table->unsignedBigInteger('payment_method_id');      // ID METODE PEMBAYARAN
            $table->unsignedBigInteger('bank_account_id')->nullable(); // ID REKENING BANK
            
            // INFORMASI PEMBAYARAN
            $table->bigInteger('amount');                         // JUMLAH TAGIHAN
            $table->bigInteger('fee_amount')->default(0);         // BIAYA ADMIN
            $table->bigInteger('total_amount');                   // TOTAL YANG HARUS DIBAYAR
            $table->bigInteger('paid_amount')->default(0);        // JUMLAH YANG SUDAH DIBAYAR
            
            // STATUS DAN WAKTU
            $table->enum('status', [
                'pending',           // MENUNGGU PEMBAYARAN
                'processing',        // SEDANG DIPROSES
                'paid',             // SUDAH DIBAYAR
                'verified',         // SUDAH DIVERIFIKASI
                'failed',           // GAGAL
                'cancelled',        // DIBATALKAN
                'expired'           // KADALUARSA
            ])->default('pending');
            
            $table->timestamp('due_date')->nullable();            // BATAS WAKTU PEMBAYARAN
            $table->timestamp('paid_at')->nullable();             // WAKTU PEMBAYARAN
            $table->timestamp('verified_at')->nullable();         // WAKTU VERIFIKASI
            $table->timestamp('expired_at')->nullable();          // WAKTU KADALUARSA
            
            // INFORMASI PAYMENT GATEWAY
            $table->string('gateway_transaction_id')->nullable(); // ID TRANSAKSI DI GATEWAY
            $table->string('gateway_reference')->nullable();      // REFERENSI GATEWAY
            $table->string('virtual_account')->nullable();        // NOMOR VIRTUAL ACCOUNT
            $table->json('gateway_response')->nullable();         // RESPONSE DARI GATEWAY
            
            // INFORMASI TAMBAHAN
            $table->text('notes')->nullable();                    // CATATAN
            $table->string('payment_proof')->nullable();          // BUKTI PEMBAYARAN (FILE)
            $table->json('metadata')->nullable();                 // DATA TAMBAHAN
            
            // AUDIT TRACKING
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable(); // YANG MEMVERIFIKASI
            
            // INDEXES
            $table->index(['status', 'created_at']);
            $table->index(['mahasiswa_id', 'status']);
            $table->index(['tagihan_id']);
            $table->index(['gateway_transaction_id']);
            
            // FOREIGN KEYS
            $table->foreign('tagihan_id')->references('id')->on('tagihan_kuliahs');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
