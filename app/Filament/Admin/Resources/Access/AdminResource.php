<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActionsRelationManager;
use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use Closure;
use Domain\Access\Admin\Actions\UpdateAdminPasswordAction;
use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\Models\Role;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lab404\Impersonate\Services\ImpersonateManager;
use Lloricode\Timezone\Timezone;
use Spatie\Permission\PermissionRegistrar;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Support\Google2FA\Actions\GenerateGoogle2FASecretAction;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->required()
                            ->email()
                            ->rule(fn () => Rule::email())
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit'),

                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('password')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->nullable()
                                ->rule(Password::default())
                                ->confirmed(),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->dehydrated(false),
                        ])
                            ->visibleOn('create'),

                        Forms\Components\Select::make('roles')
                            ->translateLabel()
                            ->relationship('roles', 'name', function (Builder $query) {
                                if (! (Filament::auth()->user()?->isSuperAdmin() ?? true)) {
                                    $query
                                        ->where('name', '!=', config('domain.access.role.super_admin'));
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists(
                                table: app(PermissionRegistrar::class)->getRoleClass(),
                                column: 'uuid'
                            )
                            ->rule(fn () => function (string $attribute, string|array $value, Closure $fail): void {

                                // work around fixes with current filament v3.0.45
                                if (is_array($value)) {
                                    $value = $value[0] ?? null;

                                    if (null === $value) {
                                        return;
                                    }
                                }

                                /** @var \Domain\Access\Role\Models\Role $superAdmin */
                                $superAdmin = Role::findByName(config('domain.access.role.super_admin'), 'admin');
                                if (
                                    ! (Filament::auth()->user()?->isSuperAdmin() ?? true) &&
                                    $value === $superAdmin->getKey()
                                ) {
                                    $fail(trans('Not allowed to create [:role] when your not [:role].', [
                                        'role' => $superAdmin->name,
                                    ]));
                                }
                            })
                            ->disabled(function (?Admin $record): bool {
                                if (Filament::auth()->user()?->isSuperAdmin() ?? true) {
                                    return false;
                                }

                                return $record?->isSuperAdmin() ?? true;
                            })
                            // error:
                            // Add fillable property [roles]
                            // to allow mass assignment on [Domain\Access\Admin\Models\Admin].
                            ->dehydrated(false),

                        Forms\Components\Select::make('timezone')
                            ->translateLabel()
                            ->options(Timezone::generateList())
                            ->required()
                            ->rule('timezone')
                            ->searchable()
                            ->default(config('app-default.timezone')),

                        Forms\Components\Select::make('branches')
                            ->translateLabel()
                            ->relationship('branches', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(fn () => trans('Add access to branch panel.')),
                    ])
                    ->columnSpan(['lg' => fn (?Admin $record) => null === $record ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (Admin $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (Admin $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),
            ])
            ->columns(3);
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->translateLabel()
                    ->getStateUsing(
                        fn (Admin $record) => $record->getFilamentAvatarUrl()
                    )
                    ->circular()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->color(fn (Admin $record) => $record->hasVerifiedEmail() ? 'success' : 'danger')
                    ->tooltip(fn (Admin $record) => $record->hasVerifiedEmail()
                        ? trans('Verified Email')
                        : trans('Not Verified Email')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('branches.name')
                    ->translateLabel()
                    ->badge()
                    ->toggleable()
//                    ->listWithLineBreaks()
//                    ->bulleted()
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel()
                    ->badge()
                    ->toggleable()
//                    ->listWithLineBreaks()
//                    ->bulleted()
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\IconColumn::make('google2fa_secret')
                    ->label(trans('Has google 2FA'))
                    ->boolean()
                    ->default(false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->translateLabel()
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label(trans('Email Verified'))
                    ->nullable(),

                Tables\Filters\TernaryFilter::make('google2fa_secret')
                    ->translateLabel()
                    ->nullable(),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),

                    Tables\Actions\Action::make('google2fa_secret')
                        ->label(trans('Enable google 2FA'))
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->successNotificationTitle(trans('Google 2FA secret key generated successfully.'))
                        ->visible(fn (Admin $record): bool => ! $record->google2faEnabled())
                        ->action(function (Admin $record, Tables\Actions\Action $action) {
                            app(GenerateGoogle2FASecretAction::class)->execute($record);
                            $action->success();
                        })
                        ->authorize('generateGoogleTwoFactorAuthenticatorSecretKey'),

                    Tables\Actions\Action::make('changePassword')
                        ->translateLabel()
                        ->icon('heroicon-o-lock-closed')
                        ->form([
                            Forms\Components\TextInput::make('new_password')
                                ->translateLabel()
                                ->required()
                                ->password()
                                ->revealable()
                                ->confirmed()
                                ->rule(Password::default()),
                            Forms\Components\TextInput::make('new_password_confirmation')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->required(),
                        ])
                        ->action(function (Admin $record, array $data) {
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
                        ->authorize('updatePassword'),

                    Tables\Actions\Action::make('resend-verification')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-envelope')
                        ->visible(fn (Admin $record) => ! $record->hasVerifiedEmail())
                        ->action(function (Admin $record, Tables\Actions\Action $action): void {
                            try {
                                VerifyEmail::$createUrlCallback = fn (MustVerifyEmail $notifiable) => Filament::getVerifyEmailUrl($notifiable);
                                $record->sendEmailVerificationNotification();
                                $action
                                    ->successNotificationTitle(trans('A fresh verification link has been sent to your email address.'))
                                    ->success();
                            } catch (Exception $exception) {
                                report($exception);
                                $action->failureNotificationTitle(trans('Failed to send verification link.'))
                                    ->failure();
                            }
                        })
                        ->authorize('resendEmailVerification'),

                    Impersonate::make()
                        ->translateLabel()
                        ->grouped()
                        ->backTo(self::getUrl())
                        ->redirectTo(function (Admin $record, Impersonate $action) {
                            if ($record->isBranch()) {
                                $branch = $record->branches->first();

                                if (filled($branch)) {
                                    return route('filament.branch.pages.main-dashboard', $branch);
                                }

                                app(ImpersonateManager::class)->leave();

                                Notification::make()
                                    ->title(trans('Admin has no branch attached.'))
                                    ->danger()
                                    ->send();

                                $action->halt(shouldRollBackDatabaseTransaction: true);
                            }

                            return config('filament-impersonate.redirect_to');
                        })
                        ->authorize('impersonate'),
                ]),
            ])
            ->deferFilters()
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'roles.name',
                Tables\Grouping\Group::make('email_verified_at')
                    ->date()
                    ->collapsible(),
            ]);
    }

    #[\Override]
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
        ];
    }

    #[\Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Access\Admin\Models\Admin $record */
        return [
            'Roles' => $record->getRoleNames()->implode(','),
            'Branches' => $record->branches->implode('name', ','),
        ];
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
            ActionsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => AdminResource\Pages\ListAdmins::route('/'),
            'create' => AdminResource\Pages\CreateAdmin::route('/create'),
            'edit' => AdminResource\Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Access\Admin\Models\Admin> */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
