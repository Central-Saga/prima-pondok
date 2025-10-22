<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
    /**
     * Store an uploaded image compressed to target size (< ~2MB) and max dimensions.
     * Returns relative path under the public disk.
     */
    public static function storeCompressed(UploadedFile $file, string $directory = 'uploads', int $targetSizeKB = 2048, int $maxWidth = 1920, int $maxHeight = 1920): string
    {
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $tmp = $file->getRealPath();

        // Read original
        [$width, $height] = @getimagesize($tmp) ?: [null, null];
        if (!$width || !$height) {
            // If cannot read, just store raw as fallback
            $path = $file->store($directory, 'public');
            return str_replace('\\', '/', ltrim($path, '/'));
        }

        // Create image resource depending on mime
        $src = null;
        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            $src = @imagecreatefromjpeg($tmp);
        } elseif (str_contains($mime, 'png')) {
            $src = @imagecreatefrompng($tmp);
        } elseif (str_contains($mime, 'webp')) {
            if (function_exists('imagecreatefromwebp')) {
                $src = @imagecreatefromwebp($tmp);
            }
        } elseif (str_contains($mime, 'gif')) {
            // For GIF, just store original to preserve animation
            $path = $file->store($directory, 'public');
            return str_replace('\\', '/', ltrim($path, '/'));
        }

        if (!is_resource($src) && !($src instanceof \GdImage)) {
            // Fallback: store raw
            $path = $file->store($directory, 'public');
            return str_replace('\\', '/', ltrim($path, '/'));
        }

        // Resize down if larger than max dims
        $scale = min(1.0, $maxWidth / $width, $maxHeight / $height);
        if ($scale < 1.0) {
            $newW = (int) max(1, round($width * $scale));
            $newH = (int) max(1, round($height * $scale));
            $dst = imagecreatetruecolor($newW, $newH);

            // Preserve transparency for PNG/WebP
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            $src = $dst;
            $width = $newW; $height = $newH;
        }

        // Decide encoder: prefer WebP if available
        $useWebp = function_exists('imagewebp');
        $ext = $useWebp ? 'webp' : 'jpg';
        $filename = Str::random(20).'.'.$ext;
        $absolute = Storage::disk('public')->path(trim($directory, '/').'/'.$filename);
        @mkdir(dirname($absolute), 0775, true);

        // Iteratively lower quality to meet target size
        $quality = 85;
        $minQuality = 45;
        $ok = false;
        while ($quality >= $minQuality) {
            // Write to temp
            $tmpOut = $absolute.'.tmp';
            if ($useWebp) {
                @imagewebp($src, $tmpOut, $quality);
            } else {
                // For JPEG, fill white background if has alpha
                if (function_exists('imagecolorallocatealpha')) {
                    $bg = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopy($bg, $src, 0, 0, 0, 0, $width, $height);
                    imagejpeg($bg, $tmpOut, $quality);
                    imagedestroy($bg);
                } else {
                    imagejpeg($src, $tmpOut, $quality);
                }
            }

            $sizeKB = @filesize($tmpOut) / 1024;
            if ($sizeKB > 0 && $sizeKB <= $targetSizeKB) {
                // Move to final
                @rename($tmpOut, $absolute);
                $ok = true;
                break;
            }
            @unlink($tmpOut);
            $quality -= 10;
        }

        if (!$ok) {
            // As last resort, save at lowest quality
            if ($useWebp) {
                @imagewebp($src, $absolute, max($minQuality, $quality));
            } else {
                imagejpeg($src, $absolute, max($minQuality, $quality));
            }
        }

        if (is_resource($src) || ($src instanceof \GdImage)) @imagedestroy($src);

        $relative = trim($directory, '/').'/'.$filename;
        return str_replace('\\', '/', ltrim($relative, '/'));
    }
}

