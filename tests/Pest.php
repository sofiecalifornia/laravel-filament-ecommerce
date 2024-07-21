<?php

declare(strict_types=1);

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Once\Cache;
use Spatie\Permission\PermissionRegistrar;
use Tests\CreatesApplication;
use Tests\Support\TestingSeeder;

use function Pest\Laravel\seed;

uses(
    TestCase::class,
    CreatesApplication::class,
    LazilyRefreshDatabase::class,
)
    ->beforeEach(function () {
        Cache::getInstance()->disable();
        Http::preventStrayRequests();
        Mail::fake();

        foreach (array_keys(config('filesystems.disks')) as $disk) {
            Storage::fake($disk);
        }

        Event::listen(MigrationsEnded::class, function () {

            seed(TestingSeeder::class);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    })
    ->in('Feature', 'Unit');
