<?php

declare(strict_types=1);

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;

abstract class BaseSettings extends Settings
{
    protected function getUrlFromStorage(string $property): string
    {
        return Storage::disk(config('filament.default_filesystem_disk'))
            ->url($property);
    }
}
