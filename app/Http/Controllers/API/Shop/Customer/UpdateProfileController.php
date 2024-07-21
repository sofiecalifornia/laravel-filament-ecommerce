<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Customer;

use App\Http\Requests\API\Shop\Customer\UpdateProfileRequest;
use App\Http\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Actions\EditCustomerRegisterAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Throwable;

/**
 * @tags Customer
 */
#[Prefix('customers'), Middleware('auth:sanctum')]
class UpdateProfileController
{
    /**
     * @operationId update profile
     *
     * @throws Throwable
     */
    #[Put('profile', name: 'customers.profile.update')]
    public function __invoke(UpdateProfileRequest $request): CustomerResource
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        DB::transaction(fn () => app(EditCustomerRegisterAction::class)
            ->execute($customer, $request->toDTO()));

        return CustomerResource::make($customer->refresh());
    }
}
