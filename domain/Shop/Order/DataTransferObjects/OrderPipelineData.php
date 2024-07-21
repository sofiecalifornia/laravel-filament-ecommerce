<?php

declare(strict_types=1);

namespace Domain\Shop\Order\DataTransferObjects;

use Domain\Shop\Order\Models\Order;

final readonly class OrderPipelineData
{
    public function __construct(
        public Order $order,
    ) {
    }
}
