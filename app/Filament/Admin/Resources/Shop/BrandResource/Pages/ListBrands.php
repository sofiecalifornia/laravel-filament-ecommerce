<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\BrandResource\Pages;

use App\Filament\Admin\Resources\Shop\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
