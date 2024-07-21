<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Order\Exports\ExportOrder;
use Domain\Shop\Order\Models\Order;

final readonly class PrintOrderAction
{
    public function execute(Order $order): ExportOrder
    {
        return new ExportOrder($order);
    }
}
