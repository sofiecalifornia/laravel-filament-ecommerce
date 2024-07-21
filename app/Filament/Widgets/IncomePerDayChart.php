<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class IncomePerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 6;

    public ?Carbon $fromDate = null;

    public ?Carbon $toDate = null;

    public function getHeading(): ?string
    {
        return trans('Income Per Day');
    }

    protected function getData(): array
    {
        $data = Trend::query(
            Order::query()
                ->where('payment_status', PaymentStatus::PAID)
                ->where('status', Status::COMPLETED)
        )
            ->between(
                start: $this->fromDate ?? now()->subMonth(),
                end: $this->toDate ?? now(),
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
