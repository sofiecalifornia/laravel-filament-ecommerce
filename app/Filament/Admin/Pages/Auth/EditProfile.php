<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use Domain\Access\Admin\Models\Admin;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Session;
use Lloricode\Timezone\Timezone;
use Support\Google2FA\Actions\DisableGoogle2FAAction;
use Support\Google2FA\Actions\GenerateGoogle2FARecoveryCodesAction;
use Support\Google2FA\Actions\GenerateGoogle2FASecretAction;
use Support\Google2FA\Models\GoogleTwoFactorRecoveryCode;

class EditProfile extends BaseEditProfile
{
    #[\Override]
    public function mount(): void
    {
        parent::mount();
        self::revealRecoveryCodes(false);
    }

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent()
                    ->disabled(fn () => $this->getAdmin()->isZeroDayAdmin()),
                $this->getEmailFormComponent()
                    ->disabled(),

                Forms\Components\Select::make('timezone')
                    ->translateLabel()
                    ->options(Timezone::generateList())
                    ->required()
                    ->rule('timezone')
                    ->searchable()
                    ->default(config('app-default.timezone')),

                Forms\Components\Repeater::make('googleTwoFactorRecoveryCodes')
                    ->label(trans('Recovery codes'))
                    ->relationship()
                    ->visible(
                        fn (Forms\Get $get) => $this->canOTP() &&
                            $this->getAdmin()->google2faEnabled() &&
                            self::isRevealRecoveryCodes() &&
                            $this->getAdmin()->googleTwoFactorRecoveryCodes->isNotEmpty()
                    )
                    ->simple(
                        Forms\Components\TextInput::make('code')
                            ->translateLabel()
                            ->disabled()
                            ->formatStateUsing(fn (GoogleTwoFactorRecoveryCode $record) => trans(':used - :code', [
                                'used' => $record->isUsed() ? 'Used' : 'Not Used',
                                'code' => $record->code,
                            ]))
                    )
                    ->addable(false)
                    ->deletable(false)
                    ->dehydrated(false),

            ]);
    }

    #[\Override]
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            ...($this->canOTP() ? $this->getOTPActions() : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getOTPActions(): array
    {
        return [
            Action::make('enable_2fa')
                ->translateLabel()
                ->icon('heroicon-m-key')
                ->visible(
                    fn () => ! $this->getAdmin()->google2faEnabled()
                )
                ->color('success')
                ->successNotificationTitle(trans('Google 2FA generated successfully!'))
                ->action(function (Action $action) {
                    app(GenerateGoogle2FASecretAction::class)
                        ->execute($this->getAdmin());

                    $action->success();
                })
                ->passwordConfirmationModalPrompt()
                ->withActivityLog(),

            Action::make('disable_2fa')
                ->translateLabel()
                ->icon('heroicon-m-key')
                ->visible(
                    fn () => $this->getAdmin()->google2faEnabled()
                )
                ->color('danger')
                ->successNotificationTitle(trans('Google 2FA disabled successfully!'))
                ->action(function (Action $action) {
                    app(DisableGoogle2FAAction::class)
                        ->execute($this->getAdmin());

                    $action->success();
                })
                ->passwordConfirmationModalPrompt()
                ->withActivityLog(),

            Action::make('generate_recovery_codes')
                ->translateLabel()
                ->icon('heroicon-m-key')
                ->successNotificationTitle(trans('Recovery code generated successfully!'))
                ->action(function (Action $action): void {
                    app(GenerateGoogle2FARecoveryCodesAction::class)
                        ->execute($this->getAdmin());

                    $action->success();
                })
                ->visible(
                    fn () => $this->getAdmin()->google2faEnabled() &&
                        $this->getAdmin()->googleTwoFactorRecoveryCodes->isEmpty()
                )
                ->passwordConfirmationModalPrompt()
                ->withActivityLog(),

            Action::make('reveal_recovery_codes')
                ->translateLabel()
                ->icon('heroicon-m-eye')
                ->successNotificationTitle(trans('Recovery code revealed!'))
                ->action(function (Action $action) {
                    self::revealRecoveryCodes();
                    $action->success();
                })
                ->visible(
                    fn (Forms\Get $get) => $this->getAdmin()->google2faEnabled() &&
                        ! self::isRevealRecoveryCodes() &&
                        $this->getAdmin()->googleTwoFactorRecoveryCodes->isNotEmpty()
                )
                ->passwordConfirmationModalPrompt()
                ->withActivityLog(),
        ];
    }

    private function getAdmin(): Admin
    {
        return once(function () {
            /** @var Admin $admin */
            $admin = $this->getUser();

            return $admin;
        });
    }

    private function canOTP(): bool
    {
        return once(
            fn () => $this->getAdmin()
                ->can('admin.manageSelfGoogleTwoFactorAuthenticator')
        );
    }

    private static function revealRecoveryCodes(bool $param = true): void
    {
        Session::put('reveal_recovery_codes', $param);
    }

    private static function isRevealRecoveryCodes(): bool
    {
        return Session::get('reveal_recovery_codes', false);
    }
}
