<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Branch\Models\Branch;

class BranchPolicy
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

    public function update(Admin $user, Branch $branch): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Branch $branch): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Branch $branch): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Branch $branch): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
