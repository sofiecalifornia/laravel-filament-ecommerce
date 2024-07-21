<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Customer;

use App\Http\Requests\API\Shop\Customer\RegisterRequest;
use App\Http\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Actions\RegisterCustomerAction;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * @tags Customer
 */
#[Prefix('customers')]
class RegisterController
{
    /** @operationId registration */
    #[Post('register', name: 'customers.register')]
    public function __invoke(RegisterRequest $request): CustomerResource
    {
        $customer = app(RegisterCustomerAction::class)
            ->execute($request->toDTO());

        return CustomerResource::make($customer);
    }
}
