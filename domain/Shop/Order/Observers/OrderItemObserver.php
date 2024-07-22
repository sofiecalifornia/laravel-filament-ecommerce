<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Order\Actions\CalculateOrderItemTotalPriceAction;
use Domain\Shop\Order\Actions\GetPaidQuantityAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Models\OrderItem;
use Illuminate\Support\Str;

class OrderItemObserver
{
    use LogAttemptDeleteResource;

    public function creating(OrderItem $orderItem): void
    {
        $orderItem->sku_code = $orderItem->sku->code;
        $orderItem->name = $orderItem->sku->product->name;

        $orderItem->paid_quantity = app(GetPaidQuantityAction::class)
            ->execute($orderItem->quantity, (float) $orderItem->minimum);

        $orderItem->total_price = app(CalculateOrderItemTotalPriceAction::class)
            ->execute(ItemWithMinMaxData::fromOrderItem($orderItem));

        $orderItem->discount_price = money(0);

        $orderItem->price = $orderItem->sku->price;
        $orderItem->minimum = $orderItem->sku->minimum;
        $orderItem->maximum = $orderItem->sku->maximum;

        if (null !== $orderItem->sku->product->description) {
            $orderItem->description = (string) Str::of($orderItem->sku->product->description)->stripTags();
        }
    }
}
