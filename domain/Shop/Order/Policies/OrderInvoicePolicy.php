<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Order\Models\OrderInvoice;

class OrderInvoicePolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(Admin $user): bool
    {
        // TODO: fix
        return true;
        //        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        // TODO: fix
        return true;
        //        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, OrderInvoice $orderInvoice): bool
    {
        // TODO: fix
        return true;
        //        return $this->checkWildcardPermissions($user);
    }

    public function download(Admin $user, OrderInvoice $orderInvoice): bool
    {
        // TODO: fix
        return true;
        //        return $this->checkWildcardPermissions($user);
    }

    public function downloadInvoice(Admin $user, OrderInvoice $orderInvoice): bool
    {
        // TODO: fix
        return true;
        //        return $this->checkWildcardPermissions($user);
    }
}
