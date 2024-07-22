<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Actions;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\Type;
use Domain\Shop\OperationHour\Models\OperationHour;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Spatie\OpeningHours\OpeningHours;

class GetOpeningHoursByBranchAction
{
    public function execute(Branch $branch, ?Type $type = null): OpeningHours
    {

        $openingHoursArgument = [];

        $timezoneOutput = Auth::user()?->timezone ?? config('app-default.timezone');

        self::operationHours($branch, $type)
            ->each(function (OperationHour $operationHour) use ($timezoneOutput, &$openingHoursArgument) {

                if (! $operationHour->is_open) {
                    return;
                }

                $from = $operationHour->from->timezone($timezoneOutput)->format('H:i');
                $to = $operationHour->to->timezone($timezoneOutput)->format('H:i');

                if (isset($openingHoursArgument[$operationHour->day->value])) {
                    $openingHoursArgument[$operationHour->day->value]['hours'][] = "$from-$to";
                } else {
                    $openingHoursArgument[$operationHour->day->value] = [
                        'hours' => ["$from-$to"],
                        //                    'data' => '',
                    ];
                }
            });

        //        $openingHoursArgument['exceptions'] = [
        //            '2016-11-11' => ['09:00-12:00'],
        //            '2016-12-25' => [],
        //            '01-01'      => [],                // Recurring on each 1st of January
        //            '12-25'      => ['09:00-12:00'],   // Recurring on each 25th of December
        //        ];

        return OpeningHours::create(
            data: $openingHoursArgument,
            //            timezone: config('app.timezone'),
            //            outputTimezone: Auth::user()?->timezone
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Domain\Shop\OperationHour\Models\OperationHour[]
     */
    private static function operationHours(Branch $branch, ?Type $type): \Illuminate\Database\Eloquent\Collection
    {
        $operationHours = match ($type) {
            null => function () use ($branch) {

                if (! $branch->relationLoaded('operationHours')) {
                    throw new Exception($branch::class.'::operationHours not eager loaded.');
                }

                return $branch->operationHours;
            },
            Type::online => function () use ($branch) {

                if (! $branch->relationLoaded('operationHoursOnline')) {
                    throw new Exception($branch::class.'::operationHoursOnline not eager loaded.');
                }

                return $branch->operationHoursOnline;
            },
            Type::in_store => function () use ($branch) {

                if (! $branch->relationLoaded('operationHoursInStore')) {
                    throw new Exception($branch::class.'::operationHoursInStore not eager loaded.');
                }

                return $branch->operationHoursInStore;
            },
        };

        return $operationHours();
    }
}
