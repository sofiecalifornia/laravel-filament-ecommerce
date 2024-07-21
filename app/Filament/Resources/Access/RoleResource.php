<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access;

use App\Filament\Resources\Access;
use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Access\RoleResource\Schema\PermissionSchema;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RoleResource extends Resource
{
    public static function getModel(): string
    {
        return app(PermissionRegistrar::class)
            ->getRoleClass()::class;
    }

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    public static function form(Form $form): Form
    {
        $guards = collect([config('auth.defaults.guard')]);

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->string()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Select::make('guard_name')
                                    ->translateLabel()
                                    ->required()
                                    ->in($guards)
                                    ->options(
                                        $guards
                                            ->mapWithKeys(fn (string $guardName) => [$guardName => $guardName])
                                    )
                                    ->default(config('auth.defaults.guard'))
                                    ->reactive(),
                            ]),
                    ]),
                Forms\Components\Section::make(trans('Permissions'))
                    ->schema(fn (Get $get) => PermissionSchema::schema($get('guard_name'))),
            ])->columns(1);
    }

    /** @throws Exception */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->translateLabel()
                    ->badge()
                    ->counts('permissions')
                    ->colors(['success'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Access\RoleResource\Pages\ListRoles::route('/'),
            'create' => Access\RoleResource\Pages\CreateRole::route('/create'),
            'edit' => Access\RoleResource\Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }
}
