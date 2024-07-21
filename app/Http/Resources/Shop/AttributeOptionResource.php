<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Product\Models\AttributeOption $resource
 */
class AttributeOptionResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'attribute_name' => $this->when(
                $this->resource->relationLoaded('attribute'),
                fn () => $this->resource->attribute->name
            ),
            'value' => $this->resource->value,
        ];
    }
}
