<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\ORder\Models\Order $resource
 */
class OrderResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'receipt_number' => $this->resource->receipt_number,
            'notes' => $this->resource->notes,
            'payment_status' => $this->resource->payment_status->getLabel(),
            'payment_method' => $this->resource->payment_method?->getLabel(),
            'status' => $this->resource->status->getLabel(),
            'total_price' => self::money($this->resource->total_price),
            'claim_type' => $this->resource->claim_type->getLabel(),
            'claim_at' => self::datetimeFormat($this->resource->claim_at),
            'created_at' => self::datetimeFormat($this->resource->created_at),
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'orderItems' => fn () => OrderItemResource::collection($this->resource->orderItems),
        ];
    }
}
