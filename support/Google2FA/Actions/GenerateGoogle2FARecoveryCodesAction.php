<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use Domain\Access\Admin\Models\Admin;
use PragmaRX\Recovery\Recovery;

class GenerateGoogle2FARecoveryCodesAction
{
    /**
     * @throws \Exception
     */
    public function execute(Admin $admin): void
    {
        foreach ((new Recovery())->toArray() as $code) {
            $admin->googleTwoFactorRecoveryCodes()
                ->create([
                    'code' => $code,
                ]);
        }

    }
}
