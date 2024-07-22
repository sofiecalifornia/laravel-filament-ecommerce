<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\ProductResource\Pages;

use App\Filament\Admin\Resources\Shop\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
