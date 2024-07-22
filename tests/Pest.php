<?php

declare(strict_types=1);

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Support\TestingSeeder;

use function Pest\Laravel\seed;

uses(
    TestCase::class,
    LazilyRefreshDatabase::class,
)
    ->beforeEach(function () {
        Http::preventStrayRequests();
        Mail::fake();

        config(['media-library.version_urls' => false]);

        foreach (array_keys(config('filesystems.disks')) as $disk) {
            Storage::fake($disk);
        }

        mockStrUuid();

        Event::listen(MigrationsEnded::class, function () {

            seed(TestingSeeder::class);
        });
    })
    ->in('Feature', 'Unit');
