<?php

namespace Database\Seeders;

use App\Models\Kamar;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        // Seed 4 Single Room and 1 Romantic Room (idempotent by nama_kamar)
        $rooms = [];
        for ($i = 1; $i <= 4; $i++) {
            $rooms[] = [
                'nama_kamar' => "Single Room $i",
                'tipe_kamar' => 'Single Room',
                'status' => 'available',
            ];
        }
        $rooms[] = [
            'nama_kamar' => 'Romantic Room 1',
            'tipe_kamar' => 'Romantic Room',
            'status' => 'available',
        ];

        foreach ($rooms as $row) {
            Kamar::firstOrCreate(
                ['nama_kamar' => $row['nama_kamar']],
                $row
            );
        }
    }
}
