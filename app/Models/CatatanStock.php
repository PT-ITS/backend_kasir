<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_harga',
        'bukti_nota',
        'tanggal_belanja',
        'fk_id_toko'
    ];

    public function tambahStocks()
    {
        return $this->hasMany(TambahStock::class, 'fk_id_catatan_stock');
    }
}
