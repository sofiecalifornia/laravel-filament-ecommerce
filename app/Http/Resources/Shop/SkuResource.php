<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Product\Models\Sku $resource
 */
class SkuResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'code' => $this->resource->code,
            'price' => self::money($this->resource->price),
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection($this->resource->media),
            'attributeOptions' => fn () => AttributeOptionResource::collection($this->resource->attributeOptions),
            'skuStocks' => fn () => StockResource::collection($this->resource->skuStocks),
        ];
    }
}
