<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Database\Factories;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Cart\Models\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function withCustomer(Customer $customer): self
    {
        return $this->state([
            'customer_id' => $customer->getKey(),
        ]);
    }

    public function withBranch(Branch $branch): self
    {
        return $this->state([
            'branch_id' => $branch->getKey(),
        ]);
    }

    public function withQuantity(float $quantity): self
    {
        return $this->state([
            'quantity' => $quantity,
        ]);
    }

    public function withSku(Sku $sku): self
    {
        return $this->state([
            'product_id' => $sku->product->getKey(),
            'sku_id' => $sku->getKey(),
            'sku_code' => $sku->code,
            'product_name' => $sku->product->name,
            'price' => $sku->price,
            'minimum' => $sku->minimum,
            'maximum' => $sku->maximum,
        ]);
    }
}
