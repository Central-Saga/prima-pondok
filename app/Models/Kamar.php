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
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function fotos()
    {
        return $this->hasMany(KamarFoto::class)->orderBy('urutan')->orderBy('id');
    }
}
