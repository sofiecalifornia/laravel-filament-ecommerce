<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\OperationHour\Models\OperationHour;

class OperationHourPolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);

    }

    public function view(Admin $user, OperationHour $operationHour): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(Admin $user, OperationHour $operationHour): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, OperationHour $operationHour): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
