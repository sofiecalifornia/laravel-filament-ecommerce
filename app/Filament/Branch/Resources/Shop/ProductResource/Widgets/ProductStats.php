<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\ProductResource\Widgets;

use App\Filament\Branch\Resources\Shop\ProductResource\Pages\ListProducts;

class ProductStats extends \App\Filament\Resources\Shop\ProductResource\Widgets\ProductStats
{
    protected function getTablePage(): string
    {
        return ListProducts::class;
    }
}
