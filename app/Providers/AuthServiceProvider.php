<?php

declare(strict_types=1);

namespace App\Providers;

use App\Policies\ActivityPolicy;
use Domain\Access\Admin\Models\Admin;
use Filament\Facades\Filament;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;
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

        LogViewer::auth(fn ($request) => Filament::auth()->check() && Filament::auth()->user()?->isSuperAdmin());

        Gate::define('viewApiDocs', fn (Admin $user) => true); // allow public access
    }
}
