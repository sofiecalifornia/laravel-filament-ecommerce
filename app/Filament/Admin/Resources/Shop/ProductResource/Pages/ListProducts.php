<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\ProductResource\Pages;

use App\Filament\Admin\Resources\Shop\ProductResource;
use App\Filament\Admin\Resources\Shop\ProductResource\Widgets\ProductStats;
use Domain\Shop\Product\Enums\Status;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ProductResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }

    #[\Override]
    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            ...collect(Status::cases())
                ->mapWithKeys(
                    fn (Status $status) => [
                        $status->value => Tab::make($status->value)
                            ->query(fn ($query) => $query->where('status', $status))
                            ->label($status->getLabel())
                            ->icon($status->getIcon()),
                    ]
                )
                ->toArray(),
        ];
    }
}
