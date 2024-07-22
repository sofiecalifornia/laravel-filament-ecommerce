<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Product\Models\Sku;
use Filament\Support\Exceptions\Halt;

class SkuObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Sku $sku): void
    {
        $sku->loadCount('carts');

        if ($sku->carts_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $sku,
                trans('Can not delete sku with associated carts.'),
                'carts',
                $sku->carts_count
            );

        }
    }

    public function updated(Sku $sku): void
    {

        // TODO: remove carts, then notify customer
    }
}
