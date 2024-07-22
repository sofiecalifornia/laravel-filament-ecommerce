<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case active = 'active';
    case inactive = 'inactive';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::active => 'success',
            self::inactive => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::active => 'heroicon-o-check-circle',
            self::inactive => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
