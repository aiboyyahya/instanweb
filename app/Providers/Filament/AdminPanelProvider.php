<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
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
            ->brandName(new HtmlString(
                '<span class="iw-brand">
                    <img src="' . e(asset('images/instanweb-logo.png')) . '" alt="" width="36" height="36" class="iw-brand__mark" aria-hidden="true">
                    <span class="iw-brand__text">
                        <span class="iw-brand__name"><span class="iw-brand__ink">instan</span><span class="iw-brand__web">web</span></span>
                        <span class="iw-brand__sub">Panel Toko</span>
                    </span>
                </span>'
            ))
            ->favicon(asset('images/instanweb-logo.png'))
            ->login(fn() => redirect()->route('login.form'))

            ->colors([
                'primary' => Color::hex('#1e56fc'),
                'gray' => Color::Slate,
            ])
            ->viteTheme('resources/css/filament/admin.css')
            ->maxContentWidth(Width::SevenExtraLarge)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17.5rem')
            ->navigationGroups([
                NavigationGroup::make('Katalog')
                    ->icon(Heroicon::OutlinedSquares2x2),
                NavigationGroup::make('Penjualan')
                    ->icon(Heroicon::OutlinedShoppingBag),
                NavigationGroup::make('Konten')
                    ->icon(Heroicon::OutlinedDocumentText),
                NavigationGroup::make('Sistem')
                    ->icon(Heroicon::OutlinedCog6Tooth),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): \Illuminate\Contracts\View\View => view('filament.partials.theme-sync'),
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): \Illuminate\Contracts\View\View => view('filament.partials.store-link'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                   \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
                \App\Http\Middleware\IsAdmin::class,
            ])


            ->homeUrl(function (): string {
                $user = Auth::user();

                if (! $user) {
                    return route('login.form');
                }


                if ($user->role === 'admin') {
                    return Dashboard::getUrl(panel: 'admin');
                }


                return route('home');
            });
    }
} 