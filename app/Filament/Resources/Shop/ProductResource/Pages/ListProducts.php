<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\ProductResource\Pages;

use App\Filament\Resources\Shop\ProductResource;
use App\Filament\Resources\Shop\ProductResource\Widgets\ProductStats;
use Domain\Shop\Product\Enums\Status;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => ListRecords\Tab::make('All'),
            ...collect(Status::cases())
                ->mapWithKeys(
                    fn (Status $status) => [
                        $status->value => ListRecords\Tab::make($status->value)
                            ->query(fn ($query) => $query->where('status', $status))
                            ->label($status->getLabel())
                            ->icon($status->getIcon()),
                    ]
                )
                ->toArray(),
        ];
    }
}
