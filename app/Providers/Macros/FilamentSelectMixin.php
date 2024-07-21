<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Filament\Forms;
use Filament\Support\Contracts\HasLabel;
use StringBackedEnum;

class FilamentSelectMixin
{
    public function optionsFromEnum(): Closure
    {
        return function (string $enum): Forms\Components\Select {

            /** @var Forms\Components\Select $component */
            $component = $this;

            if (! enum_exists($enum)) {
                abort(500, Forms\Components\Select::class.'::optionsFromEnum() parameter must be enum.');
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
