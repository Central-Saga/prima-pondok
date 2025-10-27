<?php

namespace Database\Seeders;

use App\Models\Galeri;
use App\Support\ImageUploader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        if (Galeri::count() > 0) {
            return;
        }

        // Curated Unsplash photos (hotel/room/interior vibes)
        $ids = [
            '1505691938895-1758d7feb511',
            '1501117716987-c8e3f3f1a3fa',
            '1505691723518-36a5ac3b2c47',
            '1519710164239-da123dc03ef4',
            '1493809842364-78817add7ffb',
            '1496417263034-38ec4f0b665a',
            '1501045661006-fcebe0257c3f',
            '1479839672679-a46483c0e7c8',
        ];

        foreach ($ids as $i => $id) {
            $url = "https://images.unsplash.com/photo-$id?auto=format&fit=crop&w=1920&q=80";

            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unsplash_' . Str::random(10) . '.jpg';
            try {
                Http::timeout(30)
                    ->withHeaders(['Accept' => 'image/*'])
                    ->sink($tmp)
                    ->get($url);

                if (!file_exists($tmp) || filesize($tmp) <= 0) {
                    continue; // skip if failed
                }

                $uploaded = new UploadedFile($tmp, basename($tmp), null, null, true);
                // Compress and store under 2MB, max 1920px
                $storedPath = ImageUploader::storeCompressed($uploaded, 'galeri', 2048, 1920, 1920);

                Galeri::create([
                    'title' => 'Galeri ' . ($i + 1),
                    'path' => $storedPath,
                    'status' => 'active',
                    'urutan' => $i + 1,
                ]);
            } catch (\Throwable $e) {
                // ignore and continue to next image
            } finally {
                if (is_file($tmp)) @unlink($tmp);
            }
        }
    }
}
