<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case enabled = 'enabled';
    case disabled = 'disabled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::enabled => 'success',
            self::disabled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::enabled => 'heroicon-o-check-circle',
            self::disabled => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
