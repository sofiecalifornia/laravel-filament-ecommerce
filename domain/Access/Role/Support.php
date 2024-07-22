<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Str;

final class Support
{
    public const string PANELS = 'panels';

    public const string WIDGETS = 'widgets';

    public const string PAGES = 'pages';

    private function __construct()
    {
    }

    public static function getPanelPermissionName(string|Panel $panel): string
    {
        return once(
            /**
             * @throws \Exception
             */
            function () use ($panel) {
                if (is_string($panel)) {
                    $panelObject = Filament::getPanel($panel);

                    if ($panelObject->getId() !== $panel) {
                        throw new \Exception('Panel ['.$panel.'] not found.');
                    }

                } else {
                    $panelObject = $panel;
                }

                return self::PANELS.'.'.$panelObject->getId();
            }
        );
    }

    /** @param  \Domain\Access\Role\Contracts\HasPermissionPage|class-string<\Domain\Access\Role\Contracts\HasPermissionPage>  $page */
    public static function getPagePermissionName(HasPermissionPage|string $page): string
    {
        if ($page instanceof HasPermissionPage) {
            $page = $page::class;
        }

        return once(fn () => self::PAGES.'.'.Str::of($page)->classBasename()->camel());
    }

    /** @param  \Domain\Access\Role\Contracts\HasPermissionWidgets|class-string<\Domain\Access\Role\Contracts\HasPermissionWidgets>  $widget */
    public static function getWidgetPermissionName(HasPermissionWidgets|string $widget): string
    {
        if ($widget instanceof HasPermissionWidgets) {
            $widget = $widget::class;
        }

        return once(fn () => self::WIDGETS.'.'.Str::of($widget)->classBasename()->camel());
    }
}
