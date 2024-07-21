<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\SkuStockResource\Pages;

use App\Filament\Resources\Shop\SkuStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSkuStocks extends ListRecords
{
    protected static string $resource = SkuStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
