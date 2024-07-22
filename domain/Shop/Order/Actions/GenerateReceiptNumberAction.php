<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use App\Settings\OrderSettings;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Str;

readonly class GenerateReceiptNumberAction
{
    public function __construct(
        private OrderSettings $orderSettings,
    ) {
    }

    public function execute(Branch $branch): string
    {
        $prefix = sprintf(
            '%s%s%s',
            $this->orderSettings->prefix,
            $branch->code,
            now()->format('ymd')
        );

        $latestReceiptNumber = Order::withTrashed()
            ->where(
                'receipt_number',
                'like',
                $prefix.'%'
            )
            ->latest()
            ->value('receipt_number');

        if (null === $latestReceiptNumber) {
            return $prefix.'0001';
        }

        $incrementNumber = (string) Str::of($latestReceiptNumber)
            ->substr(Str::length($prefix));

        $incrementNumberPlusOne = ((int) $incrementNumber) + 1;

        return $prefix.Str::of((string) $incrementNumberPlusOne)
            ->padLeft(4, '0');
    }
}
