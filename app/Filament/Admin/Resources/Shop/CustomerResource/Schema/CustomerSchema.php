<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CustomerResource\Schema;

use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Enums\Status;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lloricode\Timezone\Timezone;

final class CustomerSchema
{
    private function __construct()
    {
    }

    public static function schema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('image')
                ->translateLabel()
                ->hiddenLabel()
                ->collection('image')
                ->disk(config('media-library.disk_name'))
                ->columnSpanFull(),

            Forms\Components\TextInput::make('first_name')
                ->translateLabel()
                ->required(),

            Forms\Components\TextInput::make('last_name')
                ->translateLabel()
                ->nullable(),

            Forms\Components\TextInput::make('email')
                ->translateLabel()
                ->nullable()
                ->unique(ignoreRecord: true)
                ->email()
                ->rule(fn () => Rule::email()),

            Forms\Components\TextInput::make('mobile')
                ->translateLabel()
                ->nullable()
                ->string(),

            Forms\Components\TextInput::make('landline')
                ->translateLabel()
                ->nullable()
                ->string(),

            Forms\Components\Select::make('timezone')
                ->translateLabel()
                ->options(Timezone::generateList())
                ->required()
                ->rule('timezone')
                ->searchable()
                ->default(config('app-default.timezone')),

            Forms\Components\TextInput::make('password')
                ->translateLabel()
                ->password()
                ->revealable()
                ->nullable()
                ->rules([Password::defaults(), 'confirmed']),

            Forms\Components\TextInput::make('password_confirmation')
                ->translateLabel()
                ->password()
                ->revealable()
                ->nullable()
                ->dehydrated(false),

            Forms\Components\ToggleButtons::make('status')
                ->translateLabel()
                ->options(Status::class)
                ->enum(Status::class)
                ->default(Status::active)
                ->inline()
                ->required(),

            Forms\Components\ToggleButtons::make('gender')
                ->translateLabel()
                ->inline()
                ->options(Gender::class)
                ->enum(Gender::class),
        ];
    }
}
