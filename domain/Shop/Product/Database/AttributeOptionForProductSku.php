<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database;

use Domain\Shop\Product\Enums\AttributeFieldType;

final readonly class AttributeOptionForProductSku
{
    public function __construct(
        public string $attributeName,
        public string $attributeOptionValue,
        public ?string $attributeFieldPrefix = null,
        public ?string $attributeFieldSuffix = null,
        public AttributeFieldType $attributeFieldType = AttributeFieldType::text,
    ) {
    }
}
