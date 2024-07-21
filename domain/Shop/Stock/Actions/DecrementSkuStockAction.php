<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Actions;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Stock\Enums\StockType;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class DecrementSkuStockAction
{
    public function __construct(private NotifyLowerStockAction $notifyLowerStockAction)
    {
    }

    public function execute(Order $order): void
    {
        $order->load([
            'orderItems.sku.skuStocks' => fn (HasMany $query) => $query
                ->whereBelongsTo($order->branch),
        ]);

        $order->orderItems->map(fn (OrderItem $orderItem) => $this->decrement($orderItem));
    }

    private function decrement(OrderItem $orderItem): void
    {
        /** @var \Domain\Shop\Stock\Models\SkuStock $skuStock */
        $skuStock = $orderItem->sku->skuStocks[0];

        if ($skuStock->type === StockType::BASE_ON_STOCK) {

            $skuStock->decrement('count', $orderItem->quantity);

            if ($skuStock->count <= $skuStock->warning) {
                $this->notifyLowerStockAction->execute($skuStock, $orderItem->order);
            }
        }
    }
}
