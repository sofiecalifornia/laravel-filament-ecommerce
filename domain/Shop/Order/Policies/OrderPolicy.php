<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Order\Models\Order;

class OrderPolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function exportAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function view(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    //    public function update(Admin $user, Order $order): bool
    //    {
    //        return $this->checkWildcardPermissions($user);
    //    }

    public function delete(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function print(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function export(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function updatePaymentMethod(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function updatePaymentStatus(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function updateStatus(Admin $user, Order $order): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
