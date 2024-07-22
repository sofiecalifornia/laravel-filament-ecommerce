<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;

class BranchObserver
{
    use LogAttemptDeleteResource;

    public function created(Branch $branch): void
    {
        Admin::role(config('domain.access.role.super_admin'), guard: 'admin')
            ->get()
            ->each(fn (Admin $admin) => $admin->branches()->attach($branch));
    }

    /**
     * @throws Halt
     */
    public function deleting(Branch $branch): void
    {
        $branch->loadCount([
            'skuStocks',
            'orders' => function (Builder $builder) {
                /** @var \Domain\Shop\Order\Models\Order|\Illuminate\Database\Eloquent\Builder $builder */
                $builder->withTrashed();
            },
        ]);

        if ($branch->orders_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $branch,
                trans('Can not delete branch with associated orders.'),
                'orders',
                $branch->orders_count
            );

        }
        if ($branch->sku_stocks_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $branch,
                trans('Can not delete branch with associated stocks.'),
                'skuStocks',
                $branch->sku_stocks_count
            );

        }
    }
}
