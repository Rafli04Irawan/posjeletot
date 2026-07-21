<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
        'nama_outlet',
        'alamat',
    ];
     public function produksis()
    {
        return $this->hasMany(Produksi::class);
    }
}