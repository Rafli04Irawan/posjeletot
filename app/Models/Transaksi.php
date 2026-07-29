<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'outlet_id',
    ];

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'id');
    }
    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class);
    }
}