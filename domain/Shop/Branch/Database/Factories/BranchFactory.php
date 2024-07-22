<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Branch\Models\Branch>
 */
class BranchFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Branch::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'code' => fn (array $attributes) => Str::of($attributes['name'])
                ->replace(' ', '_')
                ->upper(),
            'name' => $this->faker->unique()->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'website' => $this->faker->url(),
            'status' => Arr::random(Status::cases()),
        ];
    }

    public function enabled(): self
    {
        return $this->state(['status' => Status::enabled]);
    }
}
