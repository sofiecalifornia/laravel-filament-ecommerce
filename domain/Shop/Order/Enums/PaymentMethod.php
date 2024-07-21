<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PaymentMethod: string implements HasLabel
{
    case CASH = 'cash';
    case GCASH = 'g-cash';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
