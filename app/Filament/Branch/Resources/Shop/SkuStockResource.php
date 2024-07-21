<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop;

use App\Filament\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use App\Filament\Support\TenantHelper;
use Filament\Forms\Form;

class SkuStockResource extends \App\Filament\Resources\Shop\SkuStockResource
{
    public static function form(Form $form): Form
    {
        return SkuStockSchema::form($form, TenantHelper::getBranch());
    }

    public static function getPages(): array
    {
        return [
            'create' => SkuStockResource\Pages\CreateSkuStock::route('/create'),
            'index' => SkuStockResource\Pages\ListSkuStocks::route('/'),
            'edit' => SkuStockResource\Pages\EditSkuStock::route('/{record}/edit'),
        ];
    }
}
