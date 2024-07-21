<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Stock\Models\SkuStock;

class SkuStockPolicy
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

    public function update(Admin $user, SkuStock $stock): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, SkuStock $stock): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
