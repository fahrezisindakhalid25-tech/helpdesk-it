<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS jika sedang menggunakan Tunneling (LocalTunnel/Ngrok)
        // Atau jika aplikasi mendeteksi protocol https di header
        if (request()->header('X-Forwarded-Proto') === 'https' || ! app()->isLocal()) {
            URL::forceScheme('https');
        }

        // HACK: Untuk LocalTunnel, kadang APP_ENV tetap 'local' tapi kita akses via HTTPS.
        // Kita paksa saja jika URL saat ini mengandung 'https'
        if (Str::contains(request()->url(), 'https://')) {
            URL::forceScheme('https');
        }
        Livewire::setScriptRoute(function ($handle) {
            $prefix = env('LIVEWIRE_URL_PREFIX');

            return Route::get("{$prefix}/livewire/livewire.js", $handle);
        });
    }
}
