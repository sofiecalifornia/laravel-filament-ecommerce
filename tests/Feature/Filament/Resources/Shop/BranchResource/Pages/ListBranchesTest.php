<?php

declare(strict_types=1);

use App\Filament\Resources\Shop\BranchResource;
use Domain\Shop\Branch\Database\Factories\BranchFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(BranchResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = BranchFactory::new()
        ->count(10)
        ->create();

    livewire(BranchResource\Pages\ListBranches::class)
        ->assertCanSeeTableRecords($records);
});
