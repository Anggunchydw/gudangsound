<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use Illuminate\Database\Seeder;

class CashAccountSeeder extends Seeder
{
    public function run(): void
    {
        CashAccount::updateOrCreate(
            ['id' => 1],
            [
                'name'    => 'Kas Utama',
                'balance' => 0,
            ]
        );
    }
}
