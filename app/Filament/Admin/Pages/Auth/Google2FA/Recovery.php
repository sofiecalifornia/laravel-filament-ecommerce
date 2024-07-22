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
use Support\Google2FA\Actions\VerifyRecoveryCodeAction;
use Support\Google2FA\Authenticator;

/**
 * @property Form $form
 */
class Recovery extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static string $view = 'filament.pages.auth.google-2fa.recovery';

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

        $recoveryCode = $this->form->getState()['recovery_code'];

        /** @var Admin $admin */
        $admin = Filament::auth()->user();

        $verified = DB::transaction(
            fn () => app(VerifyRecoveryCodeAction::class)
                ->execute($admin, $recoveryCode)
        );

        if (! $verified) {
            throw ValidationException::withMessages([
                'data.recovery_code' => trans('Invalid recovery code.'),
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
                        TextInput::make('recovery_code')
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

    public function otpAction(): Action
    {
        return Action::make('otp')
            ->link()
            ->label(trans('use OTP'))
            ->url(route('filament.admin.auth.google-2fa.otp'));
    }
}
