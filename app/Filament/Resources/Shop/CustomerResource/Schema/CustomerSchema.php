<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\CustomerResource\Schema;

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
        $timezones = Timezone::generateList();

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
                ->translateLabel(),

            Forms\Components\TextInput::make('password')
                ->translateLabel()
                ->password()
                ->nullable()
                ->rules([Password::defaults(), 'confirmed']),

            Forms\Components\TextInput::make('password_confirmation')
                ->translateLabel()
                ->password()
                ->nullable()
                ->dehydrated(false),

            Forms\Components\Select::make('status')
                ->translateLabel()
                ->optionsFromEnum(Status::class)
                ->default(Status::ACTIVE)
                ->required(),

            Forms\Components\Select::make('gender')
                ->translateLabel()
                ->optionsFromEnum(Gender::class),

            Forms\Components\Select::make('timezone')
                ->translateLabel()
                ->options($timezones)
                ->required()
                ->in(array_keys($timezones))
                ->searchable()
                ->default('Asia/Manila'),

        ];
    }
}
