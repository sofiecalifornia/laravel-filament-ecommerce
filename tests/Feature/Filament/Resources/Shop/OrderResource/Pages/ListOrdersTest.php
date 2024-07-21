<?php

declare(strict_types=1);

use App\Filament\Resources\Shop\OrderResource;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Order\Database\Factories\OrderFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    loginAsAdmin();
});

it('can render index', function () {
    get(OrderResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = OrderFactory::new()
        ->for(BranchFactory::new())
        ->count(10)
        ->create();

    livewire(OrderResource\Pages\ListOrders::class)
        ->assertCanSeeTableRecords($records);
});
