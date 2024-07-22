<?php

declare(strict_types=1);

namespace App\Filament\Branch\Pages\Auth\Google2FA;

use Filament\Actions\Action;

class Recovery extends \App\Filament\Admin\Pages\Auth\Google2FA\Recovery
{
    #[\Override]
    public function otpAction(): Action
    {
        return parent::otpAction()
            ->url(route('filament.branch.auth.google-2fa.otp'));
    }
}
