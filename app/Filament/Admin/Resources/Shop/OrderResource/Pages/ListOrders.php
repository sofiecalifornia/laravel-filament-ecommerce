<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\OrderResource\Pages;

use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Widgets\TotalOrders;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Exports\OrderExporter;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->translateLabel()
                ->exporter(OrderExporter::class)
                ->authorize('exportAny')
                ->withActivityLog(),
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            TotalOrders::class,
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
