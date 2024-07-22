<?php

declare(strict_types=1);

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Settings\SiteSettings;
use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\PermissionPages;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageSite extends SettingsPage implements HasPermissionPage
{
    use PermissionPages;

    protected static string $settings = SiteSettings::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $cluster = Settings::class;

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required(),

                        FileUpload::make('favicon')
                            ->image()
                            ->required()
                            ->openable()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file) => 'favicon.'.$file->extension()
                            ),

                        FileUpload::make('logo')
                            ->image()
                            ->required()
                            ->openable()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file) => 'logo.'.$file->extension()
                            ),

                        Forms\Components\Textarea::make('address')
                            ->translateLabel()
                            ->nullable()
                            ->string(),
                    ]),
            ])
            ->columns(2);
    }
}
