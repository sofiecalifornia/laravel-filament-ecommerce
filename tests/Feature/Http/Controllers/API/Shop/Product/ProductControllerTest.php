<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Product\Database\Factories\AttributeFactory;
use Domain\Shop\Product\Database\Factories\AttributeOptionFactory;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

beforeEach(fn () => config(['media-library.version_urls' => false]));

dataset(
    'includes',
    [
        'media',
        'brand',
        'brand.media',
        'skus',
        'skus.attributeOptions',
        'skus.attributeOptions.attribute',
        'skus.media',
        'skus.skuStocks',
        'category',
        'category.parent',
    ]
);

it('list', function (?string $include) {

    assertDatabaseEmpty(Product::class);

    seedProduct();

    $response = getJson('api/products?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

it('show', function (?string $include) {

    assertDatabaseEmpty(Product::class);

    $product = seedProduct();

    $response = getJson('api/products/'.$product->getRouteKey().'?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

function seedProduct(): Product
{
    return ProductFactory::new([
        'parent_sku' => 'sku sample',
        'name' => 'Samsung Galaxy S21',
        'description' => 'sample description',
    ])
        ->inStockStatus()
        ->hasSpecificMedia()
        ->for(
            BrandFactory::new(['name' => 'test brand'])
                ->hasSpecificMedia()
        )
        ->hasSku(
            priceOrSkuFactory: SkuFactory::new([
                'code' => 'sample-code',
                'price' => 349,
                'minimum' => 1,
                'maximum' => 10,
            ])
                ->hasSpecificMedia()
                ->has(
                    SkuStockFactory::new()
                        ->unlimited()
                        ->for(
                            BranchFactory::new()
                                ->enabled()
                                ->hasSpecificMedia()
                        )
                ),
            attributeOptionFactories: [
                AttributeOptionFactory::new(['value' => 'Blue'])
                    ->for(
                        AttributeFactory::new(['name' => 'Color'])
                            ->has(AttributeOptionFactory::new(['value' => 'Red']))
                    ),
            ],
        )
        ->createOne();
}
