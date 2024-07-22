<?php

declare(strict_types=1);

namespace App;

use Dedoc\Scramble\Extensions\TypeToSchemaExtension;
use Dedoc\Scramble\Support\Generator\Reference;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Type;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use TiMacDonald\JsonApi\JsonApiResource;

class JsonApiResourceToSchemaExtension extends TypeToSchemaExtension
{
    public function shouldHandle(Type $type): bool
    {
        return $type instanceof ObjectType
            && $type->isInstanceOf(JsonApiResource::class)
            && ! $type->isInstanceOf(AnonymousResourceCollection::class);
    }

    #[\Override]
    public function toSchema(Type $type): mixed
    {
        $this->infer->analyzeClass($type->name);

        $toArrayReturnType = $type->getMethodReturnType('toAttributes');

        return $this->openApiTransformer->transform($toArrayReturnType);
    }

    public function reference(ObjectType $type): Reference
    {
        return new Reference('schemas', $type->name, $this->components);
    }
}
