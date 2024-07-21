<?php

declare(strict_types=1);

namespace Tests\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

final class FakeUiAvatar implements AvatarProvider
{
    public function get(Model $user): string
    {
        return (string) $user->getKey();
    }
}
