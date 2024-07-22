<?php

declare(strict_types=1);

namespace App\Providers;

use App\Providers\ActivitylogLoggablePipes\MoneyFromLogChangesPipe;
use App\Providers\ActivitylogLoggablePipes\RedactHiddenAttributesFromLogChangesPipe;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Support\ServiceProvider;

/** @property \Illuminate\Foundation\Application $app */
class ActivityLogPipeChangesProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        Admin::addLogChange(new RedactHiddenAttributesFromLogChangesPipe());
        Customer::addLogChange(new RedactHiddenAttributesFromLogChangesPipe());

        Cart::addLogChange(new MoneyFromLogChangesPipe());
        Order::addLogChange(new MoneyFromLogChangesPipe());
        OrderItem::addLogChange(new MoneyFromLogChangesPipe());
        Sku::addLogChange(new MoneyFromLogChangesPipe());
    }
}
