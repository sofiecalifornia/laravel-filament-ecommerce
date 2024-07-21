<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use Domain\Shop\Stock\Enums\StockType;
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

    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    public static function form(Form $form): Form
    {
        return SkuStockSchema::form($form);
    }

    /** @throws Exception */
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
                    ->toggleable(),

                Tables\Columns\TextColumn::make('warning')
                    ->translateLabel()
                    ->searchable()
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch')
                    ->translateLabel()
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->translateLabel()
                    ->optionsFromEnum(StockType::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'branch.name',
                'sku.product.name',
                'type',
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'sku.code', 'branch.name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var SkuStock $record */

        return [
            'Branch' => $record->branch->name,
            'Product' => $record->sku->product->name,
            'Sku code' => $record->sku->code,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => SkuStockResource\Pages\CreateSkuStock::route('/create'),
            'index' => SkuStockResource\Pages\ListSkuStocks::route('/'),
            'edit' => SkuStockResource\Pages\EditSkuStock::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [ActivitiesRelationManager::class];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Stock\Models\SkuStock> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withWhereHas('sku.product');
    }
}
