<?php

declare(strict_types=1);

use App\Jobs\QueueJobPriority;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;

Artisan::command('app:permissions-sync', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */
    Artisan::call('db:seed', ['--class' => PermissionSeeder::class, '--force' => true]);

    $this->info(Artisan::output());

    Artisan::call('db:seed', ['--class' => RoleSeeder::class, '--force' => true]);

    $this->info(Artisan::output());

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    cache()->flush();

    $this->info('Done seeding roles and permissions.');
})
    ->purpose('Reset database role with sync permissions');

Artisan::command('app:horizon:clear', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */
    foreach (QueueJobPriority::PRIORITIES as $queueName) {
        Artisan::call('horizon:clear', ['--queue' => $queueName, '--force' => true]);
        $this->info(Artisan::output());
    }

    $this->info('Done clear jobs on horizon..');
});
