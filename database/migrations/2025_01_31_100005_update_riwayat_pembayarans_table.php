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
        Schema::table('riwayat_pembayarans', function (Blueprint $table) {
            // TAMBAH KOLOM YANG DIPERLUKAN
            $table->string('code')->unique()->after('id');                    // KODE RIWAYAT
            $table->unsignedBigInteger('mahasiswa_id')->after('code');        // ID MAHASISWA
            $table->unsignedBigInteger('tagihan_id')->after('mahasiswa_id');  // ID TAGIHAN
            $table->unsignedBigInteger('payment_transaction_id')->nullable()->after('tagihan_id'); // ID TRANSAKSI PEMBAYARAN
            
            // INFORMASI PEMBAYARAN
            $table->bigInteger('amount')->after('payment_transaction_id');    // JUMLAH PEMBAYARAN
            $table->string('payment_method')->after('amount');                // METODE PEMBAYARAN
            $table->enum('status', ['pending', 'paid', 'verified', 'failed', 'cancelled'])->default('pending')->after('payment_method');
            
            // INFORMASI TAMBAHAN
            $table->text('notes')->nullable()->after('status');               // CATATAN
            $table->string('reference_number')->nullable()->after('notes');   // NOMOR REFERENSI
            $table->timestamp('paid_at')->nullable()->after('reference_number'); // WAKTU PEMBAYARAN
            $table->timestamp('verified_at')->nullable()->after('paid_at');   // WAKTU VERIFIKASI
            
            // AUDIT TRACKING
            $table->softDeletes()->after('updated_at');
            $table->unsignedBigInteger('created_by')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->unsignedBigInteger('verified_by')->nullable()->after('deleted_by');
            
            // INDEXES
            $table->index(['mahasiswa_id', 'status']);
            $table->index(['tagihan_id']);
            $table->index(['status', 'created_at']);
            
            // FOREIGN KEYS
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas');
            $table->foreign('tagihan_id')->references('id')->on('tagihan_kuliahs');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pembayarans', function (Blueprint $table) {
            // DROP FOREIGN KEYS
            $table->dropForeign(['mahasiswa_id']);
            $table->dropForeign(['tagihan_id']);
            $table->dropForeign(['payment_transaction_id']);
            
            // DROP INDEXES
            $table->dropIndex(['mahasiswa_id', 'status']);
            $table->dropIndex(['tagihan_id']);
            $table->dropIndex(['status', 'created_at']);
            
            // DROP COLUMNS
            $table->dropColumn([
                'code', 'mahasiswa_id', 'tagihan_id', 'payment_transaction_id',
                'amount', 'payment_method', 'status', 'notes', 'reference_number',
                'paid_at', 'verified_at', 'deleted_at', 'created_by', 'updated_by',
                'deleted_by', 'verified_by'
            ]);
        });
    }
};
