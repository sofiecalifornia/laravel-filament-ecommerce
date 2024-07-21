<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Backups;
use App\Filament\Pages\HealthCheckResults;
use App\Http\Middleware\CheckMainAdminPanel;
use App\Providers\Filament\Versions\AppVersionProvider;
use App\Providers\Filament\Versions\LivewireVersionProvider;
use App\Providers\Macros\FilamentRadioMixin;
use App\Providers\Macros\FilamentSelectFilterMixin;
use App\Providers\Macros\FilamentSelectMixin;
use Awcodes\FilamentVersions\VersionsPlugin;
use Awcodes\FilamentVersions\VersionsWidget;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tables;
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
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            // TODO: remove this when in real production site
            ->login(Login::class)
            ->profile(EditProfile::class)
            ->favicon(asset('images/shopping-cart.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Pages\Dashboard::class,
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
                CheckMainAdminPanel::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn () => trans('Shop')),
                NavigationGroup::make()
                    ->label(fn () => trans('Access')),
                NavigationGroup::make()
                    ->label(fn () => trans('Settings')),
                NavigationGroup::make()
                    ->label(fn () => trans('Documentation')),
                NavigationGroup::make()
                    ->label(fn () => trans('System')),
            ])
            ->navigationItems([
                NavigationItem::make('API Documentation')
                    ->url(fn () => route('scramble.docs.api'), shouldOpenInNewTab: true)
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

                        if (! class_exists('\Laravel\Telescope\TelescopeServiceProvider')) {
                            return false;
                        }

                        if (! config('telescope.enabled')) {
                            return false;
                        }

                        return Filament::auth()->user()?->can('viewTelescope') ?? false;
                    }),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(HealthCheckResults::class),
                FilamentLanguageSwitchPlugin::make()
                    ->renderHookName('panels::global-search.before'),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class),
                VersionsPlugin::make()
                    ->widgetColumnSpan('full')
                    ->widgetSort(99)
                    ->items([
                        new AppVersionProvider(),
                        new LivewireVersionProvider(),
                    ]),
                ThemesPlugin::make(),
            ])
            ->darkMode()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->databaseNotifications()
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

    public function boot(): void
    {
        self::configureComponents();

        Forms\Components\Select::mixin(new FilamentSelectMixin());
        Forms\Components\Radio::mixin(new FilamentRadioMixin());
        Tables\Filters\SelectFilter::mixin(new FilamentSelectFilterMixin());

        // Tables\Table::$defaultCurrency = 'usd';
        Tables\Table::$defaultDateTimeDisplayFormat = 'M d, Y h:i A';

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
    }

    private static function configureComponents(): void
    {
        Forms\Components\DateTimePicker::configureUsing(
            function (Forms\Components\DateTimePicker $component): void {
                if (Filament::auth()->check()) {
                    $component
                        ->timezone(
                            /** @phpstan-ignore-next-line  */
                            Filament::auth()->user()->timezone
                        );
                }
            }
        );
        Tables\Columns\TextColumn::configureUsing(
            function (Tables\Columns\TextColumn $column): void {
                if (Filament::auth()->check()) {
                    $column
                        ->timezone(
                            /** @phpstan-ignore-next-line  */
                            Filament::auth()->user()->timezone
                        );
                }
            }
        );
        Tables\Table::configureUsing(
            fn (Tables\Table $table) => $table
                ->paginated([5, 10, 25, 50, 100])
        );
        Forms\Components\TextInput::configureUsing(
            fn (Forms\Components\TextInput $textInput) => $textInput
                ->maxLength(255)
        );
    }
}
