<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use Domain\Access\Admin\Models\Admin;
use PragmaRX\Google2FALaravel\Facade as Google2FAFacade;
use Support\Google2FA\Notifications\Google2FAGeneratedNotification;

final readonly class GenerateGoogle2FASecretAction
{
    public function __construct(private GenerateGoogle2FASecretQrCodeAction $generateGoogle2FASecretQrCodeAction)
    {
    }

    public function execute(Admin $admin): void
    {
        $admin->google2fa_secret = Google2FAFacade::generateSecretKey();

        $admin->save();

        $fileUrl = $this->generateGoogle2FASecretQrCodeAction->execute($admin);

        $admin->notify(new Google2FAGeneratedNotification($fileUrl));

    }
}
