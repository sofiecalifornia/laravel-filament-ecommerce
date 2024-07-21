<?php

declare(strict_types=1);

use App\Filament\Resources\Shop\BrandResource;
use Domain\Shop\Brand\Database\Factories\BrandFactory;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(BrandResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    $records = BrandFactory::new()
        ->count(10)
        ->create();

    livewire(BrandResource\Pages\ListBrands::class)
        ->assertCanSeeTableRecords($records);
});
