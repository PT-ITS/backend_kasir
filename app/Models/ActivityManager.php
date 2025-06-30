<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'activity',
        'deskripsi'
    ];  
}
