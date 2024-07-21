<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @property-read \Domain\Shop\Cart\Models\Cart $resource */
class CartResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'product_name' => $this->resource->product_name,
            'sku_code' => $this->resource->sku_code,
            'price' => (float) $this->resource->price,
            'quantity' => $this->resource->quantity,
        ];
    }

    /** @return array<string, callable> */
    public function toRelationships(Request $request)
    {
        return [
            'sku' => fn () => SkuResource::make($this->resource->sku),
        ];
    }
}
