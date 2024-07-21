<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Pipes\OrderCreated\DecrementSkuStockPipe;
use Domain\Shop\Order\Pipes\OrderCreated\NotifyAdminPipe;
use Illuminate\Support\Facades\Pipeline;

final readonly class OrderCreatedPipelineAction
{
    public function execute(Order $order): void
    {
        Pipeline::send(new OrderPipelineData($order))
            ->through([
                DecrementSkuStockPipe::class,
                NotifyAdminPipe::class,
            ])
            ->thenReturn();
    }
}
