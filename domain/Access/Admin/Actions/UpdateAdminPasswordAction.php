<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Actions;

use Domain\Access\Admin\Models\Admin;

final readonly class UpdateAdminPasswordAction
{
    public function execute(Admin $admin, string $password): bool
    {
        return $admin->update([
            'password' => $password,
        ]);
    }
}
