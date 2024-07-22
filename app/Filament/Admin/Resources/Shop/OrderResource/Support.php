<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\OrderResource;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Actions\GetOpeningHoursByBranchAction;
use Domain\Shop\Order\Actions\CalculateOrderTotalPriceAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Enums\ClaimType;
use Spatie\OpeningHours\OpeningHours;

class Support
{
    private function __construct()
    {
    }

    public static function callCalculatorForTotalPrice(array $orderItems): float
    {
        return app(CalculateOrderTotalPriceAction::class)
            ->execute(
                collect($orderItems)
                    ->reject(fn ($data): bool => blank($data['sku_uuid']))
                    ->map(
                        fn (array $data): ItemWithMinMaxData => new ItemWithMinMaxData(
                            price: money($data['price'] * 100),
                            quantity: (float) $data['quantity'],
                            minimum: $data['minimum'],
                            maximum: $data['maximum']
                        )
                    )
                    ->toArray()
            )->getValue();
    }

    public static function openingHours(Branch $branch, ClaimType $claimType): OpeningHours
    {
        match ($claimType) {
            ClaimType::delivery => $branch->load('operationHoursOnline'),
            ClaimType::pickup => $branch->load('operationHoursInStore'),
        };

        return app(GetOpeningHoursByBranchAction::class)
            ->execute($branch, $claimType->operationHourType());
    }
}
