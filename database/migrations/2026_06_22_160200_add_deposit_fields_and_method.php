<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add deposit_balance and whatsapp_jid to mahasiswas table
        if (Schema::hasTable('mahasiswas')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                if (!Schema::hasColumn('mahasiswas', 'deposit_balance')) {
                    $table->decimal('deposit_balance', 15, 2)->default(0.00)->after('photo');
                }
                if (!Schema::hasColumn('mahasiswas', 'whatsapp_jid')) {
                    $table->string('whatsapp_jid', 100)->nullable()->unique()->after('phone');
                }
            });
        }

        // Add DEPOSIT payment method if it doesn't exist
        $exists = DB::table('payment_methods')->where('code', 'DEPOSIT')->exists();
        if (!$exists) {
            DB::table('payment_methods')->insert([
                'code' => 'DEPOSIT',
                'name' => 'Deposit Saldo',
                'type' => 'deposit',
                'provider' => 'manual',
                'description' => 'Pembayaran menggunakan saldo deposit mahasiswa',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 0.00,
                'fee_fixed' => 0,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mahasiswas')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                if (Schema::hasColumn('mahasiswas', 'deposit_balance')) {
                    $table->dropColumn('deposit_balance');
                }
                if (Schema::hasColumn('mahasiswas', 'whatsapp_jid')) {
                    $table->dropColumn('whatsapp_jid');
                }
            });
        }

        DB::table('payment_methods')->where('code', 'DEPOSIT')->delete();
    }
};
