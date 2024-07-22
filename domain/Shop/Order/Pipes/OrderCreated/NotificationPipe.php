<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Pipes\OrderCreated;

use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Order\Notifications\AdminOrderPlacedNotification;
use Domain\Shop\Order\Notifications\CustomerOrderPlacedNotification;

readonly class NotificationPipe
{
    public function __construct(private readonly OrderSettings $orderSettings)
    {
    }

    public function handle(OrderPipelineData $orderPipelineData, callable $next): OrderPipelineData
    {
        $orderPipelineData->order
            ->customer
            ->notify(new CustomerOrderPlacedNotification($orderPipelineData->order));

        $admins = $orderPipelineData->order
            ->branch
            ->adminNotifications;

        if ($admins->isEmpty()) {
            $admins = $this->orderSettings
                ->getAdminNotifications();
        }

        $admins->each(
            fn (Admin $admin) => $admin
                ->notify(new AdminOrderPlacedNotification($orderPipelineData->order))
        );

        return $next($orderPipelineData);
    }
}
