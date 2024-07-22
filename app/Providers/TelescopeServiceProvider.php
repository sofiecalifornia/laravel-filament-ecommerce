<?php

declare(strict_types=1);

namespace App\Providers;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/** @codeCoverageIgnore  */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    #[\Override]
    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /** Prevent sensitive request details from being logged by Telescope. */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    #[\Override]
    protected function gate(): void
    {
        Gate::define('viewTelescope', fn (Admin $user): bool => $user->isSuperAdmin());
    }
}
