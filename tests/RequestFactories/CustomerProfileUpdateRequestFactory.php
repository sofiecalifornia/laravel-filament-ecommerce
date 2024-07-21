<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Domain\Shop\Customer\Enums\Gender;
use Worksome\RequestFactories\RequestFactory;

class CustomerProfileUpdateRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mobile' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(Gender::cases()),
        ];
    }
}
