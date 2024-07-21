<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Customer;

use App\Http\Requests\API\Shop\Customer\LoginRequest;
use Domain\Shop\Customer\Enums\Status;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * @tags Customer
 */
#[Prefix('customers')]
class LoginController
{
    /**
     * @operationId generate access token
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    #[Post(uri: 'login', name: 'customers.login')]
    public function __invoke(LoginRequest $request): mixed
    {
        if (! Auth::guard('api')->attempt($request->validated())) {
            throw new AuthenticationException(trans('Invalid credentials.'));
        }

        $customer = Customer::whereEmail($request->validated('email'))
            ->whereStatus(Status::ACTIVE)
            ->first();

        if ($customer === null) {
            throw new AuthenticationException(trans('Invalid credentials.'));
        }

        return response([
            'token' => $customer
                ->createToken('customer')
                ->plainTextToken,
        ]);
    }
}
