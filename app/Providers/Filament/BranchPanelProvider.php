<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Branch\Pages\Auth\Google2FA\Recovery;
use App\Filament\Branch\Pages\Auth\Google2FA\TwoFactorAuthenticator;
use App\Filament\Branch\Pages\Dashboard\MainDashboard;
use App\Http\Middleware\ApplyBranchTenantScopes;
use App\Settings\SiteSettings;
use Domain\Shop\Branch\Models\Branch;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use PragmaRX\Google2FALaravel\Middleware as Google2FAMiddleware;

class BranchPanelProvider extends PanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('branch')
            ->brandName(fn () => app(SiteSettings::class)->name)
            ->favicon(fn () => app(SiteSettings::class)->getSiteFaviconUrl())
            ->brandLogo(fn () => app(SiteSettings::class)->getSiteLogoUrl())
            ->path('admin/branch')
            ->authGuard('admin')
            ->tenant(Branch::class)
            ->login(
                Login::class // TODO: remove this when in real production site
            )
            ->emailVerification()
            ->passwordReset()
            ->tenantMiddleware(
                [
                    ApplyBranchTenantScopes::class,
                    SetTheme::class,
                ],
                isPersistent: true
            )
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: app_path('Filament/Branch/Resources'), for: 'App\\Filament\\Branch\\Resources')
            ->discoverPages(in: app_path('Filament/Branch/Pages'), for: 'App\\Filament\\Branch\\Pages')
            ->pages([
                MainDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Branch/Widgets'), for: 'App\\Filament\\Branch\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->spa()
//            ->unsavedChangesAlerts(! $this->app->isLocal())
            ->databaseNotifications()
            ->databaseTransactions()
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
                Google2FAMiddleware::class,
            ])
            ->plugins([
                ThemesPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function (): string {

                    $key = config('app-sentry.session_replay');

                    if (null === $key) {
                        return '';
                    }

                    $url = "https://js.sentry-cdn.com/$key.min.js";

                    return Blade::render("<script src=\"$url\" crossorigin=\"anonymous\"></script>");
                },
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => Blade::render('<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('<x-support-bubble />'),
            )
            ->renderHook(
                PanelsRenderHook::PAGE_END,
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
            )
            ->routes(function (Panel $panel) {

                Route::name('auth.')
                    ->middleware(Authenticate::class)
                    ->group(function () {

                        Route::name('google-2fa.')
                            ->prefix('google-2fa')
                            ->group(function () {

                                Route::get('otp', TwoFactorAuthenticator::class)
                                    ->name('otp');

                                Route::get('recovery', Recovery::class)
                                    ->name('recovery');

                            });

                    });

            });
    }
}
