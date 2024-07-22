<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Brand\Models\Brand $resource
 */
class BrandResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection(
                /** @phpstan-ignore-next-line Expression on left side of ?? is not nullable. */
                $this->resource?->media ?? []
            ),
        ];
    }
}
