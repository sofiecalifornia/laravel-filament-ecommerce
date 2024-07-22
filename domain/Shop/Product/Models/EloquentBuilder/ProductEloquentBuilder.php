<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models\EloquentBuilder;

use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Product\Models\Product>
 */
class ProductEloquentBuilder extends Builder
{
    public function whereBaseOnStocksIsWarning(): self
    {
        return $this->whereRelation(
            'skus.skuStocks',
            fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksIsWarning()
        );
    }

    public function whereBaseOnStocksNotZero(): self
    {
        return $this->whereRelation(
            'skus.skuStocks',
            fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksNotZero()
        );
    }
}
