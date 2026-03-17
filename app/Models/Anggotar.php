<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggotar extends Model
{
    use HasFactory;
    protected $table = 'anggotars';

        protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pendidikan',
        'alamat',
        'no_hp',
        'email',
        'pendidikan_terakhir',
        'nama_sekolah',
        'jurusan',
        'tahun_masuk',
        'tanggal_daftar',
        'status', // ✅ FIX
        'divisi',
        'foto',
        'kartu_pengenal',
        'formulir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'date',
    ];
}
