<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\BranchResource\Schema\BranchSchema;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\Type;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make()
                    ->tabs([

                        Tabs\Tab::make(trans('Main'))
                            ->schema([

                                Forms\Components\Group::make()
                                    ->schema([

                                        Forms\Components\Section::make([
                                            Forms\Components\TextInput::make('code')
                                                ->translateLabel()
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->minValue(3)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(
                                                    fn (Set $set, $state) => $set(
                                                        'code',
                                                        (string) Str::of($state)
                                                            ->upper()
                                                            ->replace(' ', '_')
                                                            ->trim()
                                                    )
                                                )
                                                ->alphaDash(),

                                            Forms\Components\TextInput::make('name')
                                                ->translateLabel()
                                                ->required()
                                                ->unique(ignoreRecord: true),
                                        ])
                                            ->columns(2),

                                        Forms\Components\Section::make()
                                            ->schema([
                                                Forms\Components\Textarea::make('address')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->columnSpanFull(),

                                                Forms\Components\TextInput::make('email')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->rule(Rule::email()),

                                                Forms\Components\TextInput::make('phone')
                                                    ->translateLabel()
                                                    ->nullable(),

                                                Forms\Components\TextInput::make('website')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->url()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                        Forms\Components\Section::make(trans('Images'))
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('image')
                                                    ->translateLabel()
                                                    ->collection('image')
                                                    ->disk(config('media-library.disk_name'))
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->maxFiles(5),

                                                SpatieMediaLibraryFileUpload::make('panel')
                                                    ->translateLabel()
                                                    ->collection('panel')
                                                    ->disk(config('media-library.disk_name')),
                                            ])
                                            ->collapsible(),

                                    ])
                                    ->columnSpan(['lg' => 2]),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make(trans('Status'))
                                            ->schema([

                                                Forms\Components\ToggleButtons::make('status')
                                                    ->translateLabel()
                                                    ->options(Status::class)
                                                    ->enum(Status::class)
                                                    ->required(),

                                                Forms\Components\Toggle::make('is_operation_hours_enabled')
                                                    ->label(trans('Operation hours enabled'))
                                                    ->translateLabel()
                                                    ->reactive(),

                                                Forms\Components\TextInput::make('maximum_advance_booking_days')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->minValue(0)
                                                    ->maxValue(60)
                                                    ->numeric(),
                                            ]),

                                        Forms\Components\Section::make(trans('Order Notification'))
                                            ->schema([

                                                Forms\Components\Select::make('admin_notify_receiver_id')
                                                    ->translateLabel()
                                                    ->multiple()
                                                    ->nullable()
                                                    ->relationship('adminNotifications', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->optionsLimit(50)
                                                    ->helperText(trans('If not specified, order setting will be used.')),
                                            ]),

                                        Forms\Components\Section::make()
                                            ->schema([
                                                Forms\Components\Placeholder::make('created_at')
                                                    ->translateLabel()
                                                    ->content(fn (Branch $record): ?string => $record->created_at?->diffForHumans()),

                                                Forms\Components\Placeholder::make('updated_at')
                                                    ->translateLabel()
                                                    ->content(fn (Branch $record): ?string => $record->updated_at?->diffForHumans()),
                                            ])
                                            ->hiddenOn('create'),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                            ])->columns(3),

                        Tabs\Tab::make(trans('Operation Hours Online'))
                            ->visible(fn (Forms\Get $get) => $get('is_operation_hours_enabled'))
                            ->schema(BranchSchema::operationHourSchema(Type::online)),

                        Tabs\Tab::make(trans('Operation Hours in Store'))
                            ->visible(fn (Forms\Get $get) => $get('is_operation_hours_enabled'))
                            ->schema(BranchSchema::operationHourSchema(Type::in_store)),

                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),

            ]);
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                SpatieMediaLibraryImageColumn::make('panel_image')
                    ->translateLabel()
                    ->collection('panel')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                Tables\Columns\TextColumn::make('code')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('operationHoursOnline')
                    ->translateLabel()
                    ->state(function (Branch $record) {
                        if ($record->is_operation_hours_enabled) {
                            return $record->operationHoursHumanReadable(Type::online);
                        }

                        return trans('Disabled feature.');
                    })
                    ->bulleted(
                        fn (Branch $record, array|string $state) => is_array($state) &&
                            $record->is_operation_hours_enabled
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('operationHoursInStore')
                    ->translateLabel()
                    ->state(function (Branch $record) {
                        if ($record->is_operation_hours_enabled) {
                            return $record->operationHoursHumanReadable(Type::in_store);
                        }

                        return trans('Disabled feature.');
                    })
                    ->bulleted(
                        fn (Branch $record, array|string $state) => is_array($state) &&
                            $record->is_operation_hours_enabled
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('maximum_advance_booking_days')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

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

                Tables\Filters\SelectFilter::make('status')
                    ->translateLabel()
                    ->options(Status::class),

                //                Tables\Filters\TernaryFilter::make('operation_hours_feature')
                //                    ->translateLabel()
                //                    ->trueLabel(trans('Enabled'))
                //                    ->falseLabel(trans('Disabled'))
                //                    ->queries(
                //                        true: fn (Builder $query) => $query->withTrashed(),
                //                        false: fn (Builder $query) => $query->onlyTrashed(),
                //                    ),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Action::make('panel_dashboard')
                    ->translateLabel()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(
                        fn (Branch $record): string => route('filament.branch.pages.main-dashboard', $record),
                        shouldOpenInNewTab: true
                    )
                    ->visible(
                        fn (Branch $record): bool => Filament::auth()->user()
                            ?->canAccessTenant($record) ?? false
                    ),

                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                Tables\Actions\ActionGroup::make([
                    Action::make('api_link')
                        ->label(trans('API link'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(
                            fn (Branch $record): string => route('api.branches.show', [
                                'branch' => $record,
                                'include' => implode(',', [
                                    'media',
                                ]),
                            ]),
                            shouldOpenInNewTab: true
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('api_link')
                    ->label(trans('API link'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (): string => route('api.branches.index', [
                        'include' => implode(',', [
                            'media',
                        ]),
                    ]), shouldOpenInNewTab: true),
            ])
            ->deferFilters()
            ->defaultSort(config('eloquent-sortable.order_column_name'))
            ->reorderable(config('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                'status',
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => BranchResource\Pages\ListBranches::route('/'),
            'create' => BranchResource\Pages\CreateBranch::route('/create'),
            'edit' => BranchResource\Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    #[\Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Branch\Models\Branch> */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['operationHoursOnline', 'operationHoursInStore']);
    }
}
