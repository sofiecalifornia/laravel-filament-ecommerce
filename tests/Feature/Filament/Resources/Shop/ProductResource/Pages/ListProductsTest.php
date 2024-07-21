<?php

declare(strict_types=1);

use App\Filament\Resources\Shop\ProductResource;
use Domain\Shop\Product\Database\Factories\ProductFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(ProductResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = ProductFactory::new()
        ->count(10)
        ->create();

    livewire(ProductResource\Pages\ListProducts::class)
        ->assertCanSeeTableRecords($records);
});
