<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Dashboard;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class MainDashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('start_date')
                            ->maxDate(fn (Get $get) => $get('end_date') ?? now()),
                        DatePicker::make('end_date')
                            ->minDate(fn (Get $get) => $get('start_date') ?? now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}
