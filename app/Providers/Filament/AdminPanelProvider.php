<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::Amber])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ConvertEmptyStringsToNull::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins($this->plugins());
    }

    /**
     * Os plugins abaixo transformam o painel em console de operação.
     * Descomente conforme instalar cada pacote.
     *
     * @return array<int, \Filament\Contracts\Plugin>
     */
    private function plugins(): array
    {
        return [
            // \FilipFonal\FilamentLogManager\FilamentLogManager::make(),
            // \ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin::make(),
            // \ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin::make(),
            // \Stephenjude\FilamentDebugger\DebuggerPlugin::make(),
            // \RickDBCN\FilamentEmail\FilamentEmail::make(),
            // \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin::make(),
            // \CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin::make(),
        ];
    }
}
