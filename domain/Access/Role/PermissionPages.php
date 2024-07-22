<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Filament\Facades\Filament;

trait PermissionPages
{
    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->can(Support::getPagePermissionName(static::class)) ?? false;
    }

    public static function canBeSeed(): bool
    {
        return true;
    }
}
