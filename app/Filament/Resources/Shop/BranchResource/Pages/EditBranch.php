<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\BranchResource\Pages;

use App\Filament\Resources\Shop\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
        ];
    }
}
