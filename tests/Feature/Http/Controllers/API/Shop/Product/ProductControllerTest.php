<?php

declare(strict_types=1);

use Database\Factories\Shop\TagFactory;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

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
        'tags',
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
    $product = ProductFactory::new([
        'parent_sku' => 'sku sample',
        'name' => 'Samsung Galaxy S21',
        'description' => 'sample description',
    ])
        ->has(
            TagFactory::new(['name' => 'test tag'])
        )
        ->inStockStatus()
        ->hasSpecificMedia()
        ->for(
            BrandFactory::new(['name' => 'test brand'])
                ->hasSpecificMedia()
        )
        ->createOne();

    SkuFactory::forProduct(
        product: $product,
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
        attributeOptions: [
            new AttributeOptionForProductSku(
                'Color',
                'Blue',
                attributeFieldType: AttributeFieldType::color_picker
            ),
        ],
    );

    return $product;
}
