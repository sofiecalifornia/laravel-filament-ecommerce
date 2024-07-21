<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Filament\Facades\Filament;

trait PermissionWidgets
{
    public static function canView(): bool
    {
        return Filament::auth()->user()?->can(Support::getWidgetPermissionName(static::class)) ?? false;
    }
}
