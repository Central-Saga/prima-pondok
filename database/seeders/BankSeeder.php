<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Bank::updateOrCreate(
            ['no_transfer' => '0401898661'],
            [
                'nama_bank' => 'BCA -  I MADE SUDIARTA',
                'status' => 'active',
            ]
        );
    }
}

