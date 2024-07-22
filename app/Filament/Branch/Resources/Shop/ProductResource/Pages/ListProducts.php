<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\ProductResource\Pages;

use App\Filament\Branch\Resources\Shop\ProductResource;
use App\Filament\Branch\Resources\Shop\ProductResource\Widgets\ProductStats;

class ListProducts extends \App\Filament\Admin\Resources\Shop\ProductResource\Pages\ListProducts
{
    protected static string $resource = ProductResource::class;

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }
}
