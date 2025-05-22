<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    // Kendaraan.php
    public function histories()
    {
        return $this->hasMany(HistoryKendaraan::class);
    }
}
