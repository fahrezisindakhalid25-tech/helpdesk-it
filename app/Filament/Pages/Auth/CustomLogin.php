<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends Login
{
    public function mount(): void
    {
        parent::mount();
        
        // Custom logic if needed
    }
}
