<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Support\Enums\MaxWidth;

/**
 * @mixin Action
 */
class FilamentActionMixin
{
    public function passwordConfirmationModalPrompt(): Closure
    {
        return fn (): Action => $this->modalWidth(MaxWidth::Small)
            ->modalDescription(trans('Please confirm your password.'))
            ->modalSubmitActionLabel(trans('Confirm password'))
            ->form([
                Forms\Components\TextInput::make('current_password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->currentPassword(),
            ]);
    }
}
