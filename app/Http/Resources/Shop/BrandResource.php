<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Brand\Models\Brand $resource
 */
class BrandResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }

    /** @return array<string, callable> */
    public function toRelationships(Request $request)
    {
        return [
            'media' => fn () => MediaResource::collection(
                /** @phpstan-ignore-next-line Expression on left side of ?? is not nullable. */
                $this->resource?->media ?? []
            ),
        ];
    }
}
