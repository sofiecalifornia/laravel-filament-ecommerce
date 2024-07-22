<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\EditProfile;
use App\Filament\Admin\Pages\Auth\Google2FA\Recovery;
use App\Filament\Admin\Pages\Auth\Google2FA\TwoFactorAuthenticator;
use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Admin\Pages\Backups;
use App\Filament\Admin\Pages\Dashboard\MainDashboard;
use App\Filament\Admin\Pages\HealthCheckResults;
use App\Jobs\QueueJobPriority;
use App\Providers\Filament\Versions\AppVersionProvider;
use App\Settings\SiteSettings;
use Awcodes\FilamentVersions\VersionsPlugin;
use Awcodes\FilamentVersions\VersionsWidget;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
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
use Laravel\Telescope\TelescopeServiceProvider;
use PragmaRX\Google2FALaravel\Middleware as Google2FAMiddleware;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName(fn () => app(SiteSettings::class)->name)
            ->favicon(fn () => app(SiteSettings::class)->getSiteFaviconUrl())
            ->brandLogo(fn () => app(SiteSettings::class)->getSiteLogoUrl())
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login(
                Login::class // TODO: remove this when in real production site
            )
            ->profile(EditProfile::class)
            ->emailVerification()
            ->passwordReset()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->pages([
                MainDashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                VersionsWidget::class,
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
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                Google2FAMiddleware::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn () => trans('Shop')),
                NavigationGroup::make()
                    ->label(fn () => trans('Access')),
                NavigationGroup::make()
                    ->label(fn () => trans('Configurations')),
                NavigationGroup::make()
                    ->label(fn () => trans('Documentation')),
                NavigationGroup::make()
                    ->label(fn () => trans('System')),
            ])
            ->navigationItems([
                NavigationItem::make('API Documentation')
                    ->url(fn () => route('scramble.docs.ui'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group(fn () => trans('Documentation'))
                    ->sort(1)
                    ->visible(fn () => Filament::auth()->user()?->can('viewApiDocs') ?? false),
                NavigationItem::make('Log Viewer')
                    ->url(fn () => route('log-viewer.index'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-fire')
                    ->group(fn () => trans('System'))
                    ->sort(2)
                    ->visible(fn () => Filament::auth()->user()?->can('viewLogViewer') ?? false),
                NavigationItem::make('Horizon')
                    ->url(fn () => route('horizon.index'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-globe-americas')
                    ->group(fn () => trans('System'))
                    ->sort(3)
                    ->visible(fn () => Filament::auth()->user()?->can('viewHorizon') ?? false),
                NavigationItem::make('Telescope')
                    ->url(fn () => route('telescope'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-sparkles')
                    ->group(fn () => trans('System'))
                    ->sort(4)
                    ->visible(function (): bool {

                        if (! class_exists(TelescopeServiceProvider::class)) {
                            return false;
                        }

                        if (! config('telescope.enabled')) {
                            return false;
                        }

                        return Filament::auth()->user()?->can('viewTelescope') ?? false;
                    }),
                NavigationItem::make('Pulse')
                    ->url(fn () => route('pulse'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-wrench')
                    ->group(fn () => trans('System'))
                    ->sort(3)
                    ->visible(fn () => Filament::auth()->user()?->can('viewPulse') ?? false),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(HealthCheckResults::class),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class)
                    ->usingQueue(QueueJobPriority::DB_BACKUP),
                VersionsPlugin::make()
                    ->widgetColumnSpan('full')
                    ->widgetSort(99)
                    ->items([
                        new AppVersionProvider(),
                    ]),
                ThemesPlugin::make(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->spa()
//            ->unsavedChangesAlerts(! $this->app->isLocal())
            ->databaseNotifications()
            ->databaseTransactions()
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
