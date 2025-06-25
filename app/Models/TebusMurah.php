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
        'fk_id_product'
    ];
}
