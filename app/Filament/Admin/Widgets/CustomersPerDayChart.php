<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Customer\Models\Customer;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomersPerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use InteractsWithPageFilters;
    use PermissionWidgets;

    protected static ?int $sort = 5;

    #[\Override]
    public function getHeading(): ?string
    {
        return trans('Customers Per Day');
    }

    #[\Override]
    protected function getData(): array
    {
        $customer = Trend::model(Customer::class)
            ->between(
                start: $this->getDateFilter('start_date') ?? now()->subMonth(),
                end: $this->getDateFilter('end_date') ?? now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => trans('Customers Per Day'),
                    'data' => $customer->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $customer->map(fn (TrendValue $value) => $value->date),
        ];
    }

    #[\Override]
    protected function getType(): string
    {
        return 'bar';
    }
}
