<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksis';

    protected $fillable = [
        'produk_id',
        'jumlah',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    public function billOfMaterials()
    {
        return $this->hasMany(\App\Models\BillOfMaterial::class, 'produk_id');
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class);
    }
}