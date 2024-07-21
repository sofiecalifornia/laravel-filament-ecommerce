<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\BranchResource\Pages;

use App\Filament\Resources\Shop\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
