<?php

declare(strict_types=1);

use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Tests\RequestFactories\RegisterRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('register', function () {

    $data = RegisterRequestFactory::new()->create();

    assertDatabaseEmpty(Customer::class);

    Event::fake(Registered::class);

    postJson('api/customers/register', $data)
        ->assertValid()
        ->assertCreated();

    Event::assertDispatched(Registered::class);

    unset($data['password'], $data['password_confirmation']);

    assertDatabaseCount(Customer::class, 1);
    assertDatabaseHas(Customer::class, $data);
});

it('can not use existing email', function () {

    $customer = CustomerFactory::new()->createOne();

    $data = RegisterRequestFactory::new()
        ->create(['email' => $customer->email]);

    postJson('api/customers/register', $data)
        ->assertInvalid(['email' => 'The email has already been taken.'])
        ->assertUnprocessable();
});
