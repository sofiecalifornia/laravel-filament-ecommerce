<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\ORder\Models\Order $resource
 */
class OrderResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'receipt_number' => $this->resource->receipt_number,
            'notes' => $this->resource->notes,
            'payment_status' => $this->resource->payment_status,
            'payment_method' => $this->resource->payment_method,
            'status' => $this->resource->status,
            'total_price' => $this->resource->total_price,
        ];
    }

    /** @return array<string, callable> */
    public function toRelationships(Request $request)
    {
        return [
            'orderItems' => fn () => OrderItemResource::collection($this->resource->orderItems),
        ];
    }
}
