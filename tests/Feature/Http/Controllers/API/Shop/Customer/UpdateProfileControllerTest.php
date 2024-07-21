<?php

declare(strict_types=1);

use Domain\Shop\Customer\Models\Address;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\CustomerProfileUpdateRequestFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

it('update profile', function () {

    $data = CustomerProfileUpdateRequestFactory::new()
        ->create();

    $customer = loginAsCustomer();

    putJson('api/customers/profile', $data)
        ->assertValid()
        ->assertOk()
        ->assertJson(function (AssertableJson $json) use ($customer) {
            $json
                ->where('data.type', 'customers')
                ->where('data.id', $customer->reference_number)
                ->where('data.attributes.reference_number', $customer->reference_number)
                ->where('data.attributes.email', $customer->email)
                ->where('data.attributes.first_name', $customer->first_name)
                ->where('data.attributes.last_name', $customer->last_name)
                ->where('data.attributes.mobile', $customer->mobile)
                ->where('data.attributes.gender', $customer->gender->value)
                ->where('data.attributes.status', $customer->status->value)
                ->etc();
        });

    assertDatabaseHas(Customer::class, [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'mobile' => $data['mobile'],
        'gender' => $data['gender'],
        'status' => $customer->status,
    ]);

    //    assertDatabaseHas(Address::class, [
    //        'customer_id' => $customer->getKey(),
    //    ]);
});
