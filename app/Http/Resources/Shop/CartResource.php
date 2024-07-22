<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/** @property-read \Domain\Shop\Cart\Models\Cart $resource */
class CartResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'product_name' => $this->resource->product_name,
            'sku_code' => $this->resource->sku_code,
            'price' => self::money($this->resource->price),
            'quantity' => $this->resource->quantity,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'sku' => fn () => SkuResource::make($this->resource->sku),
        ];
    }
}
