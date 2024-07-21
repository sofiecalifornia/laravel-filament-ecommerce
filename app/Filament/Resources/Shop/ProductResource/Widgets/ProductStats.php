<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\ProductResource\Widgets;

use App\Filament\Resources\Shop\ProductResource\Pages\ListProducts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(trans('Total Products'), $this->getPageTableQuery()->count()),
            //            Stat::make(trans('Product Inventory'), $this->getPageTableQuery()->sum('qty')),
            //            Stat::make(trans('Average price'), number_format($this->getPageTableQuery()->avg('price'), 2)),
        ];
    }
}
