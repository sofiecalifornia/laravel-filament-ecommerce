<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Actions;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\Type;
use Spatie\OpeningHours\Day;

readonly class GetOperationHoursHumanReadableByBranchAction
{
    public function __construct(private GetOpeningHoursByBranchAction $getOpeningHoursByBranch)
    {
    }

    /**
     * @return array<int, string>
     */
    public function execute(Branch $branch, ?Type $type = null): array
    {
        $openingHoursAction = collect(
            $this->getOpeningHoursByBranch
                ->execute($branch, $type)
                ->asStructuredData('h:i A')
        )
            ->filter(fn ($open) => isset($open['dayOfWeek']));

        $output = [];

        foreach ($openingHoursAction->groupBy('closes') as $closeTime => $openingOurs) {

            $dayOfTheWeeksKeyedByOpen = [];
            $openTimes = [];

            foreach ($openingOurs as $openingOur) {
                $dayOfTheWeeksKeyedByOpen[$openingOur['opens']][] = $openingOur['dayOfWeek'];
                $openTimes[] = $openingOur['opens'];
            }

            foreach (collect($openTimes)->unique() as $openTime) {

                $output[] = trans(
                    ':day_range: :from - :to', [
                        'day_range' => self::dayRange($dayOfTheWeeksKeyedByOpen[$openTime]),
                        'from' => $openTime,
                        'to' => '11:59 PM' === $closeTime
                           ? 'Midnight'
                           : $closeTime,
                    ]);
            }
        }

        return $output;
    }

    private static function dayRange(array $days): string
    {
        /** @var array<int> $dayNumerics */
        $dayNumerics = array_map(fn ($day) => array_search(strtolower((string) $day), Day::days()), $days);
        sort($dayNumerics);

        $prevDayNumeric = null;
        $words = [];
        foreach ($dayNumerics as $key => $dayNumeric) {
            if (! is_null($prevDayNumeric) && $prevDayNumeric + 1 === $dayNumeric) {
                $words[] = explode(' to ', (string) $words[(int) $key - 1])[0].' to '.$dayNumeric;
                unset($words[(int) $key - 1]);
            } else {
                $words[] = (int) $dayNumeric;
            }

            $prevDayNumeric = (int) $dayNumeric;
        }

        $final = '';
        foreach (str_split(implode(', ', $words)) as $char) {
            if (is_numeric($char)) {
                $final .= ucfirst((string) Day::days()[$char]);
            } else {
                $final .= $char;
            }
        }

        return $final;
    }
}
