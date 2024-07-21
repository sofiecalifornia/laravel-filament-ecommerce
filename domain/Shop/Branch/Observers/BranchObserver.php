<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Observers;

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;

class BranchObserver
{
    public function created(Branch $branch): void
    {
        Admin::role(config('domain.access.role.super_admin'), guard: 'admin')
            ->get()
            ->each(fn (Admin $admin) => $admin->branches()->attach($branch));
    }

    public function deleting(Branch $branch): void
    {
        if ($branch->orders()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete branch with associated orders.'));
        }
        if ($branch->skuStocks()->count() > 0) {
            abort(403, trans('Can not delete branch with associated stocks.'));
        }
    }
}
