<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Brand\Models\Brand;

class BrandPolicy
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

    public function update(Admin $user, Brand $brand): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Brand $brand): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Brand $brand): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Brand $brand): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
