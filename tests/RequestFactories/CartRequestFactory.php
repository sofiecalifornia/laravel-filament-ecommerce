<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Domain\Shop\Product\Models\Sku;
use Worksome\RequestFactories\RequestFactory;

class CartRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomDigitNotZero(),
        ];
    }

    public function withSku(Sku $sku): self
    {
        return $this->state([
            'sku_id' => $sku->getRouteKey(),
        ]);
    }

    public function withQuantity(int $quantity): self
    {
        return $this->state([
            'quantity' => $quantity,
        ]);
    }
}
