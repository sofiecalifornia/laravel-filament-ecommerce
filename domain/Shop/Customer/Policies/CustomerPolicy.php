<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Customer\Models\Customer;

class CustomerPolicy
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

    public function update(Admin $user, Customer $customer): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Customer $customer): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Customer $customer): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Customer $customer): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
