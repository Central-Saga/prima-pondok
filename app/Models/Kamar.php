<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'nama_kamar',
        'tipe_kamar',
        'harga',
        'deskripsi',
        'deskripsi_en',
        'status',
        'fotos',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'fotos' => 'array',
    ];

    /**
     * Konstanta status kamar
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Cek apakah kamar bisa dipesan
     */
    public function isBookable(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_AVAILABLE;
    }

    /**
     * Cek apakah kamar sedang maintenance
     */
    public function isMaintenance(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_MAINTENANCE;
    }

    /**
     * Status tampilan
     */
    public function getDisplayStatus(): string
    {
        return $this->status;
    }

    /**
     * Cek apakah kamar tidak tersedia
     */
    public function isUnavailable(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_UNAVAILABLE;
    }

    public function getDeskripsiAttribute($value): mixed
    {
        if (app()->getLocale() === 'en') {
            $english = $this->getRawOriginal('deskripsi_en');

            return filled($english) ? $english : $value;
        }

        return $value;
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_kamar', 'kamar_id', 'fasilitas_id');
    }
}
