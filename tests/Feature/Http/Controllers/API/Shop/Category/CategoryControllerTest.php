<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\Factories\AttributeFactory;
use Domain\Shop\Product\Database\Factories\AttributeOptionFactory;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

beforeEach(fn () => config(['media-library.version_urls' => false]));

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

    ProductFactory::new([
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

    return $category;
}
