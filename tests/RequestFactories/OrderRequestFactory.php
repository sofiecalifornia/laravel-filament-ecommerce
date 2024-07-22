<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\PaymentMethod;
use Illuminate\Support\Arr;
use Worksome\RequestFactories\RequestFactory;

class OrderRequestFactory extends RequestFactory
{
    #[\Override]
    public function definition(): array
    {
        return [
            'payment_method' => Arr::random(PaymentMethod::cases()),
            'notes' => $this->faker->boolean() ? $this->faker->sentence() : null,
            'claim_at' => $this->faker
                ->dateTimeBetween('now', '+1 week')
                ->format('Y-m-d H:i'),
            'claim_type' => ClaimType::delivery,
        ];
    }
}
