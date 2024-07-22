<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use Domain\Access\Admin\Models\Admin;
use PragmaRX\Google2FALaravel\Facade as Google2FAFacade;

class VerifyRecoveryCodeAction
{
    public function execute(Admin $admin, string $recoveryCode): bool
    {
        $recoveries = $admin
            ->googleTwoFactorRecoveryCodes()
            ->whereNull('used_at')
            ->get();

        foreach ($recoveries as $recovery) {
            if ($recovery->code === $recoveryCode) {

                $recovery->update([
                    'used_at' => now(),
                ]);
                Google2FAFacade::login();

                return true;
            }
        }

        return false;

    }
}
