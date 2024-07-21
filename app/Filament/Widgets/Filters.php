<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

class Filters extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.filters';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('from')
                            ->translateLabel()
                            ->live()
                            ->afterStateUpdated(
                                fn (?string $state) => $this
                                    ->dispatch('updateFromDate', from: $state)
                            ),
                        DatePicker::make('to')
                            ->translateLabel()
                            ->live()
                            ->afterStateUpdated(
                                fn (?string $state) => $this
                                    ->dispatch('updateToDate', to: $state)
                            ),
                    ]),
            ]);
    }
}
