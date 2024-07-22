<?php

// https://github.com/spatie/opening-hours

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Actions\GetOpeningHoursByBranchAction;

$branch = Branch::whereCode('BRANCH_1')
    ->with('operationHours')
//    ->with('operationHoursOnline')
//    ->with('operationHoursInStore')
    ->first();

$openingHours = app(GetOpeningHoursByBranchAction::class)
    ->execute($branch);

$now = now();
$range = $openingHours->currentOpenRange($now);

if ($range) {
    echo "It's open since ".$range->start()."\n";
    echo "It will close at ".$range->end()."\n";
} else {
    echo "It's closed since ".$openingHours->previousClose($now)->format('l H:i')."\n";
    echo "It will re-open at ".$openingHours->nextOpen($now)->format('l H:i')."\n";
}
