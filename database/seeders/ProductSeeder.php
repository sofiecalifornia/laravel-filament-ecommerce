<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\Factories\AttributeFactory;
use Domain\Shop\Product\Database\Factories\AttributeOptionFactory;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Domain\Shop\Category\Models\Category $category */
        $category = Category::whereChild()->first();
        [$iPhone, $samsung] = Brand::orderBy('name')->get();
        /** @var \Domain\Shop\Branch\Models\Branch[] $branches */
        $branches = Branch::orderBy('name')->get();

        /** @phpstan-ignore-next-line  */
        [$color, $ram, $storage] =
            AttributeFactory::new()
                ->count(3)
                ->sequence(
                    ['name' => 'Color'],
                    ['name' => 'RAM'],
                    ['name' => 'Storage'],
                )
                ->create();

        ProductFactory::new(['name' => 'Samsung Galaxy S21'])
            ->for($category)
            ->for($samsung)
            ->inStockStatus()
            ->hasRandomMedia()
            ->hasSku(
                priceOrSkuFactory: 349,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Red'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '2GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 349,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Green'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '4GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 349,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Yellow'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '8GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 1_099,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Blue'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '8GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '512GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 1_499,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Black'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '16GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '1TB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->createOne();

        ProductFactory::new(['name' => 'iPhone 14 MAX'])
            ->for($category)
            ->for($iPhone)
            ->inStockStatus()
            ->hasRandomMedia()
            ->hasSku(
                priceOrSkuFactory: 449,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Red'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '2GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 449,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Green'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '4GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 449,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Yellow'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '8GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '32GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 1_299,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Blue'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '8GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '512GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->hasSku(
                priceOrSkuFactory: 1_999,
                attributeOptionFactories: [
                    AttributeOptionFactory::new(['value' => 'Blue'])
                        ->for($color),
                    AttributeOptionFactory::new(['value' => '16GB'])
                        ->for($ram),
                    AttributeOptionFactory::new(['value' => '512GB'])
                        ->for($storage),
                ],
                branches: $branches,
            )
            ->createOne();
    }
}
