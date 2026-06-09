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

        $jumlah = [];

        foreach ($boms as $bom) {
                if (!$bom->bahanBaku) {
                continue;
            }
                if ((int) $bom->jumlah <= 0) {
                continue;
            }


            $jumlah[] = floor((int)$bom->bahanBaku->stok / (int) $bom->jumlah);
        }

        return count($jumlah)  ? min($jumlah) : 0;
    }
}
