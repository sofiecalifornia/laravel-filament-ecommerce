<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Database\Factories;

use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Order\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomDigitNotZero(),
        ];
    }

    public function forSku(Sku $sku, float $quantity = null): self
    {
        $quantity ??= (float) $this->faker->numberBetween(1, 5);

        return $this
            ->for($sku)
            ->state([
                'quantity' => $quantity,
            ]);
    }
}
