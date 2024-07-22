<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Product\Models\Product $resource
 */
class ProductResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'parent_sku' => $this->resource->parent_sku,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => $this->resource->status,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'category' => fn () => CategoryResource::make($this->resource->category),
            'media' => fn () => MediaResource::collection($this->resource->media),
            'skus' => fn () => SkuResource::collection($this->resource->skus),
            'brand' => fn () => BrandResource::make($this->resource->brand),
            'tags' => fn () => TagResource::collection($this->resource->tags),
        ];
    }
}
