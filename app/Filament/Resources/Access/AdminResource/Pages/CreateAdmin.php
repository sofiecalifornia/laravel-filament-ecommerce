<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\AdminResource\Pages;

use App\Filament\Resources\Access\AdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
}
