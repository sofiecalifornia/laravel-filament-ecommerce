<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalRevenueStats extends BaseWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $revenueToday = Order::whereDate('created_at', date('Y-m-d'))
            ->where('payment_status', PaymentStatus::PAID)
            ->where('status', Status::COMPLETED)
            ->sum('total_price');

        $revenue7Days = Order::where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->where('payment_status', PaymentStatus::PAID)
            ->where('status', Status::COMPLETED)
            ->sum('total_price');

        $revenue30Days = Order::where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->where('payment_status', PaymentStatus::PAID)
            ->where('status', Status::COMPLETED)
            ->sum('total_price');

        return [
            Stat::make(trans('Revenue Today'), money($revenueToday)->format()),
            Stat::make(trans('Revenue Last 7 Days'), money($revenue7Days)->format()),
            Stat::make(trans('Revenue Last 30 Days'), money($revenue30Days)->format()),
        ];
    }
}
