<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database\Factories;

use Domain\Shop\Product\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
        ];
    }
}
