<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Observers;

use Domain\Shop\Brand\Models\Brand;

class BrandObserver
{
    public function deleting(Brand $brand): void
    {
        if ($brand->products()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete brand with associated products.'));
        }
    }
}
