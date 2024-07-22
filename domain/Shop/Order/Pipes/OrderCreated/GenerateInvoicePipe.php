<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Pipes\OrderCreated;

use Domain\Shop\Order\Actions\CreateOrderInvoiceAction;
use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class GenerateInvoicePipe
{
    public function __construct(private CreateOrderInvoiceAction $orderInvoice)
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function handle(OrderPipelineData $orderPipelineData, callable $next): OrderPipelineData
    {
        $this->orderInvoice->execute($orderPipelineData->order);

        return $next($orderPipelineData);
    }
}
