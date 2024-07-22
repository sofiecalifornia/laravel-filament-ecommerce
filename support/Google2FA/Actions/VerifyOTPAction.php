<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use Domain\Access\Admin\Models\Admin;
use PragmaRX\Google2FALaravel\Facade as Google2FAFacade;

class VerifyOTPAction
{
    public function execute(Admin $admin, string $otp): bool
    {
        if (! $admin->google2faEnabled()) {
            throw new \LogicException('Must have google2fa_secret.');
        }

        /** @var int|false $result */
        $result = Google2FAFacade::verifyKeyNewer(
            $admin->google2fa_secret,
            $otp,
            $admin->google2fa_timestamp
        );

        if (false === $result) {
            return false;
        }

        $admin->google2fa_timestamp = $result;
        $admin->save();

        Google2FAFacade::login();

        return true;
    }
}
