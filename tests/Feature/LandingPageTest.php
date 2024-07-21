<?php

declare(strict_types=1);

it('run base url')
    ->get('/')
    ->assertOk();
