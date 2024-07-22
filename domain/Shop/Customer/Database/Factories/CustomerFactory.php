<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Enums\Status;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Customer\Models\Customer>
 */
class CustomerFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Customer::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mobile' => $this->faker->phoneNumber(),
            'landline' => $this->faker->phoneNumber(),
            'password' => $this->faker->password(),
            'status' => Arr::random(Status::cases()),
            'gender' => Arr::random(Gender::cases()),
            'timezone' => config('app-default.timezone'),
        ];
    }

    public function active(): self
    {
        return $this->state(['status' => Status::active]);
    }

    #[\Override]
    public function configure(): self
    {
        return $this
            ->afterCreating(function (Customer $customer) {
                self::seedRandomMedia($customer);
            });
    }
}
