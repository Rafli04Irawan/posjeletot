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
        return $this->hitungProduksi()['maksimal'];
    }

    public function hitungProduksi(int $jumlahDiminta = 0): array
    {
        $boms = $this->billOfMaterials()->with('bahanBaku')->get();
        $kapasitas = [];
        $kekurangan = [];

        foreach ($boms as $bom) {
            if (!$bom->bahanBaku || $bom->jumlah <= 0) {
                continue;
            }

            $stok = $this->stokDalamSatuanBom($bom);
            $kapasitas[] = (int) floor($stok / $bom->jumlah);

            $kebutuhan = $bom->jumlah * $jumlahDiminta;
            if ($jumlahDiminta > 0 && $stok < $kebutuhan) {
                $kekurangan[] = [
                    'nama' => $bom->bahanBaku->nama_bahan,
                    'dibutuhkan' => $kebutuhan,
                    'tersedia' => $stok,
                    'satuan' => $bom->satuan,
                ];
            }
        }

        return [
            'maksimal' => empty($kapasitas) ? 0 : min($kapasitas),
            'kekurangan' => $kekurangan,
        ];
    }

    protected function stokDalamSatuanBom($bom): float
    {
        $stok = (float) $bom->bahanBaku->stok;

        if ($bom->bahanBaku->satuan === 'kg' && $bom->satuan === 'gram') {
            return $stok * 1000;
        }

        if ($bom->bahanBaku->satuan === 'liter' && $bom->satuan === 'ml') {
            return $stok * 1000;
        }

        if ($bom->bahanBaku->satuan === 'gram' && $bom->satuan === 'kg') {
            return $stok / 1000;
        }

        if ($bom->bahanBaku->satuan === 'ml' && $bom->satuan === 'liter') {
            return $stok / 1000;
        }

        return $stok;
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class);
    }
}
