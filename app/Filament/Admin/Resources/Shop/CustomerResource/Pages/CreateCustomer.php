<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CustomerResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Models\Customer;
use Filament\Resources\Pages\CreateRecord;

/**
 * @property-read Customer $record
 */
class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    public function afterCreate(): void
    {
        // TODO email verification
    }
}
