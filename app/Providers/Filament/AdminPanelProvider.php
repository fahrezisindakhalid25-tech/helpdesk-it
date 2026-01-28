<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color; // PENTING: Import Warna
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            // Add global CSS for Login Page Background
            ->renderHook(
                'panels::body.end',
                fn () => view('filament.custom-login-style')
            )
            // Custom Logic: Inject Chart.js Plugin Registration
            ->renderHook(
                'panels::head.end',
                fn () => \Illuminate\Support\Facades\Blade::render('@vite(["resources/css/app.css", "resources/js/app.js"])')
            )
            
            // === BAGIAN INI YANG MENGUBAH TAMPILAN JADI BAGUS ===
            ->brandName('IT Helpdesk PTPN IV') // Mengganti tulisan "Laravel"
            ->colors([
                'primary' => Color::Green, // Mengubah tombol jadi HIJAU
            ])
            ->darkMode(true) // Enable dark mode - user bisa toggle
            // ====================================================

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\TicketStatsWidget::class,
                \App\Filament\Widgets\FirstResponseSlaChart::class,
                \App\Filament\Widgets\TicketStatusChart::class,
                \App\Filament\Widgets\TicketCategoryChart::class,
                // \App\Filament\Widgets\ThemeSwitcher::class, // Removed
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}