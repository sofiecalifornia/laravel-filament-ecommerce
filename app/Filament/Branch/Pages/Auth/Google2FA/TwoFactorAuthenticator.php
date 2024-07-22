<?php

declare(strict_types=1);

namespace App\Filament\Branch\Pages\Auth\Google2FA;

use Filament\Actions\Action;

class TwoFactorAuthenticator extends \App\Filament\Admin\Pages\Auth\Google2FA\TwoFactorAuthenticator
{
    #[\Override]
    public function recoveryAction(): Action
    {
        return parent::recoveryAction()
            ->url(route('filament.branch.auth.google-2fa.recovery'));
    }
}
