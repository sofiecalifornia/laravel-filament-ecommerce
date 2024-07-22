<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatusStatsOverview extends StatsOverviewWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 3;

    #[\Override]
    protected function getStats(): array
    {
        return [
            Stat::make(
                trans('Pending orders'),
                Order::whereStatus(Status::pending)
                    ->count()
            ),
            Stat::make(
                trans('Paid orders this month'),
                Order::wherePaymentStatus(PaymentStatus::paid)
                    ->where('created_at', '>', now()->subMonth())
                    ->count()
            ),

        ];
    }
}
