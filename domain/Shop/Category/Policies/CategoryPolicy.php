<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Category\Models\Category;

class CategoryPolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(Admin $user, Category $category): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Category $category): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Category $category): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Category $category): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
