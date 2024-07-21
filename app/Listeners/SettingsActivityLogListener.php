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
        if ($event->originalValues === null) {
            return;
        }

        $attributeChanges = $event->originalValues
            ->diff($event->properties)
            ->keys()
            ->toArray();

        if (blank($attributeChanges)) {
            return;
        }

        activity()
            ->inLog('setting: '.$event->settings::group())
            ->causedBy(Filament::auth()->user())
            ->withProperties(
                [
                    'old' => Arr::only($event->originalValues->toArray(), $attributeChanges),
                    'attributes' => Arr::only($event->properties->toArray(), $attributeChanges),
                ]
            )
            ->log(Str::headline($event->settings::group()).' Settings Updated.');
    }
}
