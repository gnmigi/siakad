<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;

class PaymentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Payment Methods
        $paymentMethods = [
            [
                'code' => 'VA',
                'name' => 'Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui Virtual Account',
                'icon' => 'fa-university',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 2500,
                'sort_order' => 1,
            ],
            [
                'code' => 'TRANSFER',
                'name' => 'Transfer Bank',
                'type' => 'bank_transfer',
                'provider' => 'manual',
                'description' => 'Pembayaran melalui transfer bank',
                'icon' => 'fa-money-bill-transfer',
                'is_active' => true,
                'auto_verify' => false,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'sort_order' => 2,
            ],
            [
                'code' => 'GOPAY',
                'name' => 'GoPay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui GoPay',
                'icon' => 'fa-mobile-alt',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.5,
                'fee_fixed' => 0,
                'sort_order' => 3,
            ],
            [
                'code' => 'OVO',
                'name' => 'OVO',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui OVO',
                'icon' => 'fa-mobile-alt',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.5,
                'fee_fixed' => 0,
                'sort_order' => 4,
            ],
            [
                'code' => 'DANA',
                'name' => 'DANA',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui DANA',
                'icon' => 'fa-mobile-alt',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.5,
                'fee_fixed' => 0,
                'sort_order' => 5,
            ],
            [
                'code' => 'SHOPEEPAY',
                'name' => 'ShopeePay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui ShopeePay',
                'icon' => 'fa-mobile-alt',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.5,
                'fee_fixed' => 0,
                'sort_order' => 6,
            ],
            [
                'code' => 'CREDIT_CARD',
                'name' => 'Kartu Kredit',
                'type' => 'credit_card',
                'provider' => 'midtrans',
                'description' => 'Pembayaran melalui kartu kredit',
                'icon' => 'fa-credit-card',
                'is_active' => true,
                'auto_verify' => true,
                'fee_percentage' => 2.9,
                'fee_fixed' => 0,
                'sort_order' => 7,
            ],
            [
                'code' => 'CASH',
                'name' => 'Tunai',
                'type' => 'cash',
                'provider' => 'manual',
                'description' => 'Pembayaran tunai di kampus',
                'icon' => 'fa-money-bills',
                'is_active' => true,
                'auto_verify' => false,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'sort_order' => 8,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        // Create Bank Accounts
        $bankAccounts = [
            [
                'bank_name' => 'Bank Central Asia',
                'bank_code' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Jakarta Pusat',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'bank_code' => 'MANDIRI',
                'account_number' => '1370012345678',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Jakarta Pusat',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'bank_name' => 'Bank Negara Indonesia',
                'bank_code' => 'BNI',
                'account_number' => '0123456789',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Jakarta Pusat',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'bank_name' => 'Bank Rakyat Indonesia',
                'bank_code' => 'BRI',
                'account_number' => '012301234567890',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Jakarta Pusat',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'bank_name' => 'Bank Permata',
                'bank_code' => 'PERMATA',
                'account_number' => '9876543210',
                'account_name' => 'YAYASAN PENDIDIKAN ISLAM',
                'branch' => 'Jakarta Pusat',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::create($account);
        }
    }
}
