<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\ActivityResource\Pages;

use App\Filament\Admin\Resources\Access\ActivityResource;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;
}
