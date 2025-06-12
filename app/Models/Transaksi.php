<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_bayar',
        'total_modal',
        'jenis_transaksi',
        'fk_id_kasir',
        'fk_id_toko'
    ];

    public function items()
    {
        return $this->hasMany(TransaksiItem::class, 'fk_id_transaksi');
    }
}
