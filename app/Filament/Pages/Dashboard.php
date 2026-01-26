<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        
        return $user->hasPermission('dashboard.view');
    }
}
