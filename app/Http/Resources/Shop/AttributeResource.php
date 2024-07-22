<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Product\Models\Attribute $resource
 */
class AttributeResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }
}
