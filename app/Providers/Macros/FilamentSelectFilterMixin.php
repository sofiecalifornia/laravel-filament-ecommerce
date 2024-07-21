<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Filament\Support\Contracts\HasLabel;
use Filament\Tables;
use StringBackedEnum;

class FilamentSelectFilterMixin
{
    public function optionsFromEnum(): Closure
    {
        return function (string $enum): Tables\Filters\SelectFilter {

            /** @var Tables\Filters\SelectFilter $component */
            $component = $this;

            if (! enum_exists($enum)) {
                abort(500, Tables\Filters\SelectFilter::class.'::optionsFromEnum() parameter must be enum.');
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
            );
        };
    }
}
