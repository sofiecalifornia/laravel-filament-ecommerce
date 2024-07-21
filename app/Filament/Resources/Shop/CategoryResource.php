<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Shop\CategoryResource\Pages;
use Domain\Shop\Category\Models\Category;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxValue(255),

                        Forms\Components\Select::make('parent_id')
                            ->translateLabel()
                            ->relationship(
                                'parent',
                                'name',
                                fn (Builder $query) =>
                                /** @var \Domain\Shop\Category\Models\Category $query */
                                $query->whereParent()
                            )
                            ->searchable()
                            ->preload(),

                        Forms\Components\Toggle::make('is_visible')
                            ->translateLabel()
                            ->default(true),

                        Forms\Components\RichEditor::make('description')
                            ->translateLabel()
                            ->nullable()
                            ->string(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->collection('image')
                            ->disk(config('media-library.disk_name')),
                    ])
                    ->columnSpan(['lg' => fn (?Category $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (Category $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (Category $record): ?string => $record->updated_at?->diffForHumans()),
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
                Tables\Columns\TextColumn::make(config('eloquent-sortable.order_column_name'))
                    ->label('#')
                    ->translateLabel()
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\CheckboxColumn::make('is_visible')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->translateLabel()
                    ->counts('products')
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
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->translateLabel(),
                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),
                Tables\Actions\ActionGroup::make([
                    Action::make('api_link')
                        ->label(trans('API link'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(
                            fn (Category $record): string => route('api.categories.show', [
                                'category' => $record,
                                'include' => implode(',', [
                                    'media',
                                    'parent.media',

                                    'products.brand',
                                    'products.media',
                                    'products.skus',
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
                    ->url(fn (): string => route('api.categories.index', [
                        'include' => implode(',', [
                            'media',
                            'parent.media',

                            'products.brand',
                            'products.media',
                            'products.skus',
                        ]),
                    ]), shouldOpenInNewTab: true),
            ])
            ->defaultSort(config('eloquent-sortable.order_column_name'))
            ->reorderable(config('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                'parent.name',
                Tables\Grouping\Group::make('is_visible')
                    ->getTitleFromRecordUsing(
                        fn (Category $record) => $record->is_visible
                            ? trans('Yes')
                            : trans('No')
                    ),
            ])
            ->groups([
                'is_visible',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Category\Models\Category> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
