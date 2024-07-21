<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Actions;

use Domain\Shop\Customer\DataTransferObjects\CustomerData;
use Domain\Shop\Customer\Models\Customer;

final readonly class EditCustomerRegisterAction
{
    public function execute(Customer $customer, CustomerData $registerCustomerDTO): Customer
    {
        $customer->update([
            'email' => $registerCustomerDTO->email,
            'first_name' => $registerCustomerDTO->first_name,
            'last_name' => $registerCustomerDTO->last_name,
            'mobile' => $registerCustomerDTO->mobile,
            'gender' => $registerCustomerDTO->gender,
        ]);

        return $customer;
    }
}
