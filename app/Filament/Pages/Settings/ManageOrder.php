<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Settings\OrderSettings;
use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\PermissionPages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Str;

class ManageOrder extends SettingsPage implements HasPermissionPage
{
    use PermissionPages;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = OrderSettings::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return trans('Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('prefix')
                            ->translateLabel()
                            ->required()
                            ->minValue(3)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (Set $set, $state) => $set(
                                    'prefix',
                                    (string) Str::of($state)
                                        ->upper()
                                        ->replace(' ', '_')
                                        ->trim()
                                )
                            )
                            ->alphaDash(),
                    ]),
            ])
            ->columns(2);
    }
}
