<?php

declare(strict_types=1);

it('run route:list command')
    ->artisan('route:list')
    ->assertSuccessful();
