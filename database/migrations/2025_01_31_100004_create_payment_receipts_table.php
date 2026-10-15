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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();           // NOMOR KWITANSI
            $table->string('receipt_code')->unique();             // KODE KWITANSI
            
            // RELASI
            $table->unsignedBigInteger('payment_transaction_id'); // ID TRANSAKSI PEMBAYARAN
            $table->unsignedBigInteger('mahasiswa_id');           // ID MAHASISWA
            $table->unsignedBigInteger('tagihan_id');             // ID TAGIHAN
            
            // INFORMASI KWITANSI
            $table->string('receipt_type')->default('digital');   // TYPE: digital, printed
            $table->bigInteger('amount');                         // JUMLAH PEMBAYARAN
            $table->string('amount_words');                       // TERBILANG
            $table->text('description');                          // DESKRIPSI PEMBAYARAN
            
            // FILE DAN TEMPLATE
            $table->string('pdf_file')->nullable();               // FILE PDF KWITANSI
            $table->string('template_used')->default('default');  // TEMPLATE YANG DIGUNAKAN
            $table->json('receipt_data')->nullable();             // DATA KWITANSI (JSON)
            
            // STATUS
            $table->enum('status', [
                'draft',           // DRAFT
                'generated',       // SUDAH DIBUAT
                'sent',           // SUDAH DIKIRIM
                'downloaded'      // SUDAH DIDOWNLOAD
            ])->default('draft');
            
            // WAKTU
            $table->timestamp('generated_at')->nullable();        // WAKTU DIBUAT
            $table->timestamp('sent_at')->nullable();             // WAKTU DIKIRIM
            $table->timestamp('downloaded_at')->nullable();       // WAKTU DIDOWNLOAD
            
            // INFORMASI TAMBAHAN
            $table->string('sent_to_email')->nullable();          // EMAIL TUJUAN
            $table->string('sent_to_whatsapp')->nullable();       // WHATSAPP TUJUAN
            $table->integer('download_count')->default(0);        // JUMLAH DOWNLOAD
            $table->text('notes')->nullable();                    // CATATAN
            
            // AUDIT TRACKING
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // INDEXES
            $table->index(['payment_transaction_id']);
            $table->index(['mahasiswa_id', 'created_at']);
            $table->index(['status']);
            
            // FOREIGN KEYS
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas');
            $table->foreign('tagihan_id')->references('id')->on('tagihan_kuliahs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
