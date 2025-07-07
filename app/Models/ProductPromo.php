<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromo extends Model
{
    use HasFactory;

    protected $fillable = [
        'harga',
        'stock',
        'fk_id_product',
        'fk_id_promo',
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'fk_id_promo');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_id_product');
    }
}
