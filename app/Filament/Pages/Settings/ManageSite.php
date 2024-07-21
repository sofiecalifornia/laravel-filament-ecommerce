<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Settings\SiteSettings;
use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\PermissionPages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSite extends SettingsPage implements HasPermissionPage
{
    use PermissionPages;

    protected static string $settings = SiteSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 1;

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
                        Forms\Components\TextInput::make('site_name')
                            ->translateLabel()
                            ->required(),
                    ]),
            ])
            ->columns(2);
    }
}
