<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
use Illuminate\Support\Facades\Blade;
use App\Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\TicketStatsWidget;
use App\Filament\Widgets\FirstResponseSlaChart;
use App\Filament\Widgets\TicketStatusChart;
use App\Filament\Widgets\TicketCategoryChart;
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
            ->login(CustomLogin::class)
            // Add global CSS for Login Page Background
            ->renderHook(
                'panels::body.end',
                fn () => view('filament.custom-login-style')
            )
            // Custom Logic: Inject Chart.js Plugin Registration
            ->renderHook(
                'panels::head.end',
                fn () => Blade::render('@vite(["resources/css/app.css", "resources/js/app.js"])')
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
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                TicketStatsWidget::class,
                FirstResponseSlaChart::class,
                TicketStatusChart::class,
                TicketCategoryChart::class,
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