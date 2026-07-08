<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS saat menggunakan tunneling atau production
        if (request()->header('X-Forwarded-Proto') === 'https' || !app()->isLocal()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        if (\Illuminate\Support\Str::contains(request()->url(), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Livewire::setScriptRoute(function ($handle) {
            $prefix = env("LIVEWIRE_URL_PREFIX");
            return Route::get("{$prefix}/livewire/livewire.js", $handle);
        });
    }
}
