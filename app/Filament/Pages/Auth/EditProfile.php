<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Lloricode\Timezone\Timezone;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        $timezones = Timezone::generateList();

        return $form
            ->schema([
                $this->getNameFormComponent()
                    ->disabled(fn () => Filament::auth()->user()?->isZeroDayAdmin() ?? true),
                $this->getEmailFormComponent()
                    ->disabled(),

                Forms\Components\Select::make('timezone')
                    ->translateLabel()
                    ->options($timezones)
                    ->required()
                    ->in(array_keys($timezones))
                    ->searchable()
                    ->default('Asia/Manila'),
            ]);
    }
}
