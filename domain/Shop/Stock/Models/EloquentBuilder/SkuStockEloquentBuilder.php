<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Models\EloquentBuilder;

use Domain\Shop\Stock\Enums\StockType;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Stock\Models\SkuStock>
 */
class SkuStockEloquentBuilder extends Builder
{
    public function whereBaseOnStocksIsWarning(): self
    {
        return $this->where('type', StockType::base_on_stock)
            ->whereColumn('count', '<', 'warning');
    }

    public function whereBaseOnStocksIsNotWarning(): self
    {
        return $this->where('type', StockType::base_on_stock)
            ->whereColumn('count', '>=', 'warning');
    }

    public function whereBaseOnStocksNotZero(): self
    {
        return $this->where('type', StockType::base_on_stock)
            ->where('count', '>=', 0);
    }
}
