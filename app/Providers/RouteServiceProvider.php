<?php

declare(strict_types=1);

namespace App\Providers;

use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    #[\Override]
    public function boot(): void
    {

        Route::bind(
            'enabledBranch',
            fn (string $routeKey): Branch => Branch::query()
                ->where((new Branch())->getRouteKeyName(), $routeKey)
                ->where('status', Status::enabled)
                ->firstOrFail()
        );

    }
}
