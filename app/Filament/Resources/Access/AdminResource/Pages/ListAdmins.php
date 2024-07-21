<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\AdminResource\Pages;

use App\Filament\Resources\Access\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
