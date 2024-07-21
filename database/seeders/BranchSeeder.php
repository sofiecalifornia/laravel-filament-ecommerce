<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Auth\AdminSeeder;
use Domain\Access\Admin\Database\Factories\AdminFactory;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Domain\Shop\Branch\Models\Branch[] $branches */
        $branches = BranchFactory::new()
            ->enabled()
            ->hasRandomMedia(collectionName: 'panel')
            ->hasRandomMedia()
            ->count(2)
            ->sequence(
                ['name' => 'Branch 1'],
                ['name' => 'Branch 2'],
            )
            ->create();

        /** @var \Domain\Access\Admin\Models\Admin $demo */
        $demo = Admin::whereEmail(AdminSeeder::demoEmail())
            ->first();

        $demo->branches()
            ->attach($branches);

        $role = app(PermissionRegistrar::class)->getRoleClass();

        foreach ($branches as $branch) {
            AdminFactory::new()
                ->hasAttached($branch)
                ->createOne([
                    'name' => 'Demo '.$branch->name,
                    'email' => Str::kebab($branch->name).'@ecommerce.com',
                ])
                ->assignRole($role::findByName('branch', 'admin'));
        }
    }
}
