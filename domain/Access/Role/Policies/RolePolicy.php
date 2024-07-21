<?php

declare(strict_types=1);

namespace Domain\Access\Role\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Access\Role\Models\Role;
use Illuminate\Foundation\Auth\User;

class RolePolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $role = null): ?bool
    {
        if ($role instanceof Role && in_array($role->name, (array) config('domain.access.role'))) {
            return false;
        }

        return null;
    }

    public function viewAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(Admin $user, Role $role): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Role $role): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
