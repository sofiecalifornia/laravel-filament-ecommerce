<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use Domain\Shop\Brand\Models\Brand;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';

    protected static ?int $navigationSort = 5;

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

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->unique(ignoreRecord: true),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->collection('image')
                            ->disk(config('media-library.disk_name'))
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5),
                    ])
                    ->columnSpan(['lg' => fn (?Brand $record) => null === $record ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (Brand $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (Brand $record): ?string => $record->updated_at?->diffForHumans()),
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
                Tables\Columns\TextColumn::make(config('eloquent-sortable.order_column_name'))
                    ->label('#')
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
                ]),
            ])
            ->deferFilters()
            ->defaultSort(config('eloquent-sortable.order_column_name'))
            ->reorderable(config('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering();
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => BrandResource\Pages\ListBrands::route('/'),
            'create' => BrandResource\Pages\CreateBrand::route('/create'),
            'edit' => BrandResource\Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Brand\Models\Brand> */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /** @return array<string, string|\Illuminate\Support\HtmlString|int> */
    #[\Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Shop\Brand\Models\Brand $record */
        return [
            'Product count' => $record->loadCount('products')->products_count ?? 0,
        ];
    }
}
