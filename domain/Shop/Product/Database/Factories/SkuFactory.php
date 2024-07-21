<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Database\Seeders\Faker\MoneyFakerData;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\Sku>
 */
class SkuFactory extends Factory
{
    use HasMediaFactory;

    protected $model = Sku::class;

    public function definition(): array
    {
        $this->faker->addProvider(new MoneyFakerData($this->faker));

        return [
            'product_id' => ProductFactory::new(),
            'code' => $this->faker->unique()->uuid(),
            /** @phpstan-ignore-next-line  */
            'price' => $this->faker->money(),
            'minimum' => $this->faker->boolean() ? Arr::random([1, 2, 3, 4]) : null,
            'maximum' => function (array $attributes) {
                if (isset($attributes['minimum'])) {
                    return $this->faker->boolean()
                        ? Arr::random(range($attributes['minimum'], $attributes['minimum'] + 4))
                        : null;
                }

                return $this->faker->boolean() ? Arr::random([1, 2, 3, 4]) : null;
            },
        ];
    }

    public function withDefaultData(array|Branch|BranchFactory|Collection $branches = null): self
    {
        $self = $this;

        $branches ??= BranchFactory::new();

        if ($branches instanceof Branch || $branches instanceof BranchFactory) {
            $branches = [$branches];
        }

        foreach ($branches as $branch) {

            if (! ($branch instanceof Branch) && ! ($branch instanceof BranchFactory)) {
                abort(500, 'Invalid');
            }

            $self = $self->has(SkuStockFactory::new()->for($branch));
        }

        return $self
            ->hasRandomMedia()
            ->regenerateCode();
    }

    public function regenerateCode(): self
    {
        return $this
            ->afterCreating(function (Sku $sku) {
                $output = $sku->product->name.' ';

                foreach ($sku->attributeOptions as $attributeOption) {
                    $output .= $attributeOption->attribute->name.' '.$attributeOption->value.' ';
                }

                $sku->update([
                    'code' => Str::slug($output),
                ]);
            });
    }
}
