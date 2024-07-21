<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Brand\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Brand\Models\Brand>
 */
class BrandFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
        ];
    }
}
