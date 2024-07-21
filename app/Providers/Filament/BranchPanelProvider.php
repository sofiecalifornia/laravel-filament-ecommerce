<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyBranchTenantScopes;
use Domain\Shop\Branch\Models\Branch;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class BranchPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('branch')
            ->path('admin/branch')
            ->authGuard('admin')
            ->tenant(Branch::class)
            ->tenantMiddleware(
                [
                    ApplyBranchTenantScopes::class,
                    SetTheme::class,
                ],
                isPersistent: true
            )
            ->favicon(asset('images/shopping-cart.png'))
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: app_path('Filament/Branch/Resources'), for: 'App\\Filament\\Branch\\Resources')
            ->discoverPages(in: app_path('Filament/Branch/Pages'), for: 'App\\Filament\\Branch\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Branch/Widgets'), for: 'App\\Filament\\Branch\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->darkMode()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->databaseNotifications()
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
            ])
            ->plugins([
                ThemesPlugin::make(),
            ])
            ->renderHook(
                'panels::styles.after',
                fn (): string => Blade::render('<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">'),
            )
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render('<x-support-bubble />'),
            )
            ->renderHook(
                'panels::page.end',
                fn () => new HtmlString('
                        <p>
                            Powered by
                            <a
                                href="https://lloricode.com"
                                target="_blank"
                            >
                                lloricode.com
                            </a>
                        </p>
                    '),
            );
    }
}
