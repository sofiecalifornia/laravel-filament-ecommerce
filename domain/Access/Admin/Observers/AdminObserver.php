<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Observers;

use Domain\Access\Admin\Models\Admin;

class AdminObserver
{
    public function deleting(Admin $admin): void
    {
        if ($admin->orders()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete admin with associated orders.'));
        }
    }
}
