<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Shop\ProductResource\RelationManagers\SkusRelationManager;
use App\Filament\Resources\Shop\ProductResource\Schema\ProductSkuSchema;
use App\Filament\Support\TenantHelper;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Enums\Status;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Stock\Enums\StockType;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

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

                                        Forms\Components\Section::make()
                                            ->schema([

                                                Forms\Components\TextInput::make('name')
                                                    ->translateLabel()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->afterStateUpdated(
                                                        function (Forms\Set $set, string $state, ?Product $record): void {
                                                            if ($record !== null) {
                                                                return;
                                                            }
                                                            $set(
                                                                'parent_sku',
                                                                Str::kebab($state)
                                                            );
                                                        }
                                                    )
                                                    ->live(onBlur: true),

                                                Forms\Components\TextInput::make('parent_sku')
                                                    ->translateLabel()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->alphaDash()
                                                    ->helperText(trans('Only letters, numbers, dashes and underscores are allowed')),

                                                Forms\Components\RichEditor::make('description')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->string()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                        Forms\Components\Section::make(trans('Images'))
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('image')
                                                    ->hiddenLabel()
                                                    ->collection('image')
                                                    ->disk(config('media-library.disk_name'))
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->maxFiles(9),
                                            ])
                                            ->collapsible(),

                                    ])
                                    ->columnSpan(['lg' => 2]),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make(trans('Status'))
                                            ->schema([

                                                Forms\Components\Radio::make('status')
                                                    ->translateLabel()
                                                    ->required()
                                                    ->optionsFromEnum(Status::class),

                                            ]),

                                        Forms\Components\Section::make(trans('Associations'))
                                            ->schema([

                                                Forms\Components\Select::make('category_id')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->relationship(
                                                        'category',
                                                        'name',
                                                        function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
                                                            /** @var \Domain\Shop\Category\Models\Category $query */
                                                            $query->whereChild();
                                                        }
                                                    )
                                                    ->getOptionLabelFromRecordUsing(
                                                        fn (Category $record) => $record->name_with_parent
                                                    )
                                                    ->searchable()
                                                    ->preload(),

                                                Forms\Components\Select::make('brand_id')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->relationship('brand', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                            ]),

                                        Forms\Components\Section::make()
                                            ->schema([
                                                Forms\Components\Placeholder::make('created_at')
                                                    ->translateLabel()
                                                    ->content(fn (Product $record): ?string => $record->created_at?->diffForHumans()),

                                                Forms\Components\Placeholder::make('updated_at')
                                                    ->translateLabel()
                                                    ->content(fn (Product $record): ?string => $record->updated_at?->diffForHumans()),
                                            ])
                                            ->columnSpan(['lg' => 1])
                                            ->hiddenOn('create'),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])->columns(3),

                        Tabs\Tab::make(trans('Skus'))
                            ->schema([

                                Forms\Components\Section::make()
                                    ->schema([

                                        Forms\Components\Repeater::make('skus')
                                            ->translateLabel()
                                            ->itemLabel(fn (array $state): ?string => $state['code'] ?? null)
                                            ->relationship()
                                            ->collapsible()
                                            ->cloneable()
                                            ->orderColumn(config('eloquent-sortable.order_column_name'))
                                            ->reorderableWithButtons()
                                            ->schema(ProductSkuSchema::schema())
                                            ->grid(2),

                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),

            ]);
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

                Tables\Columns\TextColumn::make('parent_sku')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->translateLabel()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn (Product $record) => $record->category?->name_with_parent
                    ),

                Tables\Columns\TextColumn::make('brand.name')
                    ->translateLabel()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('skus.code')
                    ->label('Sku code')
                    ->translateLabel()
                    ->bulleted()
                    ->searchable(isIndividual: true)
                    ->toggleable()
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('skus.price')
                    ->label('Sku price')
                    ->translateLabel()
                    ->bulleted()
                    ->searchable()
                    ->toggleable()
                    ->money(),

                // Object of class Domain\Shop\Product\Enums\Status could not be converted to string
                // Tables\Columns\SelectColumn::make('status')
                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

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

                Tables\Filters\SelectFilter::make('category')
                    ->translateLabel()
                    ->relationship(
                        'category',
                        'name',
                        fn (Builder $query) =>
                        /** @var \Domain\Shop\Category\Models\Category $query */
                        /** @phpstan-ignore-next-line  */
                        $query->whereChild()
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Category $record) => $record->name_with_parent
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('brand')
                    ->translateLabel()
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

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
                            fn (Product $record): string => route('api.products.show', [
                                'product' => $record,
                                'include' => implode(',', [
                                    'brand.media',
                                    'media',
                                    'skus.attributeOptions.attribute',
                                    'skus.media',
                                    'skus.skuStocks',
                                    'category.parent',
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
                    ->url(fn (): string => route(
                        'api.products.index', [
                            'include' => implode(',', [
                                'brand.media',
                                'media',
                                'skus.attributeOptions.attribute',
                                'skus.media',
                                'skus.skuStocks',
                                'category.parent',
                            ]),
                            //                            'filter' => [
                            //                                'skus.skuStocks.branch.slug' => TenantHelper::getBranch()?->getRouteKey(),
                            //                            ],
                        ]), shouldOpenInNewTab: true),
            ])
            ->defaultSort(config('eloquent-sortable.order_column_name'))
            ->reorderable(config('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Product $record) => $record->category?->name_with_parent),
                'brand.name',
                'status',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
            SkusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ProductResource\Pages\ListProducts::route('/'),
            'create' => ProductResource\Pages\CreateProduct::route('/create'),
            'edit' => ProductResource\Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'brand.name',
            'skus.code',
            'parent_sku',
            'name',
        ];
    }

    /** @return array<string, string|\Illuminate\Support\HtmlString> */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Shop\Product\Models\Product $record */
        return [
            'Brand' => $record->brand?->name ?? new HtmlString('&mdash;'),
            //            'Category' => $record->category->name,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Product::whereRelation('skus', function (Builder $query) {
            /** @var \Domain\Shop\Product\Models\Sku|\Illuminate\Database\Eloquent\Builder $query */
            $query->whereRelation('skuStocks', function (Builder $query) {
                /** @var \Domain\Shop\Stock\Models\SkuStock|\Illuminate\Database\Eloquent\Builder $query */
                $query->where('type', StockType::UNLIMITED)
                    ->orWhere(function (Builder $query) {
                        $query->where('type', StockType::BASE_ON_STOCK)
                            ->where('count', '>=', 0);
                    });
            });
        })->count();
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Product\Models\Product> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
