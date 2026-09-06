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
        'outlet_id',
       
    ];

    public function billOfMaterials()
    {
        return $this->hasMany(BillOfMaterial::class, 'bahan_baku_id');
    }

    public function produkTerpakai()
    {
        return $this->belongsToMany(
            Produk::class,
            'bill_of_materials',
            'bahan_baku_id',
            'produk_id'
        )->distinct();
    }

    public function kelompokPersediaan(): string
    {
        $produk = $this->relationLoaded('produkTerpakai')
            ? $this->produkTerpakai
            : $this->produkTerpakai()->get();

        return $produk->count() === 1
            ? $produk->first()->nama_produk
            : 'Global';
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class, 'outlet_id');
    }
}