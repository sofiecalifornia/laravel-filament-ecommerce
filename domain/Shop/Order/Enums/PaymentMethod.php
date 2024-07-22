<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case cash = 'cash';
    case g_cash = 'g_cash';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::cash => 'success',
            self::g_cash => 'warning',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::cash => 'heroicon-o-banknotes',
            self::g_cash => 'heroicon-o-credit-card',
        };
    }
}
