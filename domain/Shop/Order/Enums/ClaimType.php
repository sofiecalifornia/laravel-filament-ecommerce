<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Domain\Shop\OperationHour\Enums\Type;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ClaimType: string implements HasColor, HasIcon, HasLabel
{
    case delivery = 'delivery';
    case pickup = 'pickup';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::delivery => 'success',
            self::pickup => 'warning',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::delivery => 'heroicon-o-truck',
            self::pickup => 'heroicon-o-shopping-bag',
        };
    }

    public function operationHourType(): Type
    {
        return match ($this) {
            self::delivery => Type::online,
            self::pickup => Type::in_store,
        };
    }
}
