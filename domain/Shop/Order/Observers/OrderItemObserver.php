<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Observers;

use Domain\Shop\Order\Actions\CalculateOrderItemTotalPriceAction;
use Domain\Shop\Order\Actions\GetPaidQuantityAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Models\OrderItem;

class OrderItemObserver
{
    public function creating(OrderItem $orderItem): void
    {
        $orderItem->sku_code = $orderItem->sku->code;
        $orderItem->name = $orderItem->sku->product->name;

        $orderItem->paid_quantity = app(GetPaidQuantityAction::class)
            ->execute((float) $orderItem->quantity, (float) $orderItem->minimum);

        $orderItem->total_price = (int) app(CalculateOrderItemTotalPriceAction::class)
            ->execute(ItemWithMinMaxData::fromOrderItem($orderItem))
            ->getAmount();

        $orderItem->price = $orderItem->sku->price;
        $orderItem->minimum = $orderItem->sku->minimum;
        $orderItem->maximum = $orderItem->sku->maximum;
    }
}
