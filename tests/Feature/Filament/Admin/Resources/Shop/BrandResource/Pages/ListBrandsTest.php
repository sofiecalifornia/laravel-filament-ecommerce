<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Shop\BrandResource;
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

    livewire(\App\Filament\Admin\Resources\Shop\BrandResource\Pages\ListBrands::class)
        ->assertCanSeeTableRecords($records);
});
