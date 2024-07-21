<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Customer\Models\Address $resource
 */
class AddressResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'country' => $this->resource->country,
            'street' => $this->resource->street,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'zip' => $this->resource->zip,
        ];
    }
}
