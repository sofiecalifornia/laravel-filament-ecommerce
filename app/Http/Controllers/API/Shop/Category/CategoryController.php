<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Category;

use App\Http\Resources\Shop\CategoryResource;
use Domain\Shop\Category\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\ApiResource;

#[ApiResource('categories', only: ['index', 'show'])]
class CategoryController
{
    /**
     * @unauthenticated
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<CategoryResource>>
     */
    public function index(): mixed
    {
        return CategoryResource::collection(
            QueryBuilder::for(
                Category::whereIsVisible(true)
            )
                ->allowedIncludes([
                    'media',
                    'parent.media',

                    'products.brand',
                    'products.media',
                    'products.skus',
                ])
                ->allowedSorts([
                    'name', 'is_visible', 'updated_at',
                    config('eloquent-sortable.order_column_name'),
                ])
                ->defaultSort(config('eloquent-sortable.order_column_name'))
                ->jsonPaginate()
        );
    }

    /**
     * @unauthenticated
     */
    public function show(string $category): CategoryResource
    {
        return CategoryResource::make(
            QueryBuilder::for(
                Category::query()
                    ->where((new Category())->getRouteKeyName(), $category)
                    ->whereIsVisible(true)
            )
                ->allowedIncludes([
                    'media',
                    'parent.media',

                    'products.brand',
                    'products.media',
                    'products.skus',
                ])
                ->firstOrFail()
        );
    }
}
