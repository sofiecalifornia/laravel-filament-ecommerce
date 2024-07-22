<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Type: string implements HasLabel
{
    case online = 'online';
    case in_store = 'in_store';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
