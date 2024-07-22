<?php

declare(strict_types=1);

namespace Database\Support;

use Illuminate\Support\Facades\Storage;

trait SettingMigrationSupport
{
    public function upload(string $fileName, string $path): string
    {
        Storage::disk(config('filament.default_filesystem_disk'))
            ->put($fileName, (string) file_get_contents($path));

        return $fileName;
    }
}
