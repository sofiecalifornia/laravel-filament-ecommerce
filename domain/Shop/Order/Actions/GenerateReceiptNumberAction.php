<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use App\Settings\OrderSettings;
use App\Settings\SiteSettings;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Str;

final readonly class GenerateReceiptNumberAction
{
    public function __construct(
        private OrderSettings $orderSettings,
    ) {
    }

    public function execute(int $increase = null): string
    {
        $prefix = $this->orderSettings->prefix;

        if ($increase !== null && $increase < 1) {
            abort(500, 'invalid value: '.$increase);
        }

        $prefix = (string) Str::of($prefix)->replace(' ', '_');

        $dateTime = now(/*$this->siteSettings->timezone*/);

        $y = $dateTime->format('y');
        $m = $dateTime->format('m');
        $d = $dateTime->format('d');

        $format = sprintf('%s%s%s%s', $prefix, $y, $m, $d);

        /** @var Order $latestModel */
        $latestModel = Order::withTrashed()
            ->where(
                'receipt_number',
                'like',
                $format.'%'
            )
            ->latest()
            ->first();

        if (blank($latestModel)) {
            if ($increase === null) {
                $output = $format.'0001';
            } else {
                $output = $format.'000'.($increase + 1);
            }
        } else {
            $dateLength = Str::length($y) + Str::length($m) + Str::length($d);

            $subStr = (string) Str::of($latestModel->receipt_number)
                ->substr(Str::length($prefix) + $dateLength);

            $number = ((int) $subStr) + 1;

            if ($increase !== null) {
                $number += $increase;
            }
            $output = $format.Str::of((string) $number)->padLeft(4, '0');
        }

        return $output;
    }
}
