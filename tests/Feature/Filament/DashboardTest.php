<?php

declare(strict_types=1);

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can render dashboard', function () {

    get('admin')
        ->assertOk();
});
