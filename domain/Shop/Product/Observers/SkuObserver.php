<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Observers;

use Domain\Shop\Product\Models\Sku;

class SkuObserver
{
    public function deleting(Sku $sku): void
    {
        if ($sku->carts->count() > 0) {
            abort(403, trans('Can not delete sku with associated carts.'));
        }
    }

    public function updated(Sku $sku): void
    {

        // TODO: remove carts, then notify customer
    }
}
