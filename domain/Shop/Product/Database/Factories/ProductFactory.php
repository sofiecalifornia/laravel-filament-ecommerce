<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Product\Enums\Status;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\Product>
 */
class ProductFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Product::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'parent_sku' => $this->faker->unique()->uuid(),
            'name' => $this->faker->unique()->name(),
            'description' => $this->faker->randomHtml(),
            'status' => Arr::random(Status::cases()),
        ];
    }

    public function inStockStatus(): self
    {
        return $this->state([
            'status' => Status::in_stock,
        ]);
    }
}
