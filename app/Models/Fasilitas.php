<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
    ];

    public function kamars()
    {
        return $this->belongsToMany(Kamar::class, 'fasilitas_kamar', 'fasilitas_id', 'kamar_id');
    }
}
