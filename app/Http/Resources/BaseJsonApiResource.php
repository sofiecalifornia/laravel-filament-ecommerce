<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Akaunting\Money\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use TiMacDonald\JsonApi\JsonApiResource;

abstract class BaseJsonApiResource extends JsonApiResource
{
    protected static function datetimeFormat(?Carbon $datetime): ?string
    {
        return $datetime
            ?->timezone(
                Auth::user()?->timezone ?? config('app-default.timezone')
            )
            ->format('Y-m-d h:i A');
    }

    protected static function money(Money $money): array
    {
        return [
            'amount' => $money->getAmount(),
            'currency' => $money->getCurrency()->getCurrency(),
            'formatted' => $money->format(),
        ];
    }
}
