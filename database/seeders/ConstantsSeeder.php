<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category_types')->insertOrIgnore([
            ['value' => 'expense', 'label' => 'Expense'],
            ['value' => 'income', 'label' => 'Income'],
        ]);

        DB::table('account_types')->insertOrIgnore([
            ['value' => 'bank', 'label' => 'Bank'],
            ['value' => 'e_wallet', 'label' => 'E-Wallet'],
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'investment', 'label' => 'Investment'],
            ['value' => 'debt', 'label' => 'Debt (Hutang)'],
            ['value' => 'receivable', 'label' => 'Receivable (Piutang)'],
        ]);

        DB::table('currencies')->insertOrIgnore([
            ['value' => 'IDR', 'label' => 'IDR'],
            ['value' => 'USD', 'label' => 'USD'],
        ]);

        DB::table('transaction_types')->insertOrIgnore([
            ['value' => 'expense', 'label' => 'Expense'],
            ['value' => 'income', 'label' => 'Income'],
            ['value' => 'transfer', 'label' => 'Transfer'],
        ]);
    }
}
