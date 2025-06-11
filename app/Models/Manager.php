<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_manager',
        'hp_manager',
        'alamat_manager',
        'fk_id_user',
    ];
}
