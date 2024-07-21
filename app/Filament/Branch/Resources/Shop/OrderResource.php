<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop;

use App\Filament\Branch\Resources\Shop;

class OrderResource extends \App\Filament\Resources\Shop\OrderResource
{
    public static function getPages(): array
    {
        return [
            'index' => Shop\OrderResource\Pages\ListOrders::route('/'),
            'create' => Shop\OrderResource\Pages\CreateOrder::route('/create'),
            'view' => Shop\OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }
}
