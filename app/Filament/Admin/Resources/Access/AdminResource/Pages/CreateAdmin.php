<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\AdminResource\Pages;

use App\Filament\Admin\Resources\Access\AdminResource;
use Domain\Access\Admin\Models\Admin;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * @property-read Admin $record
 */
class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    public function afterCreate(): void
    {
        VerifyEmail::$createUrlCallback = fn (MustVerifyEmail $notifiable) => Filament::getVerifyEmailUrl($notifiable);
        $this->record->sendEmailVerificationNotification();
    }
}
