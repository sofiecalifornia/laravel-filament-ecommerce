<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Akaunting\Money\Money;

final readonly class CalculateOrderTotalPriceAction
{
    public function __construct(
        private CalculateOrderItemTotalPriceAction $calculateOrderItemTotalPriceAction,
    ) {
    }

    /** @param  \Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData[]  $itemWithMinMaxData */
    public function execute(array $itemWithMinMaxData): Money
    {
        $result = money(0);

        if (blank($itemWithMinMaxData)) {
            return $result;
        }

        foreach ($itemWithMinMaxData as $itemWithMinMaxDatum) {
            $result = $result
                ->add($this->calculateOrderItemTotalPriceAction->execute($itemWithMinMaxDatum));
        }

        return $result;
    }
}
