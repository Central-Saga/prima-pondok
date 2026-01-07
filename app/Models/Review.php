<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'pemesanan_id',
        'wisatawan_id',
        'kamar_id',
        'rating',
        'komentar',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function wisatawan()
    {
        return $this->belongsTo(Wisatawan::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}

