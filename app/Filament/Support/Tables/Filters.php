<?php

declare(strict_types=1);

namespace App\Filament\Support\Tables;

use Closure;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Filters
{
    /** @throws Exception */
    public static function fromTo(string $field): Filter
    {
        return Filter::make($field)
            ->form([
                DatePicker::make($field.'_from')
                    ->placeholder(fn ($state): string => now()->subYear()->format('M d, Y')),
                DatePicker::make($field.'_until')
                    ->placeholder(fn ($state): string => now()->format('M d, Y')),
            ])
            ->query(self::fromToQuery($field))
            ->indicateUsing(function (array $data) use ($field): array {
                $indicators = [];
                if ($data[$field.'_from'] ?? null) {
                    $indicators[$field.'_from'] = 'Order from '.
                        now()
                            ->parse($data[$field.'_from'])
                            ->toFormattedDateString();
                }
                if ($data[$field.'_until'] ?? null) {
                    $indicators[$field.'_until'] = 'Order until '.
                        now()
                            ->parse($data[$field.'_until'])
                            ->toFormattedDateString();
                }

                return $indicators;
            });
    }

    private static function fromToQuery(string $field): Closure
    {
        $statement = sprintf(
            'DATE(CONVERT_TZ(%s, \'%s\', \'%s\'))',
            $field,
            config('app.timezone'),
            Filament::auth()->user()?->timezone
        );

        return fn (Builder $query, array $data) => $query
            ->when(
                $data[$field.'_from'],
                fn (Builder $query) => $query->whereRaw(
                    $statement.' >= ?',
                    [Str::of($data[$field.'_from'])->replace(' 00:00:00', '')]
                )
            )
            ->when(
                $data[$field.'_until'],
                fn (Builder $query) => $query->whereRaw(
                    $statement.' <= ?',
                    [Str::of($data[$field.'_until'])->replace(' 00:00:00', '')]
                )
            );
    }
}
