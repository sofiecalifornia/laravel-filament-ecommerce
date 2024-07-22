<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Product\Models\AttributeOption $resource
 */
class AttributeOptionResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'value' => $this->resource->value,
            'label' => $this->resource->label,
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'attribute' => fn () => AttributeResource::make($this->resource->attribute),
        ];
    }
}
