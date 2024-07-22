<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Category\Models\Category $resource
 */
class CategoryResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'description' => $this->resource->description,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'parent' => fn () => self::make(
                /** @phpstan-ignore-next-line Using nullsafe property access on non-nullable type Domain\Shop\Category\Models\Category. Use -> instead. */
                $this->resource?->parent
            ),
            'media' => fn () => MediaResource::collection(
                /** @phpstan-ignore-next-line Expression on left side of ?? is not nullable. */
                $this->resource?->media ?? []
            ),
            'products' => fn () => ProductResource::collection($this->resource->products),
        ];
    }
}
