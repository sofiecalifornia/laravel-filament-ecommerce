<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers;

use Domain\Shop\Product\Enums\AttributeFieldType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule
                            ->where(
                                'product_uuid',
                                $this->ownerRecord->getKey()
                            )
                    ),

                Forms\Components\Select::make('type')
                    ->translateLabel()
                    ->options(AttributeFieldType::class)
                    ->enum(AttributeFieldType::class)
                    ->required()
                    ->default(AttributeFieldType::text),

                Forms\Components\TextInput::make('prefix')
                    ->translateLabel()
                    ->disabled(fn (Forms\Get $get): bool => AttributeFieldType::color_picker === $get('type'))
                    ->nullable()
                    ->string()
                    ->maxLength(3),

                Forms\Components\TextInput::make('suffix')
                    ->translateLabel()
                    ->disabled(fn (Forms\Get $get): bool => AttributeFieldType::color_picker === $get('type'))
                    ->nullable()
                    ->string()
                    ->maxLength(3),
            ])
            ->columns(2);
    }

    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->translateLabel()
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('prefix')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('suffix')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->default(new HtmlString('&mdash;')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
