<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_promo',
        'keterangan',
        'start',
        'end',
        'fk_id_toko'
    ];

    public function productPromos()
    {
        return $this->hasMany(ProductPromo::class, 'fk_id_promo');
    }
}
