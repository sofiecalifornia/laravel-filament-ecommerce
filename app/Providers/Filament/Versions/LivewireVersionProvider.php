<?php

declare(strict_types=1);

namespace App\Providers\Filament\Versions;

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;
use Composer\InstalledVersions;

class LivewireVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'Livewire';
    }

    public function getVersion(): string
    {
        return (string) InstalledVersions::getPrettyVersion('livewire/livewire');
    }
}
