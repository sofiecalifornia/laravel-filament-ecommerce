<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\OrderResource\Widgets;

use App\Filament\Branch\Resources\Shop\OrderResource\Pages\ListOrders;

class TotalOrders extends \App\Filament\Resources\Shop\OrderResource\Widgets\TotalOrders
{
    protected function getTablePage(): string
    {
        return ListOrders::class;
    }
}
