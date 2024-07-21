<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('country')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('street')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('city')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('state')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('zip')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('country')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('street')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('state')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('zip')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->translateLabel(),
            ]);
    }
}
