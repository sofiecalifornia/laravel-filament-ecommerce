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
use Illuminate\Contracts\Support\Htmlable;

class OrdersPerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use InteractsWithPageFilters;
    use PermissionWidgets;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    #[\Override]
    public function getHeading(): string|Htmlable|null
    {
        return trans('Orders per day');
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
                start: $this->getDateFilter('start_date') ?? now()->subDays(60),
                end: $this->getDateFilter('end_date') ?? now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => trans('Orders per day'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
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
