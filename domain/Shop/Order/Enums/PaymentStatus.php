<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case canceled = 'canceled';
    case failed = 'failed';
    case paid = 'paid';
    case pending = 'pending';
    case unpaid = 'unpaid';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::paid => 'success',
            self::pending => 'warning',
            self::canceled, self::failed, self::unpaid => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::paid => 'heroicon-o-check-circle',
            self::pending => 'heroicon-o-clock',
            self::canceled, self::failed, self::unpaid => 'heroicon-o-x-circle',
        };
    }
}
