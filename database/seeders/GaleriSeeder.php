<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Seeder;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        if (Galeri::count() > 0) {
            return;
        }

        $urls = [
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1501117716987-c8e3f3f1a3fa?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1505691723518-36a5ac3b2c47?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?q=80&w=1200&auto=format&fit=crop',
        ];

        foreach ($urls as $i => $url) {
            Galeri::create([
                'title' => 'Galeri '.($i+1),
                'path' => $url,
                'status' => 'active',
                'urutan' => $i+1,
            ]);
        }
    }
}

