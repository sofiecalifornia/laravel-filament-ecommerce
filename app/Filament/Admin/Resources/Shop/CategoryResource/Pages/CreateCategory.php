<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CategoryResource\Pages;

use App\Filament\Admin\Resources\Shop\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
