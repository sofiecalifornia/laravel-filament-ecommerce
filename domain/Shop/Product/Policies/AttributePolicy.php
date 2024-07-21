<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Policies;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\ChecksWildcardPermissions;
use Domain\Shop\Product\Models\Attribute;

class AttributePolicy
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

    public function update(Admin $user, Attribute $attribute): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(Admin $user, Attribute $attribute): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
