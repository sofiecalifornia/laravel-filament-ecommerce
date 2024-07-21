<?php

declare(strict_types=1);

namespace Domain\Access\Role\Actions;

use Domain\Access\Role\DataTransferObjects\RoleData;
use Domain\Access\Role\Models\Role;

final readonly class EditRoleAction
{
    public function execute(Role $role, RoleData $roleData): Role
    {
        if (in_array($role->name, (array) config('domain.access.role'))) {
            abort(400, 'Cannot update this role.');
        }

        $role->update([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
