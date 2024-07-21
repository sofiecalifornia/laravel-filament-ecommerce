<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Cart\Database\Factories\CartFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Order\Models\Order;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\OrderRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('can submit order', function () {

    $branch = BranchFactory::new()
        ->enabled()
        ->createOne();

    $product = createProduct($branch, 5.1);

    $customer = CustomerFactory::new()
        ->active()
        ->createOne();

    CartFactory::new()
        ->withQuantity(5.1)
        ->withSku($product->skus[0])
        ->withBranch($branch)
        ->withCustomer($customer)
        ->createOne();

    $data = OrderRequestFactory::new()
        ->create();

    loginAsCustomer($customer);

    assertDatabaseEmpty(Order::class);

    postJson('api/orders/'.$branch->getRouteKey().'?include=orderItems.sku', $data)
        ->assertValid()
        ->assertCreated();
    //        ->assertJson(function (AssertableJson $json) use ($customer) {
    //            $json
    //                ->where('data.type', 'customers')
    //                ->where('data.id', $customer->reference_number)
    //                ->where('data.attributes.reference_number', $customer->reference_number)
    //                ->where('data.attributes.email', $customer->email)
    //                ->where('data.attributes.first_name', $customer->first_name)
    //                ->where('data.attributes.last_name', $customer->last_name)
    //                ->where('data.attributes.mobile', $customer->mobile)
    //                ->where('data.attributes.gender', $customer->gender->value)
    //                ->where('data.attributes.status', $customer->status->value)
    //                ->etc();
    //        });

    //    assertDatabaseCount(Order::class, 1);
    //
    //    assertDatabaseHas(Order::class, [
    //        'first_name' => $data['first_name'],
    //        'last_name' => $data['last_name'],
    //        'email' => $data['email'],
    //        'mobile' => $data['mobile'],
    //        'gender' => $data['gender'],
    //        'status' => $customer->status,
    //    ]);

});
