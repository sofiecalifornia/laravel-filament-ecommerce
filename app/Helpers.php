<?php

declare(strict_types=1);

namespace App;

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Support\Facades\Auth;
use LogicException;

final class Helpers
{
    private function __construct()
    {
    }

    public static function getCurrentAuthDriver(): ?string
    {
        if (Auth::guest()) {
            return null;
        }

        /** @var \Domain\Access\Admin\Models\Admin|\Domain\Shop\Customer\Models\Customer $user */
        $user = Auth::user();

        return match ($user::class) {
            Customer::class => 'customer',
            Admin::class => 'admin',
            default => throw new LogicException('Unknown user class')
        };
    }
}
