<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Cart\Database\Factories\CartFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
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
    assertDatabaseEmpty(OrderItem::class);

    postJson('api/branches/'.$branch->getRouteKey().'/orders?include=orderItems.sku', $data)
        ->assertValid()
        ->assertCreated()
        ->assertJson(function (AssertableJson $json) use ($customer) {
            $order = Order::first();
            $json
                ->where('data.type', 'orders')
                ->where('data.id', $order->uuid)
                ->where('data.attributes.receipt_number', $order->receipt_number)
                ->where('data.attributes.payment_status', $order->payment_status->getLabel())
                ->where('data.attributes.payment_method', $order->payment_method?->getLabel())
                ->where('data.attributes.status', $order->status->getLabel())
                ->where('data.attributes.total_price', moneyJsonApi($order->total_price))
                ->where(
                    'data.attributes.claim_at',
                    $order->claim_at
                        ->timezone($customer->timezone)->format('Y-m-d h:i A')
                )
                ->where(
                    'data.attributes.created_at',
                    $order->created_at
                        ->timezone($customer->timezone)->format('Y-m-d h:i A')
                )
                ->etc();
        });

    assertDatabaseCount(Order::class, 1);
    assertDatabaseCount(OrderItem::class, 1);

    assertDatabaseHas(Order::class, [
        'payment_method' => $data['payment_method'],
        'notes' => $data['notes'],
        'claim_at' => now()->parse($data['claim_at'])->timezone($customer->timezone),
        'claim_type' => $data['claim_type'],
    ]);
    assertDatabaseHas(OrderItem::class, [
        'order_uuid' => Order::value('uuid'),
        // TODO: assert order item
    ]);

});
