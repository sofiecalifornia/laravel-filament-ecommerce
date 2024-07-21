<?php

declare(strict_types=1);

use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;

it('fetch current customer account', function () {
    $customer = loginAsCustomer();

    $response = getJson('api/customers/profile');

    $response->assertOk()
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
});

it('fetch w/ media', function () {
    loginAsCustomer();

    $response = getJson('api/customers/profile?include=media');

    $response->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json
                ->count('included', 1)
                ->where('included.0.type', 'media')
                ->etc();
        });
});
