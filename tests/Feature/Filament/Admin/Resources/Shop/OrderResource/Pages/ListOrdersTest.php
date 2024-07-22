<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ListOrders;
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

    fakeGenerateReceiptNumberActionFake();

    $records = OrderFactory::new()
        ->for(BranchFactory::new())
        ->count(10)
        ->create();

    livewire(ListOrders::class)
        ->assertCanSeeTableRecords($records);
});
