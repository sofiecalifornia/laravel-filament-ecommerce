<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers;

use App\Filament\Admin\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SkusRelationManager extends RelationManager
{
    protected static string $relationship = 'skus';

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->translateLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText(trans('Only letters, numbers, dashes and underscores are allowed'))
                            ->columnSpan(2),
                        //                ->disabled(fn (Forms\Get $get): bool => $get('auto_generate_code')),

                        //            Forms\Components\Toggle::make('auto_generate_code')
                        //                ->translateLabel()
                        //                ->dehydrated(false)
                        //                ->reactive()
                        //                ->helperText(trans('If enabled, the code will be generated automatically'))
                        //                ->afterStateHydrated(
                        //                    fn (Forms\Components\Toggle $component) => $component->state(true)
                        //                ),

                        Forms\Components\TextInput::make('price')
                            ->translateLabel()
                            ->money()
                            ->required()
                            ->numeric()
                            ->columnSpan(1),

                    ]),

                Forms\Components\Section::make(trans('Attribute options'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => 'edit' === $context)
                    ->schema([

                        Forms\Components\Repeater::make('attributeOptions')
                            ->translateLabel()
                            ->itemLabel(
                                fn (array $state): string => Attribute::whereKey($state['attribute_uuid'])
                                    ->value('name').': '.$state['value']
                            )
                            ->relationship()
                            ->collapsible()
                            ->collapsed(fn (string $context) => 'edit' === $context)
                            ->cloneable()
                            ->orderColumn(config('eloquent-sortable.order_column_name'))
                            ->reorderableWithButtons()
                            ->maxItems(fn () => Attribute::count())
                            ->schema([

                                Forms\Components\Select::make('attribute_uuid')
                                    ->translateLabel()
                                    ->required()
                                    ->relationship(
                                        'attribute',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo($this->ownerRecord)
                                    )
                                    ->preload()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                ...$this->typeField(),

                            ])
                            ->columns(2),

                    ]),

                Forms\Components\Section::make(trans('Stocks'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => 'edit' === $context)
                    ->schema([
                        Forms\Components\Repeater::make('skuStocks')
                            ->translateLabel()
                            ->itemLabel(fn (array $state): string => trans(':branch: :type', [
                                'branch' => Branch::whereKey($state['branch_uuid'])->value('name'),
                                'type' => Str::headline($state['type'] ?? null),
                            ]))
                            ->relationship()
                            ->schema(SkuStockSchema::schema(hasSku: false))
                            ->collapsible()
                            ->collapsed(fn (string $context) => 'edit' === $context),
                    ]),

                Forms\Components\Section::make(trans('Other fields'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => 'edit' === $context)
                    ->schema([
                        Forms\Components\TextInput::make('minimum')
                            ->translateLabel()
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),

                        Forms\Components\TextInput::make('maximum')
                            ->translateLabel()
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),
                    ]),

                Forms\Components\Section::make(trans('Images'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => 'edit' === $context)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->hiddenLabel()
                            ->collection('image')
                            ->disk(config('media-library.disk_name'))
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5),
                    ]),

            ])
            ->columns(1);
    }

    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                Tables\Columns\TextColumn::make('code')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('attributeOptionsList')
                    ->translateLabel()
                    ->bulleted(),

                Tables\Columns\TextColumn::make('price')
                    ->translateLabel()
                    ->sortable()
                    ->money(),

                Tables\Columns\TextColumn::make('attribute_options_count')
                    ->translateLabel()
                    ->counts('attributeOptions')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->translateLabel(),
                Tables\Actions\DeleteAction::make()
                    ->translateLabel(),
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

    /**
     * only one will visible
     */
    private function typeField(): array
    {
        return [
            Forms\Components\TextInput::make('value')
                ->translateLabel()
                ->prefix(
                    fn (Get $get): ?string => self::getAttributeModel($get)?->prefix
                )
                ->suffix(
                    fn (Get $get): ?string => self::getAttributeModel($get)?->suffix
                )
                ->visible(
                    fn (Get $get): bool => AttributeFieldType::text === self::getAttributeModel($get)?->type
                )
                ->required(),

            Forms\Components\TextInput::make('value')
                ->translateLabel()
                ->numeric()
                ->prefix(
                    fn (Get $get): ?string => self::getAttributeModel($get)?->prefix
                )
                ->suffix(
                    fn (Get $get): ?string => self::getAttributeModel($get)?->suffix
                )
                ->visible(
                    fn (Get $get): bool => AttributeFieldType::numeric === self::getAttributeModel($get)?->type
                )
                ->required(),

            Forms\Components\ColorPicker::make('value')
                ->translateLabel()
                ->visible(
                    fn (Get $get): bool => AttributeFieldType::color_picker === self::getAttributeModel($get)?->type
                )
                ->required(),

            Forms\Components\TextInput::make('value')
                ->translateLabel()
                ->disabled()
                ->visible(
                    fn (Get $get): bool => null === self::getAttributeModel($get)?->type
                ),
        ];
    }

    public static function getAttributeModel(Get $get): ?Attribute
    {
        return once(fn () => Attribute::whereKey($get('attribute_uuid'))->first());
    }
}
