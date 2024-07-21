<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class OrderSettings extends Settings
{
    public string $prefix;

    public static function group(): string
    {
        return 'order';
    }
}
