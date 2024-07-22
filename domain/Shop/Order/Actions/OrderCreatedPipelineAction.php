<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Pipes\OrderCreated\DecrementSkuStockPipe;
use Domain\Shop\Order\Pipes\OrderCreated\GenerateInvoicePipe;
use Domain\Shop\Order\Pipes\OrderCreated\NotificationPipe;
use Illuminate\Support\Facades\Pipeline;

final readonly class OrderCreatedPipelineAction
{
    public function execute(Order $order): void
    {
        Pipeline::send(new OrderPipelineData($order))
            ->through([
                GenerateInvoicePipe::class,
                DecrementSkuStockPipe::class,
                NotificationPipe::class,
            ])
            ->thenReturn();
    }
}
