<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\AttributeResource\Pages;

use App\Filament\Resources\Shop\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
