<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\BrandResource\Pages;

use App\Filament\Admin\Resources\Shop\BrandResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;
}
