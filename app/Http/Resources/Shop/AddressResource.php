<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Customer\Models\Address $resource
 */
class AddressResource extends BaseJsonApiResource
{
    #[\Override]
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
