<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\SkuStockResource\Pages;

use App\Filament\Admin\Resources\Shop\SkuStockResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSkuStock extends CreateRecord
{
    protected static string $resource = SkuStockResource::class;
}
