<?php

declare(strict_types=1);

namespace App\Providers\Filament\Versions;

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;

class AppVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'App';
    }

    public function getVersion(): string
    {
        return config('app.version');
    }
}
