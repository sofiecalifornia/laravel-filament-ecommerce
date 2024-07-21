<?php

declare(strict_types=1);

use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Illuminate\Support\Facades\Artisan;

Artisan::command('app:permissions-sync', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */
    Artisan::call('db:seed', ['--class' => PermissionSeeder::class, '--force' => true]);
    Artisan::call('db:seed', ['--class' => RoleSeeder::class, '--force' => true]);
    cache()->flush();
    $this->info('Done seeding roles and permissions.');
})
    ->purpose('Reset database role with sync permissions');
