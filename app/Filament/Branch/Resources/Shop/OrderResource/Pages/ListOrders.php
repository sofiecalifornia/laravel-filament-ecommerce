<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\OrderResource\Pages;

use App\Filament\Branch\Resources\Shop\OrderResource;
use App\Filament\Branch\Resources\Shop\OrderResource\Widgets\TotalOrders;

class ListOrders extends \App\Filament\Resources\Shop\OrderResource\Pages\ListOrders
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TotalOrders::class,
        ];
    }
}
