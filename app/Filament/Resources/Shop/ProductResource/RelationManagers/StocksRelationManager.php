<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\ProductResource\RelationManagers;

use App\Filament\Resources\Shop\SkuStockResource;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stock';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return SkuStockResource::form($form);
    }

    /** @throws Exception */
    public function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('type')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('count')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('warning')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                //                Tables\Actions\DeleteAction::make()
                //                    ->translateLabel(),
            ])
            ->bulkActions([
                //                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function canCreate(): bool
    {
        return false;
    }

    public function canDelete(Model $record): bool
    {
        return false;
    }
}
