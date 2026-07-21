<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
        'nama_outlet',
        'alamat',
    ];
}