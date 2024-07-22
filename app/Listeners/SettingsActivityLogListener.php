<?php

declare(strict_types=1);

namespace App\Listeners;

use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\LaravelSettings\Events\SavingSettings;

class SettingsActivityLogListener
{
    public function handle(SavingSettings $event): void
    {
        $old = $event->originalValues;

        if (null === $old) {
            return;
        }

        $new = $event->properties;

        $implodeArray = function (mixed $value) {
            if (is_array($value)) {
                sort($value);

                return implode(', ', $value);
            }

            return $value;
        };

        $old = $old->map($implodeArray);
        $new = $new->map($implodeArray);

        $attributeChanges = $old
            ->diff($new)
            ->keys()
            ->toArray();

        if (blank($attributeChanges)) {
            return;
        }

        activity()
            ->event('settings updated')
            ->causedBy(Filament::auth()->user())
            ->withProperties(
                [
                    'old' => Arr::only($old->toArray(), $attributeChanges),
                    'attributes' => Arr::only($new->toArray(), $attributeChanges),
                ]
            )
            ->log(Str::headline($event->settings::group()).' Settings Updated.');
    }
}
