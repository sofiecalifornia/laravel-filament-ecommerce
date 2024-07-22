<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Branch;

use App\Http\Resources\Shop\BranchResource;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Resource;

#[Resource('branches', only: ['index', 'show'])]
class BranchController
{
    /**
     * @unauthenticated
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<BranchResource>>
     */
    public function index(): mixed
    {
        return BranchResource::collection(
            QueryBuilder::for(
                Branch::whereStatus(Status::enabled)
                    ->with([
                        'operationHoursOnline',
                        'operationHoursInStore',
                    ])
            )
                ->allowedIncludes(['media'])
                ->allowedSorts([
                    'name',
                    'updated_at',
                    config('eloquent-sortable.order_column_name'),
                ])
                ->defaultSort(config('eloquent-sortable.order_column_name'))
                ->jsonPaginate()
        );
    }

    public function show(string $branch): mixed
    {
        return BranchResource::make(
            QueryBuilder::for(
                Branch::whereStatus(Status::enabled)
                    ->where((new Branch())->getRouteKeyName(), $branch)
                    ->with([
                        'operationHoursOnline',
                        'operationHoursInStore',
                    ])
            )
                ->allowedIncludes(['media'])
                ->firstOrFail()
        );
    }
}
