<?php

declare(strict_types=1);

use Akaunting\Money\Money;
use Domain\Access\Admin\Database\Factories\AdminFactory;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Product\Database\Factories\AttributeFactory;
use Domain\Shop\Product\Database\Factories\AttributeOptionFactory;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;
use Laravel\Sanctum\Sanctum;
use Tests\Support\GenerateReceiptNumberActionFake;

use function Pest\Laravel\actingAs;

function loginAsAdmin(?Admin $admin = null): Admin
{
    $admin ??= createAdmin();

    $admin->assignRole(config('domain.access.role.admin'));

    actingAs($admin);

    return $admin;
}

function loginAsCustomer(?Customer $customer = null): Customer
{
    $customer ??= createCustomer();

    Sanctum::actingAs($customer);

    return $customer;
}

function createAdmin(): Admin
{
    return AdminFactory::new()
        ->createOne();
}

function createCustomer(): Customer
{
    return CustomerFactory::new()
        ->active()
        ->createOne();
}

function createProduct(Branch $branch, float $stockCount): Product
{
    $product = ProductFactory::new()
        ->for(CategoryFactory::new())
        ->for(BrandFactory::new())
        ->inStockStatus()
        ->hasRandomMedia()
        ->createOne();

    SkuFactory::forProduct(
        product: $product,
        priceOrSkuFactory: SkuFactory::new([
            'price' => 123.45,
            'minimum' => 0,
            'maximum' => 0,
        ])
            ->has(
                SkuStockFactory::new()->baseOnStock($stockCount)
                    ->for($branch)
            )
            ->hasRandomMedia()
            ->regenerateCode(),
        attributeOptions: [
            AttributeOptionFactory::new()
                ->for(AttributeFactory::new()),
        ]
    );

    return $product;
}

function fakeGenerateReceiptNumberActionFake(): void
{
    app()->bind(GenerateReceiptNumberAction::class, GenerateReceiptNumberActionFake::class);
}

function mockStrUuid(): void
{
    $counter = 0;
    Str::createUuidsUsing(function () use (&$counter) {
        $fakeUuids = require __DIR__.'/fakeUuids.php';

        $fake = $fakeUuids[$counter] ?? throw new \Exception(
            'Insufficient fake uuid, index used ['.$counter.'].'
        );
        $counter++;

        // Call to a member function toString() on string
        return Str::of($fake);
    });
}

function moneyJsonApi(Money $money): array
{
    return [
        'amount' => $money->getAmount(),
        'currency' => $money->getCurrency()->getCurrency(),
        'formatted' => $money->format(),
    ];
}
