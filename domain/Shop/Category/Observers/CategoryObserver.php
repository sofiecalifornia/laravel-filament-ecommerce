<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Observers;

use Domain\Shop\Category\Models\Category;

class CategoryObserver
{
    public function deleting(Category $category): void
    {
        if ($category->products()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete category with associated products.'));
        }
        if ($category->children()->withTrashed()->count() > 0) {
            abort(403, trans('Can not delete category with associated children.'));
        }
    }

    public function updating(Category $category): void
    {
        if ($category->parent_id === null && $category->products()->withTrashed()->count() > 0) {
            abort(403, trans('Can not remove parent category with associated products.'));
        }
    }
}
