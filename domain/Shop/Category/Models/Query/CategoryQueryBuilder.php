<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Models\Query;

use Illuminate\Database\Eloquent\Builder;

class CategoryQueryBuilder extends Builder
{
    public function whereParent(): self
    {
        return $this->whereNull('parent_id');
    }

    public function whereChild(): self
    {
        return $this->whereNotNull('parent_id');
    }
}
