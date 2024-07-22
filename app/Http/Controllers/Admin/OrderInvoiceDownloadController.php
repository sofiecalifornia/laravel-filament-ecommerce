<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Domain\Shop\Order\Models\OrderInvoice;
use Illuminate\Support\Facades\Gate;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('order-invoices/download')]
class OrderInvoiceDownloadController
{
    /**
     * @throws \Exception
     */
    #[Get('{orderInvoice}/invoice', 'order-invoices.download')]
    public function invoice(OrderInvoice $orderInvoice): mixed
    {
        Gate::authorize('downloadInvoice', $orderInvoice);

        return $orderInvoice->download();
    }
}
