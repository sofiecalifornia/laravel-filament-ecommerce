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
        string $skuColumn = 'uuid',
    ) {
        $query = $sku instanceof Sku
            ? Sku::whereKey($sku)
            : Sku::where($skuColumn, $sku);

        $this->skuModel = $query
            ->whereRelation('product', 'status', Status::in_stock)
            ->with([
                'skuStocks' => fn (HasMany $query) => $query
                    ->whereBelongsTo($branch),
            ])
            ->first();
    }

    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $quantity = (float) $value;
        unset($value);

        $skuStock = $this->skuModel?->skuStocks[0] ?? null;

        if (null === $skuStock) {
            $fail(trans('Sku stock not ready.'));

            return;
        }

        if (StockType::unlimited === $skuStock->type) {
            return;
        }

        if (StockType::unavailable === $skuStock->type) {
            $fail(trans('Sku stock is not available.'));

            return;
        }

        if (StockType::base_on_stock === $skuStock->type && $quantity > $skuStock->count) {
            /** @var int $count */
            $count = $skuStock->count;
            $fail(trans('Sku Stock is insufficient, available: :count.', ['count' => $count]));

            return;
        }
    }
}
