<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\BranchResource\Schema;

use Domain\Shop\OperationHour\Enums\Day;
use Domain\Shop\OperationHour\Enums\Type;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Set;

final class BranchSchema
{
    private function __construct()
    {
    }

    public static function operationHourSchema(Type $type): array
    {
        return [
            Forms\Components\Repeater::make(
                Type::online === $type
                    ? 'operationHoursOnline'
                    : 'operationHoursInStore'
            )
                ->translateLabel()
                ->itemLabel(
                    fn (array $state) => trans(':day (:is_open)', [
                        'day' => $state['day'],
                        'is_open' => ((bool) $state['is_open']) ? 'Open' : 'Closed',
                    ])
                )
                ->hiddenLabel()
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->orderColumn(config('eloquent-sortable.order_column_name'))
                ->reorderableWithButtons()
                ->schema([

                    Forms\Components\Hidden::make('type')
                        ->default($type),

                    Forms\Components\Select::make('day')
                        ->translateLabel()
                        ->options(Day::class)
                        ->enum(Day::class)
                        ->searchable()
                        ->required()
                        ->preload(),

                    Forms\Components\TimePicker::make('from')
                        ->translateLabel()
                        ->required()
                        ->afterStateHydrated(self::timePickerTimezoneResolver(...))
                        ->disabled(fn (Forms\Get $get) => $get('is_all_day'))
                        // to allow store in db even disabled
                        ->dehydrated(fn ($state) => $state),

                    Forms\Components\TimePicker::make('to')
                        ->translateLabel()
                        ->required()
                        ->afterStateHydrated(self::timePickerTimezoneResolver(...))
                        ->disabled(fn (Forms\Get $get) => $get('is_all_day'))
                        // to allow store in db even disabled
                        ->dehydrated(fn ($state) => $state),

                    Forms\Components\Checkbox::make('is_all_day')
                        ->translateLabel()
                        ->reactive()
                        ->afterStateUpdated(function (bool $state, Set $set) {
                            if ($state) {
                                $set('from', '00:00:00');
                                $set('to', '23:59:00');
                            }
                        }),

                    Forms\Components\Checkbox::make('is_open')
                        ->translateLabel(),
                ])
                ->columns(5),
        ];
    }

    private static function timePickerTimezoneResolver(?string $state, Forms\Components\TimePicker $component): void
    {
        if (null === $state) {
            return;
        }

        /** @var \Domain\Access\Admin\Models\Admin $admin */
        $admin = Filament::auth()->user();

        $component->state(now()->parse($state)->timezone($admin->timezone)->toTimeString());

    }
}
