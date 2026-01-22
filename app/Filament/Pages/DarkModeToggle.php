<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class DarkModeToggle extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-moon';
    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string | Htmlable
    {
        return 'Theme Settings';
    }

    public function toggleTheme(string $mode): void
    {
        auth()->user()->update(['theme_mode' => $mode]);
        $this->dispatch('theme-changed', mode: $mode);
    }
}
