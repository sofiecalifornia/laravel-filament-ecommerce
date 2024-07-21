<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * https://filamentphp.com/docs/3.x/panels/getting-started#casting-the-price-to-an-integer
 */
class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, mixed $value, array $attributes): float
    {
        // Transform the integer stored in the database into a float.
        return round(floatval($value) / 100, precision: 2);
    }

    public function set($model, string $key, mixed $value, array $attributes): float
    {
        // Transform the float into an integer for storage.
        return round(floatval($value) * 100);
    }
}
