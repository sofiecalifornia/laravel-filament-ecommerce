<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Customer\Models\Customer $resource
 */
class CustomerResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'email' => $this->resource->email,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'mobile' => $this->resource->mobile,
            'status' => $this->resource->status,
            'gender' => $this->resource->gender,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection($this->resource->media),
            'addresses' => fn () => AddressResource::collection($this->resource->addresses),
        ];
    }
}
