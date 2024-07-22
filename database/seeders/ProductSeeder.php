<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {
        /** @var \Domain\Shop\Category\Models\Category $category */
        $category = Category::whereChild()->first();
        [$iPhone, $samsung] = Brand::orderBy('name')->get();
        /** @var \Domain\Shop\Branch\Models\Branch[] $branches */
        $branches = Branch::orderBy('name')->get();

        $product = ProductFactory::new(['name' => 'Samsung Galaxy S21'])
            ->for($category)
            ->for($samsung)
            ->inStockStatus()
            ->hasRandomMedia()
            ->createOne();

        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 100,
            attributeOptions: [

                new AttributeOptionForProductSku(
                    'Color',
                    'Red',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '2',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '8',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 200,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Black',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '4',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '1',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 300,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'White',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '16',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '64',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );

        $product = ProductFactory::new(['name' => 'iPhone 14 MAX'])
            ->for($category)
            ->for($iPhone)
            ->inStockStatus()
            ->hasRandomMedia()
            ->createOne();

        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 400,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Red',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '2',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '8',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 500,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Black',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '4',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '1',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 600,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'White',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '16',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '64',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
    }
}
