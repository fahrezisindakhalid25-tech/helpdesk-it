<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ThemeSwitcher extends Widget
{
    protected static string $view = 'filament.widgets.theme-switcher';

    public function switchTheme(string $mode): void
    {
        auth()->user()->update(['theme_mode' => $mode]);
        // Refresh page untuk apply theme
        $this->redirect(request()->header('Referer') ?? '/admin');
    }

    public function getCurrentTheme(): string
    {
        return auth()->user()->theme_mode ?? 'light';
    }
}
