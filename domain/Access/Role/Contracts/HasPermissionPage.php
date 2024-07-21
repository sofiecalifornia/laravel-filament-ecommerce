<?php

declare(strict_types=1);

namespace Domain\Access\Role\Contracts;

interface HasPermissionPage
{
    public static function canBeSeed(): bool;
}
