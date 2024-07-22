<?php

declare(strict_types=1);

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
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

    protected static string $settings = OrderSettings::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $cluster = Settings::class;

    #[\Override]
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

                        Forms\Components\TextInput::make('maximum_advance_booking_days')
                            ->translateLabel()
                            ->required()
                            ->minValue(0)
                            ->maxValue(60)
                            ->numeric()
                            ->helperText(trans('Default when branch has no specified.')),

                        Forms\Components\Select::make('admin_notification_ids')
                            ->label('Admin Notifications')
                            ->translateLabel()
                            ->multiple()
                            // TODO: add limit on result options for dropdown
                            ->options(Admin::pluck('name', 'uuid'))
                            ->getSearchResultsUsing(
                                fn (string $search): array => Admin::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->orderBy('name')
                                    ->pluck('name', 'uuid')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                    ]),
            ])
            ->columns(2);
    }
}
