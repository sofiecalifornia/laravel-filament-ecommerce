<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access;

use App\Filament\Resources\Access;
use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Access\AdminResource\RelationManagers\CauserRelationManager;
use App\Filament\Resources\Access\AdminResource\RelationManagers\OrdersRelationManager;
use Closure;
use Domain\Access\Admin\Actions\UpdateAdminPasswordAction;
use Domain\Access\Admin\Models\Admin;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lab404\Impersonate\Services\ImpersonateManager;
use Lloricode\Timezone\Timezone;
use Spatie\Permission\PermissionRegistrar;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    public static function form(Form $form): Form
    {
        $timezones = Timezone::generateList();

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
                            ->unique(ignoreRecord: true),

                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('password')
                                ->translateLabel()
                                ->password()
                                ->nullable()
                                ->rule(Password::default())
                                ->confirmed(),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->translateLabel()
                                ->password()
                                ->dehydrated(false),
                        ])
                            ->visibleOn('create'),

                        Forms\Components\Select::make('branches')
                            ->translateLabel()
                            ->relationship('branches', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(fn () => trans('Add access to branch panel.')),

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
                                table: app(PermissionRegistrar::class)->getRoleClass()::class,
                                column: 'id'
                            )
                            ->rule(fn () => function (string $attribute, int|array $value, Closure $fail): void {

                                // work around fixes with current filament v3.0.45
                                if (is_array($value)) {
                                    $value = $value[0] ?? null;

                                    if ($value === null) {
                                        return;
                                    }
                                }

                                /** @var \Domain\Access\Role\Models\Role $superAdmin */
                                $superAdmin = app(PermissionRegistrar::class)->getRoleClass()
                                    ->findByName(config('domain.access.role.super_admin'), 'admin');
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
                            // Add fillable property [roles] to allow mass assignment on [Domain\Access\Admin\Models\Admin].
                            ->dehydrated(false),

                        Forms\Components\Select::make('timezone')
                            ->translateLabel()
                            ->options($timezones)
                            ->required()
                            ->in(array_keys($timezones))
                            ->searchable()
                            ->default('Asia/Manila'),
                    ])
                    ->columnSpan(['lg' => fn (?Admin $record) => $record === null ? 3 : 2]),

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

                Tables\Columns\TextColumn::make('orders_count')
                    ->translateLabel()
                    ->counts('orders')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Created by')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(new HtmlString('&mdash;')),

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

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                Impersonate::make()
                    ->translateLabel()
                    ->backTo(self::getUrl())
                    ->redirectTo(function (Admin $record, Impersonate $action) {
                        if ($record->isBranch()) {
                            $branch = $record->branches->first();

                            if (filled($branch)) {
                                return route('filament.branch.pages.dashboard', $branch);
                            }

                            app(ImpersonateManager::class)->leave();

                            Notification::make()
                                ->title(trans('Admin has no branch attached.'))
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        return config('filament-impersonate.redirect_to');
                    })
                    ->authorize('impersonate'),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),

                    Tables\Actions\Action::make('changePassword')
                        ->translateLabel()
                        ->icon('heroicon-o-lock-closed')
                        ->form([
                            Forms\Components\TextInput::make('new_password')
                                ->translateLabel()
                                ->required()
                                ->password()
                                ->confirmed()
                                ->rule(Password::default()),
                            Forms\Components\TextInput::make('new_password_confirmation')
                                ->translateLabel()
                                ->password()
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
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'roles.name',
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
        ];
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
            ActivitiesRelationManager::class,
            CauserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Access\AdminResource\Pages\ListAdmins::route('/'),
            'create' => Access\AdminResource\Pages\CreateAdmin::route('/create'),
            'edit' => Access\AdminResource\Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Access\Admin\Models\Admin> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
