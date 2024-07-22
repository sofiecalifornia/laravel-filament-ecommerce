<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderInvoice;
use Illuminate\Support\Facades\Gate;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('orders/download')]
class OrderDownloadController
{
    /**
     * @throws \Exception
     */
    #[Get('{order}/invoice', 'orders.download.invoice')]
    public function invoice(Order $order): mixed
    {
        Gate::authorize('downloadInvoice', $order);

        /** @var OrderInvoice|null $invoice */
        $invoice = $order->orderInvoices->first();

        return $invoice?->download() ?? abort(404, trans('Invoice not found.'));
    }
}
