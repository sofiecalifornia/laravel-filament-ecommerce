<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class IncomePerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use InteractsWithPageFilters;
    use PermissionWidgets;

    protected static ?int $sort = 6;

    #[\Override]
    public function getHeading(): ?string
    {
        return trans('Income Per Day');
    }

    #[\Override]
    protected function getData(): array
    {
        $data = Trend::query(
            Order::query()
                ->where('payment_status', PaymentStatus::paid)
                ->where('status', Status::completed)
        )
            ->between(
                start: $this->getDateFilter('start_date') ?? now()->subMonth(),
                end: $this->getDateFilter('end_date') ?? now(),
            )
            ->perDay()
            ->sum('total_price');

        return [
            'datasets' => [
                [
                    'label' => trans('Income Per Day'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate / 100),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    #[\Override]
    protected function getType(): string
    {
        return 'bar';
    }
}
