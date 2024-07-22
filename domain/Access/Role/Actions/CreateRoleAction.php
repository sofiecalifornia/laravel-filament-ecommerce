<?php

declare(strict_types=1);

namespace Domain\Access\Role\Actions;

use Domain\Access\Role\DataTransferObjects\RoleData;
use Domain\Access\Role\Models\Role;

final readonly class CreateRoleAction
{
    public function execute(RoleData $roleData): Role
    {
        /** @var \Domain\Access\Role\Models\Role $role */
        $role = Role::create([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
