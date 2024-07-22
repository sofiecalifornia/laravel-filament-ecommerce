<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop;

class OrderResource extends \App\Filament\Admin\Resources\Shop\OrderResource
{
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'create' => OrderResource\Pages\CreateOrder::route('/create'),
            'view' => OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }
}
