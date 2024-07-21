<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Domain\Shop\Stock\Models\SkuStock $resource
 */
class StockResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'type' => $this->resource->type,
            'count' => $this->resource->count,
        ];
    }
}
