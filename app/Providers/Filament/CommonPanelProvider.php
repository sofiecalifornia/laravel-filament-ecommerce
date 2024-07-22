<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Providers\Macros\FilamentActionMixin;
use App\Providers\Macros\FilamentMountableActionMixin;
use App\Providers\Macros\FilamentTextInputMixin;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Actions\Action;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class CommonPanelProvider extends ServiceProvider
{
    public function boot(): void
    {
        self::configureComponents();

        self::registerMacros();

        Infolist::$defaultCurrency =
        Tables\Table::$defaultCurrency = config('money.defaults.currency');

        Infolist::$defaultDateTimeDisplayFormat =
        Tables\Table::$defaultDateTimeDisplayFormat = 'M d, Y h:i A';

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr'])
                ->circular();
        });

        // https://github.com/filamentphp/filament/issues/10002#issuecomment-1837511287
        Import::polymorphicUserRelationship();

        // https://filamentphp.com/docs/3.x/actions/prebuilt-actions/export#using-a-polymorphic-user-relationship
        Export::polymorphicUserRelationship();

    }

    private static function registerMacros(): void
    {
        MountableAction::mixin(new FilamentMountableActionMixin());
        Forms\Components\TextInput::mixin(new FilamentTextInputMixin());
        Action::mixin(new FilamentActionMixin());
    }

    private static function configureComponents(): void
    {
        Infolists\Components\TextEntry::configureUsing(
            function (Infolists\Components\TextEntry $component) {
                $component->lineClamp(1);
                if (Filament::auth()->check()) {
                    $component
                        ->timezone(
                            /** @phpstan-ignore-next-line  */
                            Filament::auth()->user()->timezone
                        );
                }
            }
        );

        Forms\Components\DateTimePicker::configureUsing(
            function (Forms\Components\DateTimePicker $component): void {
                if (Filament::auth()->check()) {
                    $component
                        ->timezone(
                            /** @phpstan-ignore-next-line  */
                            Filament::auth()->user()->timezone
                        );
                }

                //                $component
                //                    ->seconds(false);
            }
        );

        //        Forms\Components\TimePicker::configureUsing(
        //            function (Forms\Components\TimePicker $component): void {
        //                if (Filament::auth()->check()) {
        //                    $component
        //                        ->afterStateHydrated(function (string $state, Forms\Components\TimePicker $component) {
        //                            $component->state(now()->parse($state)->timezone(Filament::auth()->user()->timezone)->toTimeString());
        //                        });
        ////                        ->timezone(
        ////                            Filament::auth()->user()->timezone
        ////                        );
        //                }
        //            }
        //        );
        Tables\Columns\TextColumn::configureUsing(
            function (Tables\Columns\TextColumn $column): void {
                $column->lineClamp(1);
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
                ->extremePaginationLinks()
                ->paginated([5, 10, 25, 50, 100])
        );
    }
}
