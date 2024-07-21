<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\SkuStockResource\Pages;

use App\Filament\Resources\Shop\SkuStockResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property-read \Domain\Shop\Stock\Models\SkuStock $record
 */
class EditSkuStock extends EditRecord
{
    protected static string $resource = SkuStockResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->sku->product->name.' - '.$this->record->sku->code;
    }
}
