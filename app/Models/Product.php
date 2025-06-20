<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_product',
        'nama_product',
        'stock_product',
        'harga_jual',
        'harga_pokok',
        'barcode',
        'satuan',
        'jenis',
        'merek',
        'fk_id_toko',
    ];
}
