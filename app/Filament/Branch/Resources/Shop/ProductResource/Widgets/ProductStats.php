<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\ProductResource\Widgets;

use App\Filament\Branch\Resources\Shop\ProductResource\Pages\ListProducts;

class ProductStats extends \App\Filament\Admin\Resources\Shop\ProductResource\Widgets\ProductStats
{
    #[\Override]
    protected function getTablePage(): string
    {
        return ListProducts::class;
    }
}
