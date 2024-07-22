<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Access\ActivityResource;

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(ActivityResource::getUrl())
        ->assertOk();
});

todo('can index list');
