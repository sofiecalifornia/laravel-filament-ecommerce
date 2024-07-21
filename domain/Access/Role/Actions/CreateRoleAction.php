<?php

declare(strict_types=1);

namespace Domain\Access\Role\Actions;

use Domain\Access\Role\DataTransferObjects\RoleData;
use Domain\Access\Role\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final readonly class CreateRoleAction
{
    public function __construct(private PermissionRegistrar $permissionRegistrar)
    {
    }

    public function execute(RoleData $roleData): Role
    {
        /** @phpstan-ignore-next-line */
        $role = $this->permissionRegistrar->getRoleClass()::create([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
