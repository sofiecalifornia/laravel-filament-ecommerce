<?php

declare(strict_types=1);

namespace App\Providers;

use App\Policies\ActivityPolicy;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    public function boot(): void
    {
        /** @see https://freek.dev/1325-when-to-use-gateafter-in-laravel */
        Gate::after(fn ($user) => $user instanceof Admin ? $user->isSuperAdmin() : null);

        self::defineGates();
    }

    private static function defineGates(): void
    {
        // super admin only
        $superAdminOnly = fn (?Authenticatable $user): bool => $user instanceof Admin && $user->isSuperAdmin();

        Gate::define('viewLogViewer', $superAdminOnly);
        Gate::define('viewPulse', $superAdminOnly);
        Gate::define('download-backup', $superAdminOnly);
        Gate::define('delete-backup', $superAdminOnly);
        //        Gate::define('viewApiDocs', fn (Admin $user) => true); // allow public access
    }
}
