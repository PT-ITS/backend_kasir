<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'foto',
        'shift',
        'tanggal_absensi',
        'jenis_absensi',
        'fk_id_kasir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_id_kasir');
    }

    public function kasir()
    {
        return $this->hasOne(Kasir::class, 'fk_id_user', 'fk_id_kasir');
    }
}
