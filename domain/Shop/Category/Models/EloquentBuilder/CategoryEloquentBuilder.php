<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Models\EloquentBuilder;

use Illuminate\Database\Eloquent\Builder;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Category\Models\Category>
 */
class CategoryEloquentBuilder extends Builder
{
    public function whereParent(): self
    {
        return $this->whereNull('parent_uuid');
    }

    public function whereChild(): self
    {
        return $this->whereNotNull('parent_uuid');
    }
}
