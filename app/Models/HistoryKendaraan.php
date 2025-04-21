<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryKendaraan extends Model
{
    protected $table = 'history_kendaraans';
    protected $fillable = [
        'kendaraan_id',
        'nama_mobil',
        'nopol',
        'status',
        'nama_pemakai',
        'departemen',
        'driver',
        'tujuan',
        'keterangan',
        'pic_update'
    ];
}
