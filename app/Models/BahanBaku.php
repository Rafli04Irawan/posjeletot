<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_bakus';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok',
       
    ];

    public function billOfMaterials()
    {
        return $this->hasMany(BillOfMaterial::class);
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class);
    }
}