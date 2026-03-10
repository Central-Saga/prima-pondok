<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarMaintenance extends Model
{
    use HasFactory;

    protected $table = 'kamar_maintenances';

    protected $fillable = [
        'kamar_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * Scope: jadwal maintenance yang aktif (berlaku hari ini atau di masa depan)
     */
    public function scopeActive($query)
    {
        return $query->where('tanggal_selesai', '>=', now()->toDateString());
    }

    /**
     * Scope: maintenance yang overlap dengan rentang tanggal tertentu
     */
    public function scopeOverlapping($query, $start, $end)
    {
        return $query->where('tanggal_mulai', '<=', $end)
                     ->where('tanggal_selesai', '>=', $start);
    }
}
