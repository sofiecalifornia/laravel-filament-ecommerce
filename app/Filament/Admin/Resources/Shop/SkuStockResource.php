<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use App\Settings\SkuStockSettings;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Domain\Shop\Stock\Models\SkuStock;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SkuStockResource extends Resource
{
    protected static ?string $model = SkuStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 7;

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        return SkuStockSchema::form($form);
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('sku.code')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('sku.product.name')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('branch.name')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->translateLabel()
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('count')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->color(
                        fn (SkuStock $record) => StockType::base_on_stock === $record->type
                            ? $record->isBaseOnStockWarning() ? 'danger' : 'success'
                            : null
                    )
                    ->tooltip(
                        fn (SkuStock $record) => StockType::base_on_stock === $record->type
                            ? $record->isBaseOnStockWarning() ? trans('Low stock warning') : null
                            : null
                    ),

                Tables\Columns\TextColumn::make('warning')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(
                        fn (SkuStock $record) => StockType::base_on_stock === $record->type
                            ? $record->warning
                            : 'n/a'
                    ),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch')
                    ->translateLabel()
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->translateLabel()
                    ->options(StockType::class),

                //                Tables\Filters\SelectFilter::make('sku.product')
                //                    ->translateLabel()
                //                    ->relationship('sku.product', 'name')
                //                    ->searchable()
                //                    ->preload(),

                Tables\Filters\TernaryFilter::make('has_base_on_stocks_warning')
                    ->translateLabel()
                    ->queries(
                        true: fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksIsWarning(),
                        false: fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksIsNotWarning(),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->deferFilters()
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'branch.name',
                'sku.product.name',
                'type',
            ]);
    }

    #[\Override]
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'sku.code', 'branch.name',
        ];
    }

    #[\Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var SkuStock $record */

        return [
            'Branch' => $record->branch->name,
            'Product' => $record->sku->product->name,
            'Sku code' => $record->sku->code,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'create' => SkuStockResource\Pages\CreateSkuStock::route('/create'),
            'index' => SkuStockResource\Pages\ListSkuStocks::route('/'),
            'edit' => SkuStockResource\Pages\EditSkuStock::route('/{record}/edit'),
        ];
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [ActivitiesRelationManager::class];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Stock\Models\SkuStock> */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withWhereHas('sku.product');
    }

    #[\Override]
    public static function getNavigationBadge(): ?string
    {
        $count = SkuStock::whereBaseOnStocksIsWarning()->count();

        if ($count > 0) {
            return (string) $count;
        }

        return null;
    }

    #[\Override]
    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = self::getNavigationBadge();

        if (null === $count) {
            return null;
        }

        return app(SkuStockSettings::class)->getColor((int) $count);
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        return trans('Low stock warning');
    }
}
