<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Domain\Shop\Order\Enums\PaymentMethod;
use Illuminate\Support\Arr;
use Worksome\RequestFactories\RequestFactory;

class OrderRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'payment_method' => Arr::random(PaymentMethod::cases()),
            'notes' => $this->faker->randomHtml(),
        ];
    }
}
