<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Customer\Models\Customer;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class CustomersPerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 5;

    public ?Carbon $fromDate = null;

    public ?Carbon $toDate = null;

    public function getHeading(): ?string
    {
        return trans('Customers Per Day');
    }

    protected function getData(): array
    {

        $customer = Trend::model(Customer::class)
            ->between(
                start: $this->fromDate ?? now()->subMonth(),
                end: $this->toDate ?? now(),
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

    protected function getType(): string
    {
        return 'bar';
    }

    #[On('updateFromDate')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::make($from);
        $this->updateChartData();
    }

    #[On('updateToDate')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::make($to);
        $this->updateChartData();
    }
}
