<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TambahStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'jumlah',
        'harga_beli',
        'expired',
        'fk_id_catatan_stock',
        'fk_id_product',
    ];

    public function catatanStock()
    {
        return $this->belongsTo(CatatanStock::class, 'fk_id_catatan_stock');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_id_product');
    }
}
