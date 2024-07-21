<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PaymentStatus: string implements HasColor, HasLabel
{
    case CANCELED = 'canceled';
    case FAILED = 'failed';
    case PAID = 'paid';
    case PENDING = 'pending';
    case UNPAID = 'unpaid';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PAID => 'success',
            self::PENDING => 'warning',
            self::CANCELED, self::FAILED, self::UNPAID => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
