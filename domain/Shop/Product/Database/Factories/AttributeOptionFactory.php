<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database\Factories;

use Domain\Shop\Product\Models\AttributeOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\AttributeOption>
 */
class AttributeOptionFactory extends Factory
{
    protected $model = AttributeOption::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->unique()->name(),
        ];
    }

    public function hasAttribute(string $name): self
    {
        return $this->has(AttributeFactory::new(['name' => $name]));
    }
}
