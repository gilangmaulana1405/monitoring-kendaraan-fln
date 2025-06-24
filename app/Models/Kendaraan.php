<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraans';

    protected $fillable = [
        'nama_mobil',
        'nopol',
        'gambar_mobil',
        'status',
        'isActive',
    ];

    // Kendaraan.php
    public function histories()
    {
        return $this->hasMany(HistoryKendaraan::class);
    }
}
