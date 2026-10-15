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
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            // Enhanced Due Date Management Fields
            $table->integer('grace_period_days')->default(7)->after('due_date')
                ->comment('Masa tenggang pembayaran dalam hari (tanpa denda)');
            
            $table->timestamp('reminder_sent_at')->nullable()->after('grace_period_days')
                ->comment('Waktu terakhir reminder dikirim');
            
            $table->timestamp('last_reminder_sent')->nullable()->after('reminder_sent_at')
                ->comment('Waktu reminder terakhir dikirim');
            
            $table->enum('overdue_status', ['on_time', 'grace_period', 'overdue'])
                ->default('on_time')->after('last_reminder_sent')
                ->comment('Status keterlambatan pembayaran');
            
            $table->enum('payment_priority', ['normal', 'urgent', 'critical'])
                ->default('normal')->after('overdue_status')
                ->comment('Prioritas pembayaran berdasarkan status');
            
            $table->boolean('academic_hold_applied')->default(false)->after('payment_priority')
                ->comment('Apakah academic hold sudah diterapkan');
            
            $table->text('notes')->nullable()->after('academic_hold_applied')
                ->comment('Catatan tambahan untuk tagihan');
            
            // Reminder tracking
            $table->json('reminder_history')->nullable()->after('notes')
                ->comment('History pengiriman reminder');
            
            // Payment plan support
            $table->boolean('installment_allowed')->default(false)->after('reminder_history')
                ->comment('Apakah tagihan ini bisa dicicil');
            
            $table->integer('installment_count')->nullable()->after('installment_allowed')
                ->comment('Jumlah cicilan jika diizinkan');
            
            // Enhanced tracking
            $table->timestamp('first_reminder_sent')->nullable()->after('installment_count')
                ->comment('Waktu reminder pertama dikirim');
            
            $table->integer('reminder_count')->default(0)->after('first_reminder_sent')
                ->comment('Jumlah reminder yang sudah dikirim');
            
            // Academic integration
            $table->timestamp('academic_hold_date')->nullable()->after('reminder_count')
                ->comment('Tanggal academic hold diterapkan');
            
            $table->string('academic_hold_reason')->nullable()->after('academic_hold_date')
                ->comment('Alasan academic hold');
            
            // Add indexes for better performance
            $table->index(['overdue_status', 'payment_priority'], 'idx_overdue_priority');
            $table->index(['due_date', 'overdue_status'], 'idx_due_overdue');
            $table->index(['academic_hold_applied'], 'idx_academic_hold');
            $table->index(['reminder_sent_at'], 'idx_reminder_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_overdue_priority');
            $table->dropIndex('idx_due_overdue');
            $table->dropIndex('idx_academic_hold');
            $table->dropIndex('idx_reminder_sent');
            
            // Drop columns
            $table->dropColumn([
                'grace_period_days',
                'reminder_sent_at',
                'last_reminder_sent',
                'overdue_status',
                'payment_priority',
                'academic_hold_applied',
                'notes',
                'reminder_history',
                'installment_allowed',
                'installment_count',
                'first_reminder_sent',
                'reminder_count',
                'academic_hold_date',
                'academic_hold_reason'
            ]);
        });
    }
};
