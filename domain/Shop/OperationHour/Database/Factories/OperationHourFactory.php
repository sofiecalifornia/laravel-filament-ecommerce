<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Database\Factories;

use Domain\Shop\OperationHour\Enums\Day;
use Domain\Shop\OperationHour\Enums\Type;
use Domain\Shop\OperationHour\Models\OperationHour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\OperationHour\Models\OperationHour>
 */
class OperationHourFactory extends Factory
{
    protected $model = OperationHour::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'day' => $this->faker->randomElement(Day::cases()),
            'from' => self::fixTimezone(
                '0'.Arr::random(['3', '4', '6', '7', '8']).':00:00'
            ),
            'to' => self::fixTimezone(
                Arr::random(['16', '17', '18', '19', '20', '21', '22']).':00:00'
            ),
            'is_open' => $this->faker->boolean(),
            'type' => $this->faker->randomElement(Type::cases()),
        ];
    }

    public function wholeDay(): self
    {
        return $this->state([
            'is_all_day' => true,
            'from' => self::fixTimezone('00:00:00'),
            'to' => self::fixTimezone('23:59:00'),
        ]);
    }

    public function open(): self
    {
        return $this->state([
            'is_open' => true,
        ]);
    }

    public function wholeWeek(Type $type): self
    {
        return $this
            ->state([
                'is_open' => true,
                'is_all_day' => false,
                'type' => $type,
            ])
            ->count(7)
            ->sequence(
                ['day' => Day::Sunday],
                ['day' => Day::Monday],
                ['day' => Day::Tuesday],
                ['day' => Day::Wednesday],
                ['day' => Day::Thursday],
                ['day' => Day::Friday],
                ['day' => Day::Saturday],
            );
    }

    private static function fixTimezone(string $datetime): Carbon
    {
        return now()
            ->parse($datetime, config('app-default.timezone'))
            ->timezone(config('app.timezone'));
    }
}
