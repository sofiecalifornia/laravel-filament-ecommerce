<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;

final readonly class SaveOrderItemAction
{
    public function execute(Order $order, Sku $sku, float $quantity): OrderItem
    {
        return $order->orderItems()->create([
            'sku_id' => $sku->getKey(),
            'quantity' => $quantity,
        ]);
    }
}
