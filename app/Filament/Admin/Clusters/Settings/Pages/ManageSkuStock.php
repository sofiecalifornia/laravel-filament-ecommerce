<?php

declare(strict_types=1);

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Settings\SkuStockSettings;
use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\PermissionPages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSkuStock extends SettingsPage implements HasPermissionPage
{
    use PermissionPages;

    protected static string $settings = SkuStockSettings::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $cluster = Settings::class;

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('yellow_warning_count')
                            ->translateLabel()
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->rule(
                                fn (Forms\Get $get) => function (string $attribute, int $value, callable $fail) use ($get
                                ) {
                                    if ($get('red_warning_count') >= $value) {
                                        $fail(trans('The :attribute must be greater than other field.'));
                                    }
                                }
                            )
                            ->helperText(trans('Greater than this value, the stock will be displayed in green.')),

                        Forms\Components\TextInput::make('red_warning_count')
                            ->translateLabel()
                            ->required()
                            ->integer()
                            ->minValue(0),

                    ]),
            ])
            ->columns(2);
    }
}
