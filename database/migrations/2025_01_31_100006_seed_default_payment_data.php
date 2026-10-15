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
        // SEED DEFAULT PAYMENT METHODS
        $paymentMethods = [
            [
                'code' => 'BANK_TRANSFER',
                'name' => 'Transfer Bank',
                'type' => 'bank_transfer',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui transfer bank',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 0.00,
                'fee_fixed' => 4000,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'VIRTUAL_ACCOUNT',
                'name' => 'Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui Virtual Account',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 0.00,
                'fee_fixed' => 4000,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'GOPAY',
                'name' => 'GoPay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui GoPay',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.00,
                'fee_fixed' => 0,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'OVO',
                'name' => 'OVO',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui OVO',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.00,
                'fee_fixed' => 0,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DANA',
                'name' => 'DANA',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui DANA',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.00,
                'fee_fixed' => 0,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'SHOPEEPAY',
                'name' => 'ShopeePay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui ShopeePay',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.00,
                'fee_fixed' => 0,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'CREDIT_CARD',
                'name' => 'Kartu Kredit',
                'type' => 'credit_card',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui Kartu Kredit',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.90,
                'fee_fixed' => 2000,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'CASH',
                'name' => 'Tunai',
                'type' => 'cash',
                'provider' => 'manual',
                'description' => 'Pembayaran tunai di kampus',
                'is_active' => true,
                'auto_verify' => false,
                'fee_percentage' => 0.00,
                'fee_fixed' => 0,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('payment_methods')->insert($paymentMethods);

        // SEED DEFAULT BANK ACCOUNTS
        $bankAccounts = [
            [
                'code' => 'BCA_MAIN',
                'bank_name' => 'Bank Central Asia',
                'bank_code' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Cabang Utama',
                'description' => 'Rekening utama untuk pembayaran akademik',
                'is_active' => true,
                'is_primary' => true,
                'virtual_account_prefix' => '70012',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'MANDIRI_MAIN',
                'bank_name' => 'Bank Mandiri',
                'bank_code' => 'MANDIRI',
                'account_number' => '1370012345678',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Cabang Utama',
                'description' => 'Rekening alternatif untuk pembayaran akademik',
                'is_active' => true,
                'is_primary' => false,
                'virtual_account_prefix' => '70013',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'BNI_MAIN',
                'bank_name' => 'Bank Negara Indonesia',
                'bank_code' => 'BNI',
                'account_number' => '0123456789',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Cabang Utama',
                'description' => 'Rekening alternatif untuk pembayaran akademik',
                'is_active' => true,
                'is_primary' => false,
                'virtual_account_prefix' => '70014',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('bank_accounts')->insert($bankAccounts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_methods')->truncate();
        DB::table('bank_accounts')->truncate();
    }
};
