<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Akaunting\Money\Money;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;

final readonly class CalculateOrderItemTotalPriceAction
{
    public function execute(ItemWithMinMaxData $item): Money
    {
        $quantity = $item->quantity;

        if (null !== $item->minimum && $quantity < $item->minimum) {
            $quantity = $item->minimum;
        }

        return $item->price->multiply($quantity);
    }
}
