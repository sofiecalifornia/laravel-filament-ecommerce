<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media $resource
 */
class MediaResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'collection_name' => $this->resource->collection_name,
            'file_name' => $this->resource->file_name,
            'custom_properties' => $this->resource->custom_properties,
            'generated_conversions' => $this->generatedConversionUrls(),
            'type' => $this->resource->type,
        ];
    }

    private function generatedConversionUrls(): array
    {
        return $this->resource->getGeneratedConversions()
            ->map(
                fn ($status, $generatedConversion) => $this
                    ->resource
                    ->getFullUrl($generatedConversion)
            )
            ->toArray();
    }
}
