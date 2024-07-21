<?php

declare(strict_types=1);

namespace Database\Seeders\Auth;

use Domain\Access\Role\Support;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissionRegistrar = app(PermissionRegistrar::class);

        $role = $permissionRegistrar->getRoleClass();

        foreach (config('domain.access.role') as $roleName) {
            $role::findOrCreate(name: $roleName, guardName: 'admin');
        }

        $role::findByName(config('domain.access.role.admin'), guardName: 'admin')
            ->syncPermissions($permissionRegistrar->getPermissions()->pluck('name'));

        $role::findOrCreate(name: 'employee', guardName: 'admin')
            ->syncPermissions(['order', 'customer']);

        $role::findOrCreate(name: 'branch', guardName: 'admin')
            ->syncPermissions([
                'product.viewAny',
                'order',
                'skuStock',
            ]);

        $role::findOrCreate(name: 'demo', guardName: 'admin')
            ->syncPermissions([
                'admin.viewAny',
                'role.viewAny',
                'order',
                'customer',
                'activity',
                'category',
                'branch',
                'brand',
                'product',
                'attribute',
                'skuStock',
                Support::WIDGETS,
                Support::PAGES,
            ]);
    }
}
