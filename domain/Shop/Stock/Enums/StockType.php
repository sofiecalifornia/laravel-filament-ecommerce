<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum StockType: string implements HasColor, HasIcon, HasLabel
{
    case unlimited = 'unlimited';
    case base_on_stock = 'base_on_stock';
    case unavailable = 'unavailable';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::unlimited => 'success',
            self::base_on_stock => 'warning',
            self::unavailable => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::unlimited => 'heroicon-o-check-circle',
            self::base_on_stock => 'heroicon-o-clock',
            self::unavailable => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
