<?php

// https://github.com/spatie/opening-hours

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\Type;
use Illuminate\Support\Facades\Auth;

$admin = Admin::first(); /*?->timezone*/
Auth::setUser($admin);
Auth::check(); //?

Branch::whereCode('BRANCH_1')
//    ->with('operationHours')
    ->with('operationHoursOnline')
//    ->with('operationHoursInStore')
    ->first()/*?->code*/
    ->operationHoursHumanReadable(
        Type::online
//        Type::IN_STORE
    );
