<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Observers;

use Domain\Shop\Customer\Actions\GenerateCustomerReferenceNumberAction;
use Domain\Shop\Customer\Models\Customer;
use Filament\Facades\Filament;

class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        if (Filament::auth()->check()) {
            $customer->admin()->associate(Filament::auth()->id());
        }

        $customer->reference_number = app(GenerateCustomerReferenceNumberAction::class)
            ->execute();
    }

    public function deleting(Customer $customer): void
    {
        if ($customer->orders()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete customer with associated orders.'));
        }
    }
}
