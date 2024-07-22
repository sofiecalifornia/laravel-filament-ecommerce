<?php

declare(strict_types=1);

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Configurations');
    }
}
