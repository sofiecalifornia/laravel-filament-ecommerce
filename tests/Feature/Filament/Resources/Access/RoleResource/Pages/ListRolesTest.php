<?php

declare(strict_types=1);

use App\Filament\Resources\Access\RoleResource;

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(RoleResource::getUrl())
        ->assertOk();
});

todo('can index list');
