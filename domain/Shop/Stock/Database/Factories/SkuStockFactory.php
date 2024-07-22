<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Database\Factories;

use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Stock\Models\SkuStock>
 */
class SkuStockFactory extends Factory
{
    protected $model = SkuStock::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'type' => Arr::random(StockType::cases()),
            'count' => fn (array $attributes) => match ($attributes['type']) {
                StockType::base_on_stock => $this->faker->numberBetween(15, 30),
                default => null,
            },
            'warning' => fn (array $attributes) => match ($attributes['type']) {
                StockType::base_on_stock => $this->faker->numberBetween(5, 15),
                default => null,
            },
        ];
    }

    public function unlimited(): self
    {
        return $this->state([
            'type' => StockType::unlimited,
        ]);
    }

    public function baseOnStock(float $stockCount): self
    {
        return $this->state([
            'type' => StockType::base_on_stock,
            'count' => $stockCount,
        ]);
    }
}
