<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Branch\Models\Branch>
 */
class BranchFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'status' => Arr::random(Status::cases()),
        ];
    }

    public function enabled(): self
    {
        return $this->state(['status' => Status::ENABLED]);
    }
}
