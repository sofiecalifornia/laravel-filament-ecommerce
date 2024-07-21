<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Cart\Database\Factories\CartFactory;
use Domain\Shop\Cart\Models\Cart;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\CartRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->branch = Branchfactory::new()->enabled()->createOne();
    $this->customer = loginAsCustomer();
});

it('get list', function () {
    $product = createProduct($this->branch, 2);

    /** @var \Domain\Shop\Product\Models\Sku $sku */
    $sku = $product->skus[0];

    /** @var \Domain\Shop\Cart\Models\Cart $cart */
    $cart = CartFactory::new()
        ->withQuantity(2)
        ->withSku($sku)
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    getJson('api/carts/'.$this->branch->getRouteKey().'?include=sku.product')
        ->assertOk()
        ->assertJson(function (AssertableJson $json) use ($cart) {
            $json
                ->count('data', 1)
                ->where('data.0.type', 'carts')
                ->where('data.0.id', (string) $cart->getRouteKey())
                ->where('data.0.attributes.product_name', $cart->product_name)
                ->where('data.0.attributes.sku_code', $cart->sku_code)
                ->where('data.0.attributes.price', (float) $cart->price)
                ->where('data.0.attributes.quantity', 2)
                ->etc();
        });

});

it('get store', function () {

    $product = createProduct($this->branch, 2);

    $data = CartRequestFactory::new()
        ->withSku($product->skus[0])
        ->withQuantity(2)
        ->create();

    postJson('api/carts/'.$this->branch->getRouteKey().'?include=sku.product', $data)
        ->assertValid()
        ->assertCreated();

});

it('update', function () {

    $product = createProduct($this->branch, 3);

    $cart = CartFactory::new()
        ->withQuantity(2)
        ->withSku($product->skus[0])
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    assertDatabaseCount(Cart::class, 1);
    assertDatabaseHas(Cart::class, [
        'quantity' => 2,
    ]);

    putJson('api/carts/'.$this->branch->getRouteKey().'/'.$cart->getRouteKey(), ['quantity' => 3])
        ->assertValid()
        ->assertOk();

    assertDatabaseCount(Cart::class, 1);
    assertDatabaseHas(Cart::class, [
        'quantity' => 3,
    ]);
});

it('delete', function () {

    $product = createProduct($this->branch, 2);

    $cart = CartFactory::new()
        ->withQuantity(2)
        ->withSku($product->skus[0])
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    assertDatabaseCount(Cart::class, 1);

    deleteJson('api/carts/'.$this->branch->getRouteKey().'/'.$cart->getRouteKey())
        ->assertNoContent();

    assertDatabaseEmpty(Cart::class);
});
