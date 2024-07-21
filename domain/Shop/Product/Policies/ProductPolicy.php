<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Policies;

use App\Filament\Support\TenantHelper;
use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Product\Models\Product;

class ProductPolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(Admin $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(Admin $user): bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function update(Admin $user, Product $product): bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Product $product): bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function restore(Admin $user, Product $product): bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(Admin $user, Product $product): bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }
}
