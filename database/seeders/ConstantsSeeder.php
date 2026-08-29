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
        DB::table('category_types')->insert([
            ['value' => 'expense', 'label' => 'Expense'],
            ['value' => 'income', 'label' => 'Income'],
        ]);

        DB::table('account_types')->insert([
            ['value' => 'bank', 'label' => 'Bank'],
            ['value' => 'e_wallet', 'label' => 'E-Wallet'],
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'investment', 'label' => 'Investment'],
        ]);

        DB::table('currencies')->insert([
            ['value' => 'IDR', 'label' => 'IDR'],
            ['value' => 'USD', 'label' => 'USD'],
        ]);

        DB::table('transaction_types')->insert([
            ['value' => 'expense', 'label' => 'Expense'],
            ['value' => 'income', 'label' => 'Income'],
            ['value' => 'transfer', 'label' => 'Transfer'],
        ]);
    }
}
