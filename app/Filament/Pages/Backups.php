<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
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
