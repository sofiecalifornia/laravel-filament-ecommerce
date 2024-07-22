<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

dataset(
    'includes',
    [
        'media',
        'parent',
        'parent.media',

        'products',
        'products.brand',
        'products.media',
        'products.skus',
    ]
);

it('list', function (?string $include) {

    assertDatabaseEmpty(Category::class);

    seedCategory();

    $response = getJson('api/categories?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

it('show', function (?string $include) {

    assertDatabaseEmpty(Category::class);

    $category = seedCategory();

    $response = getJson('api/categories/'.$category->getRouteKey().'?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

function seedCategory(): Category
{
    $category = CategoryFactory::new([
        'name' => 'test name',
        'description' => 'test description',
    ])
        ->for(
            CategoryFactory::new([
                'name' => 'test parent name',
                'description' => 'test parent description',
            ])
                ->hasSpecificMedia()
                ->isVisibleStatus(),
            'parent',
        )
        ->hasSpecificMedia()
        ->isVisibleStatus()
        ->createOne();

    $product = ProductFactory::new([
        'parent_sku' => 'sku sample',
        'name' => 'Samsung Galaxy S21',
        'description' => 'sample description',
    ])
        ->for($category)
        ->inStockStatus()
        ->hasSpecificMedia()
        ->for(
            BrandFactory::new(['name' => 'test brand'])
                ->hasSpecificMedia()
        )->createOne();

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

    return $category;
}
