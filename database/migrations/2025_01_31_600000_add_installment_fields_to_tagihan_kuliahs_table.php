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
            // Add fields for installment payments
            $table->decimal('paid_amount', 15, 2)->default(0)->after('amount');
            $table->decimal('remaining_amount', 15, 2)->nullable()->after('paid_amount');
            
            // Add installment tracking
            $table->boolean('is_installment')->default(false)->after('installment_allowed');
            $table->integer('installment_payments_count')->default(0)->after('is_installment');
            $table->json('installment_history')->nullable()->after('installment_payments_count');
            
            // Add index for better performance
            $table->index(['mahasiswa_id', 'status', 'paid_amount']);
            $table->index(['remaining_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->dropIndex(['mahasiswa_id', 'status', 'paid_amount']);
            $table->dropIndex(['remaining_amount']);
            
            $table->dropColumn([
                'paid_amount',
                'remaining_amount', 
                'is_installment',
                'installment_payments_count',
                'installment_history'
            ]);
        });
    }
};
