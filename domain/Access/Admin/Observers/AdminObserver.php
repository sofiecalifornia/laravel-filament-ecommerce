<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Observers;

use App\Observers\LogAttemptDeleteResource;
use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;

class AdminObserver
{
    use LogAttemptDeleteResource;

    public function __construct(private readonly OrderSettings $orderSettings)
    {
    }

    /**
     * @throws Halt
     */
    public function deleting(Admin $admin): void
    {
        $admin->loadCount([
            'orders' => function (Builder $builder) {
                /** @var \Domain\Shop\Order\Models\Order|\Illuminate\Database\Eloquent\Builder $builder */
                $builder->withTrashed();
            },
        ]);

        if ($admin->orders_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $admin,
                trans('Can not delete admin with associated orders.'),
                'orders',
                $admin->orders_count
            );

        }

        if (in_array($admin->getKey(), $this->orderSettings->admin_notification_ids)) {

            self::abortThenLogAttemptDelete(
                $admin,
                trans('Can not delete admin with associated orders settings.'),
                [
                    'admin_notification_ids' => implode(', ', $this->orderSettings->admin_notification_ids),
                ]
            );

        }
    }
}
