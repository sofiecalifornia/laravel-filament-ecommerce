<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case pending = 'pending';
    case preparing = 'preparing';
    case in_queue = 'in_queue';
    case ready = 'ready';
    case dispatched = 'dispatched';
    case completed = 'completed';
    case canceled = 'canceled';
    case failed = 'failed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::completed => 'success',
            self::pending, self::ready, self::preparing,
            self::in_queue , self::dispatched => 'warning',
            self::canceled, self::failed => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::completed => 'heroicon-o-check-circle',
            self::pending, self::canceled, self::failed => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    }
}
