<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\ORder\Models\OrderItem $resource
 */
class OrderItemResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
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
