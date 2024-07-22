<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CustomerResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
