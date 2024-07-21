<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Product\Models\Product $resource
 */
class ProductResource extends JsonApiResource
{
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
    public function toRelationships(Request $request)
    {
        return [
            'category' => fn () => CategoryResource::make($this->resource->category),
            'media' => fn () => MediaResource::collection($this->resource->media),
            'skus' => fn () => SkuResource::collection($this->resource->skus),
            'brand' => fn () => BrandResource::make($this->resource->brand),
        ];
    }
}
