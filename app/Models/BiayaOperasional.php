<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaOperasional extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_operasional',
        'waktu_operasional',
        'tanggal_bayar',
        'jumlah_biaya',
        'fk_id_toko',
    ];
}
