<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;

trait PermissionPages
{
    public function mount(): void
    {
        if (static::shouldRegisterNavigation()) {
            parent::mount();

            return;
        }

        /** @var string $title */
        $title = $this->getTitle();

        Notification::make()
            ->title(trans('You do not have permission to access `:value` page.', ['value' => $title]))
            ->warning()
            ->send();

        redirect(route('filament.admin.auth.login'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->can(Support::getPagePermissionName(static::class)) ?? false;
    }

    public static function canBeSeed(): bool
    {
        return true;
    }
}
