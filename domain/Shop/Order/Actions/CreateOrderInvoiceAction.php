<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderInvoice;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;

readonly class CreateOrderInvoiceAction
{
    public function __construct(private OrderInvoiceAction $orderInvoiceAction)
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(Order $order): OrderInvoice
    {
        // TODO: generate invoice base on status.

        $invoice = $this->orderInvoiceAction->execute($order);

        $invoice->save(disk: config('filesystems.default'));

        return $order->orderInvoices()->create([
            'file_name' => Str::afterLast($invoice->filename, '/'),
            'disk' => $invoice->disk,
            'path' => $invoice->filename,
        ]);
    }
}
