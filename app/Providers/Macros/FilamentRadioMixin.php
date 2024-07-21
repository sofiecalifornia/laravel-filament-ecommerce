<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Filament\Forms;
use Filament\Support\Contracts\HasLabel;
use StringBackedEnum;

class FilamentRadioMixin
{
    public function optionsFromEnum(): Closure
    {
        return function (string $enum): Forms\Components\Radio {

            /** @var Forms\Components\Radio $component */
            $component = $this;

            if (! enum_exists($enum)) {
                abort(500, Forms\Components\Radio::class.'::optionsFromEnum() parameter must be enum.');
            }

            /** @var class-string<\Filament\Support\Contracts\HasLabel&StringBackedEnum> $enum */

            /** @var \Illuminate\Support\Collection<int, \Filament\Support\Contracts\HasLabel&StringBackedEnum> $cases */
            /** @phpstan-ignore-next-line  */
            $cases = collect($enum::cases());

            return $component->options(
                fn () => $cases->mapWithKeys(
                    /** @var \Filament\Support\Contracts\HasLabel&StringBackedEnum $case */
                    fn (HasLabel $case) => [
                        /** @phpstan-ignore-next-line  */
                        $case->value => $case->getLabel(),
                    ]
                )
            )
                ->enum($enum);
        };
    }
}
