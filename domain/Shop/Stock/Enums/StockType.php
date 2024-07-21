<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum StockType: string implements HasColor, HasIcon, HasLabel
{
    case UNLIMITED = 'unlimited';
    case BASE_ON_STOCK = 'base-on-stock';
    case UNAVAILABLE = 'unavailable';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNLIMITED => 'success',
            self::BASE_ON_STOCK => 'warning',
            self::UNAVAILABLE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UNLIMITED => 'heroicon-o-check-circle',
            self::BASE_ON_STOCK => 'heroicon-o-clock',
            self::UNAVAILABLE => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
