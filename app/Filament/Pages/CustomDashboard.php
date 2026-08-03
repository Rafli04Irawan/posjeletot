<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class CustomDashboard extends Dashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin' || auth()->user()?->hasMenuPermission('dashboard');
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
