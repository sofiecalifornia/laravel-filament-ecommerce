<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\RoleResource\Pages;

use App\Filament\Admin\Resources\Access\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
