<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Illuminate\Foundation\Auth\User;

class AdminPolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $admin = null): ?bool
    {
        if ($admin instanceof Admin && $admin->isZeroDayAdmin()) {
            return false;
        }

        if ($user instanceof Admin && $admin instanceof Admin) {
            if ($admin->isSuperAdmin()) {
                return $user->isSuperAdmin();
            }
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(User $user, Admin $admin): bool
    {

        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function updatePassword(User $user, Admin $admin): bool
    {
        if ($admin->trashed()) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function impersonate(User $user, Admin $admin): bool
    {
        if ($admin->trashed()) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }
}
