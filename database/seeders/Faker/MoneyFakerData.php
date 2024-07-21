<?php

declare(strict_types=1);

namespace Database\Seeders\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Arr;

class MoneyFakerData extends Base
{
    public function money(): int
    {
        $a = Arr::random(range(100, 1000));
        $b = Arr::random(range(1, 99)); // / 100;

        return $a + $b;
    }
}
