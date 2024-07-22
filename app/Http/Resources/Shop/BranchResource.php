<?php

declare(strict_types=1);

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Domain\Shop\OperationHour\Enums\Type;
use Illuminate\Http\Request;

/**
 * @property-read \Domain\Shop\Branch\Models\Branch $resource
 */
class BranchResource extends BaseJsonApiResource
{
    #[\Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'address' => $this->resource->address,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'website' => $this->resource->website,
            'operation_hours' => $this->when(
                $this->resource->is_operation_hours_enabled,
                [
                    'online' => $this->resource
                        ->operationHoursHumanReadable(Type::online),
                    'in_store' => $this->resource
                        ->operationHoursHumanReadable(Type::in_store),
                ],
            ),
        ];
    }

    /** @return array<string, callable> */
    #[\Override]
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection($this->resource->media),
        ];
    }
}
