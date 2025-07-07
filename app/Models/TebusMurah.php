<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TebusMurah extends Model
{
    use HasFactory;

    protected $fillable = [
        'harga',
        'minimal_belanja',
        'start',
        'end',
        'stock',
        'fk_id_product'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_id_product');
    }
}
