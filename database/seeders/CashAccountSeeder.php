<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use Illuminate\Database\Seeder;

class CashAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CashAccount::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Kas Utama',
                'balance' => 0,
            ]
        );
    }
}
