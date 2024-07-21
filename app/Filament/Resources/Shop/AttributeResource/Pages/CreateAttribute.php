<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\AttributeResource\Pages;

use App\Filament\Resources\Shop\AttributeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
}
