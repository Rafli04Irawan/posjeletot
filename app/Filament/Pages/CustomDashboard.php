<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class CustomDashboard extends Dashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (!$user instanceof \App\Models\User) {
            return false;
        }

        return $user->role === 'admin' || $user->hasMenuPermission('dashboard');
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
