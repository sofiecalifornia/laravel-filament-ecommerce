<?php

declare(strict_types=1);

use Domain\Access\Admin\Database\Factories\AdminFactory;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Database\Factories\AttributeFactory;
use Domain\Shop\Product\Database\Factories\AttributeOptionFactory;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\actingAs;

function loginAsAdmin(Admin $admin = null): Admin
{
    $admin ??= createAdmin();

    $admin->assignRole(config('domain.access.role.admin'));

    actingAs($admin);

    return $admin;
}

function loginAsCustomer(Customer $customer = null): Customer
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
    return ProductFactory::new()
        ->for(CategoryFactory::new())
        ->for(BrandFactory::new())
        ->inStockStatus()
        ->hasRandomMedia()
        ->hasSku(
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
            attributeOptionFactories: [
                AttributeOptionFactory::new()
                    ->for(AttributeFactory::new()),
            ],
        )
        ->createOne();
}
