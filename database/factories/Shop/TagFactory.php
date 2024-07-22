<?php

declare(strict_types=1);

namespace Database\Factories\Shop;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Tags\Tag;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Spatie\Tags\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(2),
        ];
    }
}
