<?php

declare(strict_types=1);

use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;

it('generate token', function () {
    $password = fake()->password();
    $customer = CustomerFactory::new()
        ->active()
        ->createOne([
            'password' => $password,
        ]);

    assertCount(0, $customer->tokens);

    postJson('api/customers/login', [
        'email' => $customer->email,
        'password' => $password,
    ])
        ->assertValid()
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('token')
                ->whereType('token', 'string');
        });

    assertCount(1, $customer->refresh()->tokens);
});

it('can not generate token w/ invalid credentials', function () {
    $password = fake()->password();
    $customer = CustomerFactory::new()
        ->active()
        ->createOne([
            'password' => $password,
        ]);

    assertCount(0, $customer->tokens);

    postJson('api/customers/login', [
        'email' => $customer->email,
        'password' => $password.'-now-is-wrong',
    ])
        ->assertValid()
        ->assertUnauthorized();

    assertCount(0, $customer->refresh()->tokens);
});

it('can access protected route with valid token', function () {
    $customer = CustomerFactory::new()
        ->active()
        ->createOne();

    $token = $customer
        ->createToken(
            name: 'testing-auth',
        )
        ->plainTextToken;

    Route::get('api/test-private-route', fn () => 'access granted!')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route', [
        'Authorization' => 'Bearer '.$token,
    ])
        ->assertOk()
        ->assertSee('access granted!');
});

it('can access protected route with invalid token', function (?string $token) {
    Route::get('api/test-private-route', fn () => '')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route', [
        'Authorization' => $token,
    ])
        ->assertUnauthorized();
})
    ->with([
        null,
        'Bearer invalid',
        '',
    ]);

it('can not access protected route w/out header authorization', function () {
    Route::get('api/test-private-route', fn () => '')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route')
        ->assertUnauthorized();
});
