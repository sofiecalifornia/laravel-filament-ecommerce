<?php

declare(strict_types=1);

namespace Domain\Access\Role;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

trait ChecksWildcardPermissions
{
    protected function checkWildcardPermissions(User $user): bool
    {
        return $user->can($this->getResourceName().'.'.$this->getAbility());
    }

    private function getResourceName(): string
    {
        return (string) Str::of(static::class)
            ->classBasename()
            ->remove('Policy')
            ->camel();
    }

    private function getAbility(): string
    {
        $trace = debug_backtrace(limit: 3);

        return $trace[2]['function'];
    }
}
