<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Domain\Shop\Customer\Enums\Gender;
use Worksome\RequestFactories\RequestFactory;

class RegisterRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
            'password_confirmation' => fn (array $attributes) => $attributes['password'],
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mobile' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(Gender::cases()),
        ];
    }
}
