<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use Domain\Access\Admin\Models\Admin;

class DisableGoogle2FAAction
{
    public function execute(Admin $admin): void
    {
        $admin->google2fa_secret = null;
        $admin->google2fa_timestamp = null;
        $admin->save();

        $admin->googleTwoFactorRecoveryCodes()
            ->delete();
    }
}
