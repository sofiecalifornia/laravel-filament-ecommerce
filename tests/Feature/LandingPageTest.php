<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('run base url')
    ->get('/')
    ->assertOk();

it('run base url w/ logged-in admin', function () {
    loginAsAdmin();
    get('/')
        ->assertOk();
});
