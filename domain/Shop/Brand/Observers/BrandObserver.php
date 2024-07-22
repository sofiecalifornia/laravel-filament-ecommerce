<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Brand\Models\Brand;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;

class BrandObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Brand $brand): void
    {
        $brand->loadCount([
            'products' => function (Builder $builder) {
                /** @var \Domain\Shop\Product\Models\Product|\Illuminate\Database\Eloquent\Builder $builder */
                $builder->withTrashed();
            },
        ]);

        if ($brand->products_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $brand,
                trans('Can not delete brand with associated products.'),
                'products',
                $brand->products_count
            );

        }
    }
}
