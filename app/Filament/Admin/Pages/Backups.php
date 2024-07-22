<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use Filament\Facades\Filament;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    public function mount(): void
    {
        abort_unless(self::shouldRegisterNavigation(), 403);
    }

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('System');
    }

    #[\Override]
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->isSuperAdmin() ?? false;
    }
}
