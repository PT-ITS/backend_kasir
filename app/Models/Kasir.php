<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kasir',
        'hp_kasir',
        'alamat_kasir',
        'fk_id_toko',
        'fk_id_user',
    ];
}
