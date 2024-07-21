<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Actions;

use Domain\Shop\Customer\DataTransferObjects\CustomerData;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Auth\Events\Registered;

final readonly class RegisterCustomerAction
{
    public function execute(CustomerData $registerCustomerDTO): Customer
    {
        $customer = Customer::create([
            'email' => $registerCustomerDTO->email,
            'password' => $registerCustomerDTO->password,
            'first_name' => $registerCustomerDTO->first_name,
            'last_name' => $registerCustomerDTO->last_name,
            'mobile' => $registerCustomerDTO->mobile,
            'gender' => $registerCustomerDTO->gender,
        ]);

        event(new Registered($customer));

        return $customer;
    }
}
