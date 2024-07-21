<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Customer\Models\Customer $resource
 */
class CustomerResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'reference_number' => $this->resource->reference_number,
            'email' => $this->resource->email,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'mobile' => $this->resource->mobile,
            'status' => $this->resource->status,
            'gender' => $this->resource->gender,
        ];
    }

    /** @return array<string, callable> */
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection($this->resource->media),
            'addresses' => fn () => AddressResource::collection($this->resource->addresses),
        ];
    }
}
