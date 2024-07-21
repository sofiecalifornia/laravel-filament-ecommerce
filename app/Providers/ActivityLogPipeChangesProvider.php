<?php

declare(strict_types=1);

namespace App\Providers;

use App\Providers\ActivitylogLoggablePipes\RedactHiddenAttributesFromLogChangesPipe;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Support\ServiceProvider;

/** @property \Illuminate\Foundation\Application $app */
class ActivityLogPipeChangesProvider extends ServiceProvider
{
    public function register(): void
    {
        Admin::addLogChange(new RedactHiddenAttributesFromLogChangesPipe());
        Customer::addLogChange(new RedactHiddenAttributesFromLogChangesPipe());
    }
}
