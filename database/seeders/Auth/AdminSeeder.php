<?php

declare(strict_types=1);

namespace Database\Seeders\Auth;

use Domain\Access\Admin\Database\Factories\AdminFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminHashPassword = config('seeder.admin_hash_password');

        if (blank($superAdminHashPassword)) {
            $this->command->getOutput()->error('SUPER_ADMIN_PASSWORD_HASH not defined in .env file.');
            exit();
        }

        $role = app(PermissionRegistrar::class)->getRoleClass();

        AdminFactory::new()
            ->createOne([
                'name' => 'Lloric Garcia',
                'email' => 'ecommerce@lloricode.com',
                'password' => $superAdminHashPassword,
            ])
            ->assignRole($role::findByName(config('domain.access.role.super_admin'), 'admin'));
        unset($superAdminHashPassword);

        AdminFactory::new()
            ->createOne([
                'name' => 'Admin',
                'email' => 'admin.ecommerce@lloricode.com',
            ])
            ->assignRole($role::findByName(config('domain.access.role.admin'), 'admin'));

        AdminFactory::new()
            ->createOne([
                'name' => 'Employee',
                'email' => 'employee.ecommerce@lloricode.com',
            ])
            ->assignRole($role::findByName('employee', 'admin'));

        AdminFactory::new()
            ->createOne([
                'name' => 'Employee 2',
                'email' => 'employee2.ecommerce@lloricode.com',
            ])
            ->assignRole($role::findByName('employee', 'admin'));

        AdminFactory::new()
            ->createOne([
                'name' => 'Demo',
                'email' => self::demoEmail(),
                'password' => self::demoPassword(),
            ])
            ->assignRole($role::findByName('demo', 'admin'));

        AdminFactory::new()
            ->withGoogle2fa()
            ->createOne([
                'name' => 'OTP',
                'email' => 'otp.ecommerce@lloricode.com',
                'password' => app()->environment('local', 'testing') ? 'secret' : '3CMLPJRWJOYEDEQL',
            ])
            ->assignRole($role::findByName('demo', 'admin'));

        AdminFactory::new()
            ->createOne([
                'name' => 'No Role',
                'email' => 'no-roles.ecommerce@lloricode.com',
            ]);
    }

    public static function demoEmail(): string
    {
        return 'demo.ecommerce@lloricode.com';
    }

    public static function demoPassword(): string
    {
        return app()->isLocal() ? 'secret' : 'K5D@P^y#Z9v778v7DX9u3#T@mNfVmS';
    }
}
