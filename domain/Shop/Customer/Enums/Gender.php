<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Gender: string implements HasLabel
{
    case FEMALE = 'female';
    case MALE = 'male';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
