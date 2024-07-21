<?php

declare(strict_types=1);

namespace Domain\Shop\Order\DataTransferObjects;

use Domain\Shop\Order\Models\OrderItem;

final readonly class ItemWithMinMaxData
{
    public function __construct(
        public float $price,
        public float $quantity,
        public ?float $minimum,
        public ?float $maximum,
    ) {
    }

    public static function fromOrderItem(OrderItem $orderItem): self
    {
        return new self(
            price: $orderItem->sku->price,
            quantity: (float) $orderItem->quantity,
            minimum: $orderItem->sku->minimum,
            maximum: $orderItem->sku->maximum,
        );
    }
}
