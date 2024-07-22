<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AttributeFieldType: string implements HasLabel
{
    case text = 'text';
    case color_picker = 'color_picker';
    case numeric = 'numeric';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
