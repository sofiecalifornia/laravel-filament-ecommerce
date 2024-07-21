<?php

declare(strict_types=1);

use App\Filament\Resources\Access\AdminResource;
use Domain\Access\Admin\Database\Factories\AdminFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(AdminResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = AdminFactory::new()
        ->count(9) // included current user
        ->create();

    livewire(AdminResource\Pages\ListAdmins::class)
        ->assertCanSeeTableRecords($records);
});
