<?php

declare(strict_types=1);

namespace Support\Google2FA;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FALaravel\Listeners\LoginViaRemember;
use PragmaRX\Google2FALaravel\Support\Authenticator as AuthenticatorBase;

class Google2FAServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(AuthenticatorBase::class, Authenticator::class);

        Event::listen(Login::class, LoginViaRemember::class);
    }
}
