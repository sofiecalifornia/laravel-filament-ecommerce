<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ENABLED => 'success',
            self::DISABLED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ENABLED => 'heroicon-o-check-circle',
            self::DISABLED => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
