<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Day: string implements HasLabel
{
    case Sunday = 'Sunday';
    case Monday = 'Monday';
    case Tuesday = 'Tuesday';
    case Wednesday = 'Wednesday';
    case Thursday = 'Thursday';
    case Friday = 'Friday';
    case Saturday = 'Saturday';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
