<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

final readonly class GetPaidQuantityAction
{
    public function execute(float $quantity1, float $quantity2): int|float
    {
        return max($quantity1, $quantity2);
    }
}
