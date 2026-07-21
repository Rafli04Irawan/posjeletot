<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk'; 
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'kategori_id',
        'gambar',
    ];
    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(\App\Models\Kategori::class);
    }
    public function billOfMaterials()
    {
        return $this->hasMany(BillOfMaterial::class, 'produk_id');
    }
    public function jumlahBisaDibuat()
    {
        $boms = $this->billOfMaterials()->with('bahanBaku')->get();

        if ($boms->isEmpty()) {
            return 0;
        }

        $hasil = [];

        foreach ($boms as $bom) {

            if (!$bom->bahanBaku) {
                continue;
            }

            $stok = $bom->bahanBaku->stok;
            $jumlah = $bom->jumlah;

            // KG -> Gram
            if ($bom->bahanBaku->satuan == 'kg' && $bom->satuan == 'gram') {
                $stok *= 1000;
            }

            // Liter -> ml
            if ($bom->bahanBaku->satuan == 'liter' && $bom->satuan == 'ml') {
                $stok *= 1000;
            }

            // Gram -> Kg
            if ($bom->bahanBaku->satuan == 'gram' && $bom->satuan == 'kg') {
                $stok /= 1000;
            }

            // ml -> Liter
            if ($bom->bahanBaku->satuan == 'ml' && $bom->satuan == 'liter') {
                $stok /= 1000;
            }

            if ($jumlah <= 0) {
                continue;
            }

            $hasil[] = floor($stok / $jumlah);
        }

        return empty($hasil) ? 0 : min($hasil);
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class);
    }
}
