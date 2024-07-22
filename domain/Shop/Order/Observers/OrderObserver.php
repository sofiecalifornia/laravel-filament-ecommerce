<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\Models\Order;

class OrderObserver
{
    use LogAttemptDeleteResource;

    public function creating(Order $order): void
    {
        if (blank($order->receipt_number)) {
            $order->receipt_number = app(GenerateReceiptNumberAction::class)->execute($order->branch);
        }

    }
}
