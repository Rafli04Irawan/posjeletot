<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'outlet_id',
        'menu_permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'menu_permissions' => 'array',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [
            'admin',
            'pegawai',
            'kasir',
        ]);
    }

    public function hasMenuPermission(string $menu): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return in_array($menu, $this->menu_permissions ?? [], true);
    }
}