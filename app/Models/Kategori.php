<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori'; 
    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi ke produk
    public function produks()
    {
        return $this->hasMany(\App\Models\Produk::class);
    }
}
