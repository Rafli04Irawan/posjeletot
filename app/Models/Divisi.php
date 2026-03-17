<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    // Laravel otomatis pakai tabel "divisis"

    protected $fillable = [
        'nama',
        'singkatan',
        'deskripsi',
    ];
}