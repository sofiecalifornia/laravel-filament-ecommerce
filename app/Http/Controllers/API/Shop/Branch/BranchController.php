<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Branch;

use App\Http\Resources\Shop\BranchResource;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Get;

class BranchController
{
    #[Get('branches', name: 'branches.index')]
    public function __invoke(): mixed
    {
        return BranchResource::collection(
            QueryBuilder::for(
                Branch::whereStatus(Status::ENABLED)
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
}
