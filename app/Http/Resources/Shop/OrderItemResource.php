<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\ORder\Models\OrderItem $resource
 */
class OrderItemResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'quantity' => $this->resource->quantity,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request)
    {
        return [
            'sku' => fn () => SkuResource::make($this->resource->sku),
        ];
    }
}
