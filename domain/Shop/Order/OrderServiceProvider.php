<?php

declare(strict_types=1);

namespace Domain\Shop\Order;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/order.php', 'domain.shop.order');
    }
}
