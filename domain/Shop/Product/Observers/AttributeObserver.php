<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Observers;

use Domain\Shop\Product\Models\Attribute;

class AttributeObserver
{
    public function deleting(Attribute $attribute): void
    {
        if ($attribute->attributeOptions->count() > 0) {
            abort(403, trans('Can not delete attribute with associated attribute options.'));
        }
    }
}
