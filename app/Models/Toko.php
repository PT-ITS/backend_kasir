<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo_toko',
        'nama_toko',
        'hp_toko',
        'alamat_toko',
        'fk_id_manager',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_id_manager');
    }

    public function manager()
    {
        return $this->hasOne(Manager::class, 'fk_id_user', 'fk_id_manager');
    }
}
