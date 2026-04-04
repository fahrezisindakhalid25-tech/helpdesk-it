<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        /** @var User */
        $user = Auth::user();

        return $user->hasPermission('dashboard.view');
    }
}
