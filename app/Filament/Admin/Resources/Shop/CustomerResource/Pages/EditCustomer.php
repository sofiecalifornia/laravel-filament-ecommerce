<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CustomerResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
            Actions\RestoreAction::make()
                ->translateLabel(),
            Actions\ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }
}
