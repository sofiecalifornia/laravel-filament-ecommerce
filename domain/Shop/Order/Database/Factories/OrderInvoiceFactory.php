<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Database\Factories;

use Domain\Shop\Order\Models\OrderInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Order\Models\OrderInvoice>
 */
class OrderInvoiceFactory extends Factory
{
    protected $model = OrderInvoice::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'file_name' => Str::snake($this->faker->word()).'.pdf',
        ];
    }
}
