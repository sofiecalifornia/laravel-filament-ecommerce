<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\AdminResource\Pages\Actions;

use Domain\Access\Admin\Actions\UpdateAdminPasswordAction;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;

class ChangePasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'changePassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->translateLabel()
            ->icon('heroicon-o-lock-closed')
            ->form([
                TextInput::make('new_password')
                    ->translateLabel()
                    ->password()
                    ->required()
                    ->confirmed()
                    ->rule(Password::default()),
                TextInput::make('new_password_confirmation')
                    ->translateLabel()
                    ->password(),
            ])
            ->action(function (array $data) {
                /** @phpstan-ignore-next-line */
                $record = $this->getLivewire()->record;
                app(UpdateAdminPasswordAction::class)
                    ->execute($record, $data['new_password'])
                    ? Notification::make()
                        ->title(trans(':value password updated successfully!', ['value' => $record->name]))
                        ->success()
                        ->send()
                    : Notification::make()
                        ->title(trans(':value password updated failed!', ['value' => $record->name]))
                        ->danger()
                        ->send();
            })
            ->authorize(
                fn () => Filament::auth()
                    ->user()
                    ?->can(
                        'updatePassword',
                        /** @phpstan-ignore-next-line */
                        $this->getLivewire()->record
                    ) ?? false
            );

    }
}
