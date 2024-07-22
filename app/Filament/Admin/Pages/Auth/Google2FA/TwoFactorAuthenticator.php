<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth\Google2FA;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Domain\Access\Admin\Models\Admin;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Support\Google2FA\Actions\VerifyOTPAction;
use Support\Google2FA\Authenticator;

/**
 * @property Form $form
 */
class TwoFactorAuthenticator extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static string $view = 'filament.pages.auth.google-2fa.otp';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(Request $request): void
    {
        $autherticator = app(Authenticator::class)
            ->boot($request);

        /** @phpstan-ignore-next-line Call to an undefined method PragmaRX\Google2FALaravel\Google2FA::isAuthenticated(). */
        if ($autherticator->isAuthenticated()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    /**
     * @throws \Throwable
     */
    public function submit(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(trans('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(
                    trans('filament-panels::pages/auth/login.notifications.throttled.body', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ])
                )
                ->danger()
                ->send();

            return;
        }

        $otp = $this->form->getState()['one_time_password'];

        /** @var Admin $admin */
        $admin = Filament::auth()->user();

        $verified = DB::transaction(
            fn () => app(VerifyOTPAction::class)
                ->execute($admin, $otp)
        );

        if (! $verified) {
            throw ValidationException::withMessages([
                'data.one_time_password' => trans('Invalid one time password.'),
            ]);
        }

        redirect()->intended(Filament::getUrl());
    }

    #[\Override]
    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    #[\Override]
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('one_time_password')
                            ->translateLabel()
                            ->required(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->translateLabel()
                ->submit('submit'),
        ];
    }

    public function recoveryAction(): Action
    {
        return Action::make('recovery')
            ->link()
            ->label(trans('Recovery code'))
            ->url(route('filament.admin.auth.google-2fa.recovery'));
    }
}
