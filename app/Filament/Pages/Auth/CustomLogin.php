<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login;

class CustomLogin extends Login
{
    public function mount(): void
    {
        parent::mount();

        // Custom logic if needed
    }
}
