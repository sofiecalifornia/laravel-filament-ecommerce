<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Product;

use App\Http\Resources\Shop\ProductResource;
use Domain\Shop\Product\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Resource;

#[Resource('products', only: ['index', 'show'])]
class ProductController
{
    /**
     * @unauthenticated
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<ProductResource>>
     */
    public function index(): mixed
    {
        return ProductResource::collection(
            QueryBuilder::for(
                Product::class
            )
                ->allowedIncludes([
                    'brand.media',
                    'media',
                    'skus.attributeOptions.attribute',
                    'skus.media',
                    'skus.skuStocks',
                    'category.parent',
                    'tags',
                ])
                ->allowedSorts([
                    'name', 'status', 'updated_at',
                    config('eloquent-sortable.order_column_name'),
                ])
                ->allowedFilters([
                    'skus.skuStocks.branch.slug',
                ])
                ->defaultSort(config('eloquent-sortable.order_column_name'))
                ->jsonPaginate()
        );
    }

    /**
     * @unauthenticated
     */
    public function show(string $product): ProductResource
    {
        return ProductResource::make(
            QueryBuilder::for(
                Product::query()
                    ->where((new Product())->getRouteKeyName(), $product)
            )
                ->allowedIncludes([
                    'brand.media',
                    'media',
                    'skus.attributeOptions.attribute',
                    'skus.media',
                    'skus.skuStocks',
                    'category.parent',
                    'tags',
                ])
                ->firstOrFail()
        );
    }
}
