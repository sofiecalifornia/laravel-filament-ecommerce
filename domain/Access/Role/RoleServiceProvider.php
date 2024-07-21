<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/role.php', 'domain.access.role');
    }
}
