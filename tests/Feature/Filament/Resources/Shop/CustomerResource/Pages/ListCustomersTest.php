<?php

declare(strict_types=1);

use App\Filament\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    loginAsAdmin();
});

it('can render index', function () {
    get(CustomerResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = CustomerFactory::new()
        ->count(10)
        ->create();

    livewire(CustomerResource\Pages\ListCustomers::class)
        ->assertCanSeeTableRecords($records);
});
