<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarFoto extends Model
{
    use HasFactory;

    protected $table = 'kamar_fotos';

    protected $fillable = [
        'kamar_id',
        'path',
        'urutan',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}

