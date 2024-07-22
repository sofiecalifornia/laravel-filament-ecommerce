<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Product\Models\Product;

class ProductObserver
{
    use LogAttemptDeleteResource;

    public function updated(Product $product): void
    {
        // TODO: remove carts, then notify customer
    }
}
