<?php

declare(strict_types=1);

namespace Support\Google2FA;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use PragmaRX\Google2FALaravel\Support\Authenticator as AuthenticatorBase;

class Authenticator extends AuthenticatorBase
{
    #[\Override]
    protected function makeHtmlResponse($statusCode): mixed
    {
        Notification::make()
            ->title(trans('OTP required.'))
            ->danger()
            ->persistent()
            ->send();

        $panelId = Filament::getCurrentPanel()?->getId() ?? throw new \LogicException('Filament panel id not found.');

        return redirect()->route('filament.'.$panelId.'.auth.google-2fa.otp');
    }
}
