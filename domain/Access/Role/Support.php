<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Illuminate\Support\Str;

final class Support
{
    public const WIDGETS = 'widgets';

    public const PAGES = 'pages';

    private function __construct()
    {
    }

    /** @param  \Domain\Access\Role\Contracts\HasPermissionPage|class-string<\Domain\Access\Role\Contracts\HasPermissionPage>  $page */
    public static function getPagePermissionName(HasPermissionPage|string $page): string
    {
        return self::PAGES.'.'.Str::of($page)->classBasename()->camel();
    }

    /** @param  \Domain\Access\Role\Contracts\HasPermissionWidgets|class-string<\Domain\Access\Role\Contracts\HasPermissionWidgets>  $widget */
    public static function getWidgetPermissionName(HasPermissionWidgets|string $widget): string
    {
        return self::WIDGETS.'.'.Str::of($widget)->classBasename()->camel();
    }
}
