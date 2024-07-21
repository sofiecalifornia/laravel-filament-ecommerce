<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Rules;

use Closure;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Enums\Status;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Enums\StockType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\HasMany;

readonly class CheckQuantitySkuStockRule implements ValidationRule
{
    private ?Sku $skuModel;

    public function __construct(
        Branch $branch,
        Sku|string|int $sku,
        string $skuColumn = 'id',
    ) {
        $query = $sku instanceof Sku
            ? Sku::whereKey($sku)
            : Sku::where($skuColumn, $sku);

        $this->skuModel = $query
            ->whereRelation('product', 'status', Status::IN_STOCK)
            ->with([
                'skuStocks' => fn (HasMany $query) => $query
                    ->whereBelongsTo($branch),
            ])
            ->first();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $quantity = (float) $value;
        unset($value);

        $skuStock = $this->skuModel?->skuStocks[0] ?? null;

        if ($skuStock === null) {
            $fail(trans('Sku stock not ready.'));

            return;
        }

        if ($skuStock->type === StockType::UNLIMITED) {
            return;
        }

        if ($skuStock->type === StockType::UNAVAILABLE) {
            $fail(trans('Sku stock is not available.'));

            return;
        }

        if ($skuStock->type === StockType::BASE_ON_STOCK && $quantity > $skuStock->count) {
            /** @var int $count */
            $count = $skuStock->count;
            $fail(trans('Sku Stock is insufficient, available: :count.', ['count' => $count]));

            return;
        }
    }
}
