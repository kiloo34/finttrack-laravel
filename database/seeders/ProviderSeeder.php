<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $providers = [
            // Banks
            ['name' => 'BCA', 'type' => 'bank'],
            ['name' => 'Mandiri', 'type' => 'bank'],
            ['name' => 'BRI', 'type' => 'bank'],
            ['name' => 'BNI', 'type' => 'bank'],
            ['name' => 'BSI (Bank Syariah Indonesia)', 'type' => 'bank'],
            ['name' => 'CIMB Niaga', 'type' => 'bank'],
            ['name' => 'Permata Bank', 'type' => 'bank'],
            ['name' => 'Danamon', 'type' => 'bank'],
            ['name' => 'Panin Bank', 'type' => 'bank'],
            ['name' => 'Bank Mega', 'type' => 'bank'],
            ['name' => 'BTN', 'type' => 'bank'],

            // Digital Banks
            ['name' => 'Bank Jago', 'type' => 'bank'],
            ['name' => 'Jenius (BTPN)', 'type' => 'bank'],
            ['name' => 'SeaBank', 'type' => 'bank'],
            ['name' => 'Blu by BCA', 'type' => 'bank'],
            ['name' => 'Allo Bank', 'type' => 'bank'],
            ['name' => 'Bank Neo Commerce', 'type' => 'bank'],

            // E-Wallets
            ['name' => 'GoPay', 'type' => 'ewallet'],
            ['name' => 'OVO', 'type' => 'ewallet'],
            ['name' => 'DANA', 'type' => 'ewallet'],
            ['name' => 'ShopeePay', 'type' => 'ewallet'],
            ['name' => 'LinkAja', 'type' => 'ewallet'],
            ['name' => 'AstraPay', 'type' => 'ewallet'],

            // Investment / Others
            ['name' => 'Bibit', 'type' => 'investment'],
            ['name' => 'Bareksa', 'type' => 'investment'],
            ['name' => 'Ajaib', 'type' => 'investment'],
            ['name' => 'Pluang', 'type' => 'investment'],

            // Cash
            ['name' => 'Tunai / Cash', 'type' => 'cash'],
        ];

        $data = array_map(function ($item) use ($now) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            return $item;
        }, $providers);

        DB::table('providers')->insertOrIgnore($data);
    }
}
