<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Category\Models\Category>
 */
class CategoryFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'description' => $this->faker->randomHtml(),
            'is_visible' => $this->faker->boolean(chanceOfGettingTrue: 75),
        ];
    }

    public function isVisibleStatus(bool $visible = true): self
    {
        return $this->state(['is_visible' => $visible]);
    }
}
