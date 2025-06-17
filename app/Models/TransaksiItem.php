<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'jumlah_product',
        'harga_jual_product',
        'fk_id_product',
        'fk_id_transaksi'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_id_product');
    }
}
