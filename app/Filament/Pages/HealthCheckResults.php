<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults as BaseHealthCheckResults;

class HealthCheckResults extends BaseHealthCheckResults
{
    public function mount(): void
    {
        abort_unless(self::shouldRegisterNavigation(), 403);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('System');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->isSuperAdmin() ?? false;
    }
}
