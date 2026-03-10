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
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Konstanta status kamar
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Cek apakah kamar bisa dipesan (available atau maintenance dengan jadwal per tanggal)
     */
    public function isBookable(): bool
    {
        $status = strtolower((string) $this->status);
        return in_array($status, [self::STATUS_AVAILABLE, self::STATUS_MAINTENANCE]);
    }

    /**
     * Cek apakah kamar sedang maintenance
     */
    public function isMaintenance(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_MAINTENANCE;
    }

    /**
     * Status tampilan: jika status=maintenance tapi hari ini BUKAN di dalam jadwal maintenance,
     * tampilkan sebagai "available". Maintenance badge hanya muncul saat hari ini masuk jadwal.
     */
    public function getDisplayStatus(): string
    {
        if ($this->isMaintenance()) {
            return $this->hasActiveMaintenance() ? self::STATUS_MAINTENANCE : self::STATUS_AVAILABLE;
        }
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

    public function fotos()
    {
        return $this->hasMany(KamarFoto::class)->orderBy('urutan')->orderBy('id');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_kamar', 'kamar_id', 'fasilitas_id');
    }

    public function maintenances()
    {
        return $this->hasMany(KamarMaintenance::class);
    }

    /**
     * Cek apakah kamar punya jadwal maintenance aktif saat ini
     */
    public function hasActiveMaintenance(): bool
    {
        return $this->maintenances()
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->exists();
    }

    /**
     * Ambil maintenance yang aktif hari ini (untuk info message)
     */
    public function getActiveMaintenanceInfo(): ?KamarMaintenance
    {
        return $this->maintenances()
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->first();
    }
}
