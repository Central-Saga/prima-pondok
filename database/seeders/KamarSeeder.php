<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\KamarFoto;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageUploader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kamar' => 'Kamar 101', 'tipe_kamar' => 'Standard', 'harga' => 300000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 102', 'tipe_kamar' => 'Standard', 'harga' => 300000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 201', 'tipe_kamar' => 'Deluxe',  'harga' => 450000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 202', 'tipe_kamar' => 'Deluxe',  'harga' => 450000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 301', 'tipe_kamar' => 'Family',  'harga' => 600000, 'status' => 'available'],
            ['nama_kamar' => 'Kamar 302', 'tipe_kamar' => 'Family',  'harga' => 600000, 'status' => 'available'],
        ];

        // Ensure these 6 rooms exist (idempotent)
        $kamars = [];
        foreach ($data as $row) {
            $kamars[] = Kamar::firstOrCreate(
                ['nama_kamar' => $row['nama_kamar']],
                $row
            );
        }

        // Seed photos for each room from Unsplash, compressed < 2MB
        $ids = [
            '1505691938895-1758d7feb511',
            '1501117716987-c8e3f3f1a3fa',
            '1505691723518-36a5ac3b2c47',
            '1519710164239-da123dc03ef4',
            '1493809842364-78817add7ffb',
            '1496417263034-38ec4f0b665a',
            '1501045661006-fcebe0257c3f',
            '1479839672679-a46483c0e7c8',
            '1524758631624-e2822e304c36',
            '1493809842364-78817add7ffb',
        ];

        $idx = 0;
        foreach ($kamars as $kamar) {
            if ($kamar->nama_kamar === 'Kamar 301') {
                continue; // handle separately below
            }
            if ($kamar->fotos()->count() > 0) {
                continue; // already has images; skip to avoid duplicates
            }
            // assign 3 images per room
            for ($i = 0; $i < 3; $i++) {
                $id = $ids[$idx % count($ids)];
                $idx++;
                $url = "https://images.unsplash.com/photo-$id?auto=format&fit=crop&w=1920&q=80";

                $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unsplash_' . Str::random(10) . '.jpg';
                try {
                    Http::timeout(30)
                        ->withHeaders(['Accept' => 'image/*'])
                        ->sink($tmp)
                        ->get($url);

                    if (!file_exists($tmp) || filesize($tmp) <= 0) {
                        continue;
                    }

                    $uploaded = new UploadedFile($tmp, basename($tmp), null, null, true);
                    $storedPath = ImageUploader::storeCompressed($uploaded, 'kamar', 2048, 1920, 1920);

                    KamarFoto::create([
                        'kamar_id' => $kamar->id,
                        'path' => $storedPath,
                        'urutan' => $i + 1,
                    ]);
                } catch (\Throwable $e) {
                    // skip on failure
                } finally {
                    if (is_file($tmp)) @unlink($tmp);
                }
            }
        }

        // Special handling: replace images for Kamar 301
        $kamar301 = Kamar::where('nama_kamar', 'Kamar 301')->first();
        if ($kamar301) {
            // Remove existing photos and files
            foreach ($kamar301->fotos as $foto) {
                try { Storage::disk('public')->delete($foto->path); } catch (\Throwable $e) {}
                $foto->delete();
            }

            $ids301 = [
                '1503602642458-232111445657', // bedroom
                '1505691723518-36a5ac3b2c47', // interior
                '1524758631624-e2822e304c36', // lamp/bed detail
            ];

            foreach (array_values($ids301) as $i => $id) {
                $url = "https://images.unsplash.com/photo-$id?auto=format&fit=crop&w=1920&q=80";
                $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unsplash_' . Str::random(10) . '.jpg';
                try {
                    Http::timeout(30)
                        ->withHeaders(['Accept' => 'image/*'])
                        ->sink($tmp)
                        ->get($url);

                    if (!file_exists($tmp) || filesize($tmp) <= 0) {
                        continue;
                    }

                    $uploaded = new UploadedFile($tmp, basename($tmp), null, null, true);
                    $storedPath = ImageUploader::storeCompressed($uploaded, 'kamar', 2048, 1920, 1920);
                    KamarFoto::create([
                        'kamar_id' => $kamar301->id,
                        'path' => $storedPath,
                        'urutan' => $i + 1,
                    ]);
                } catch (\Throwable $e) {
                    // skip
                } finally {
                    if (is_file($tmp)) @unlink($tmp);
                }
            }
        }

        // Ensure images for Kamar 302 (add or replace if missing)
        $kamar302 = Kamar::where('nama_kamar', 'Kamar 302')->first();
        if ($kamar302) {
            // Determine if any existing photo files are missing; if so, replace all
            $needsReplace = $kamar302->fotos()->count() === 0;
            if (!$needsReplace) {
                foreach ($kamar302->fotos as $foto) {
                    if (!Storage::disk('public')->exists($foto->path)) {
                        $needsReplace = true; break;
                    }
                }
            }

            if ($needsReplace) {
                foreach ($kamar302->fotos as $foto) {
                    try { Storage::disk('public')->delete($foto->path); } catch (\Throwable $e) {}
                    $foto->delete();
                }

                $ids302 = [
                    '1501045661006-fcebe0257c3f', // chair/room detail
                    '1496417263034-38ec4f0b665a', // modern chair
                    '1479839672679-a46483c0e7c8', // bright room
                ];

                foreach (array_values($ids302) as $i => $id) {
                    $url = "https://images.unsplash.com/photo-$id?auto=format&fit=crop&w=1920&q=80";
                    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unsplash_' . Str::random(10) . '.jpg';
                    try {
                        Http::timeout(30)
                            ->withHeaders(['Accept' => 'image/*'])
                            ->sink($tmp)
                            ->get($url);

                        if (!file_exists($tmp) || filesize($tmp) <= 0) {
                            continue;
                        }

                        $uploaded = new UploadedFile($tmp, basename($tmp), null, null, true);
                        $storedPath = ImageUploader::storeCompressed($uploaded, 'kamar', 2048, 1920, 1920);
                        KamarFoto::create([
                            'kamar_id' => $kamar302->id,
                            'path' => $storedPath,
                            'urutan' => $i + 1,
                        ]);
                    } catch (\Throwable $e) {
                        // skip
                    } finally {
                        if (is_file($tmp)) @unlink($tmp);
                    }
                }
            }
        }
    }
}
