<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\OrderResource\Widgets;

use App\Filament\Resources\Shop\OrderResource\Pages\ListOrders;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalOrders extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListOrders::class;
    }

    protected function getStats(): array
    {

        return [
            Stat::make(
                trans('Pending orders'),
                /** @phpstan-ignore-next-line  */
                $this->getPageTableQuery()
                    ->whereStatus(Status::PENDING)
                    ->count()
            ),
            Stat::make(
                trans('Paid orders'),
                /** @phpstan-ignore-next-line  */
                $this->getPageTableQuery()
                    ->wherePaymentStatus(PaymentStatus::PAID)
                    ->count()
            ),
        ];
    }
}
