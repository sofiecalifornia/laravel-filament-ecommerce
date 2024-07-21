<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Customer;

use App\Http\Resources\Shop\CustomerResource;
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * @tags Customer
 */
#[Prefix('customers'), Middleware('auth:sanctum')]
class ProfileController
{
    /** @operationId profile */
    #[Get('profile', name: 'customers.profile')]
    public function __invoke(): CustomerResource
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        return CustomerResource::make($customer);
    }
}
