<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case IN_STOCK = 'in-stock';
    case SOLD_OUT = 'sold-out';
    case COMING_SOON = 'coming-soon';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::IN_STOCK => 'success',
            self::SOLD_OUT => 'danger',
            self::COMING_SOON => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::IN_STOCK => 'heroicon-o-check-circle',
            self::SOLD_OUT => 'heroicon-o-x-circle',
            self::COMING_SOON => 'heroicon-o-clock',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
