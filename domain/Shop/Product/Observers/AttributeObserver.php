<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Product\Models\Attribute;
use Filament\Support\Exceptions\Halt;

class AttributeObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Attribute $attribute): void
    {
        $attribute->loadCount('attributeOptions');

        if ($attribute->attribute_options_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $attribute,
                trans('Can not delete attribute with associated attribute options.'),
                'attributeOptions',
                $attribute->attribute_options_count
            );

        }
    }
}
