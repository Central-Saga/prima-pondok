<?php

namespace Database\Seeders;

use App\Models\Kamar;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        if (Kamar::count() > 0) {
            return;
        }

        $data = [
            ['nama_kamar' => 'Kamar 101', 'tipe_kamar' => 'Standard', 'harga' => 300000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 102', 'tipe_kamar' => 'Standard', 'harga' => 300000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 201', 'tipe_kamar' => 'Deluxe', 'harga' => 450000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 202', 'tipe_kamar' => 'Deluxe', 'harga' => 450000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 301', 'tipe_kamar' => 'Family', 'harga' => 600000, 'status' => 'available'],
        ];

        foreach ($data as $row) {
            Kamar::create($row);
        }
    }
}

