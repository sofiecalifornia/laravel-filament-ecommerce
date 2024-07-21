<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Pipes\OrderCreated;

use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Stock\Actions\DecrementSkuStockAction;

class DecrementSkuStockPipe
{
    public function __construct(private DecrementSkuStockAction $decrementSkuStockAction)
    {
    }

    public function handle(OrderPipelineData $orderPipelineData, callable $next): OrderPipelineData
    {
        $this->decrementSkuStockAction->execute($orderPipelineData->order);

        return $next($orderPipelineData);
    }
}
