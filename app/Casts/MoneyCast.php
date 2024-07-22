<?php

declare(strict_types=1);

namespace App\Casts;

/**
 * @template TGet
 * @template TSet
 */
class MoneyCast extends \Akaunting\Money\Casts\MoneyCast
{
    #[\Override]
    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_numeric($value)) {
            $value = money($value * 100);
        }

        return parent::set($model, $key, $value, $attributes);
    }
}
