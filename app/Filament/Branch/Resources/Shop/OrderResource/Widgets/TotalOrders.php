<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\OrderResource\Widgets;

use App\Filament\Branch\Resources\Shop\OrderResource\Pages\ListOrders;

class TotalOrders extends \App\Filament\Admin\Resources\Shop\OrderResource\Widgets\TotalOrders
{
    #[\Override]
    protected function getTablePage(): string
    {
        return ListOrders::class;
    }
}
