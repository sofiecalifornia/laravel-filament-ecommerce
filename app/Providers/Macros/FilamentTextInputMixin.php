<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Filament\Forms;
use Illuminate\Support\Str;

/**
 * @mixin \Filament\Forms\Components\TextInput
 */
class FilamentTextInputMixin
{
    public function money(): \Closure
    {
        return fn (): Forms\Components\TextInput => $this->formatStateUsing(
            fn (?array $state): ?float => $state['value'] ?? null
        )
            ->prefix(Str::upper(config('money.defaults.currency')));
    }
}
