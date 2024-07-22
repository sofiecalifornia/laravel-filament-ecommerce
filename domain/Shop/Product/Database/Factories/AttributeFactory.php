<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database\Factories;

use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'product_uuid' => ProductFactory::new(),
            'name' => $this->faker->unique()->name(),
            'type' => Arr::random(AttributeFieldType::cases()),
        ];
    }
}
