<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\ProductResource\Schema;

use App\Filament\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use Domain\Shop\Product\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Table;
use Illuminate\Support\Str;

final class ProductSkuSchema
{
    private function __construct()
    {
    }

    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->translateLabel()
                ->required()
                ->unique(ignoreRecord: true)
                ->alphaDash()
                ->helperText(trans('Only letters, numbers, dashes and underscores are allowed')),
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
                ->required()
                ->numeric()
                ->prefix(Str::upper(Table::$defaultCurrency)),

            Forms\Components\Repeater::make('attributeOptions')
                ->translateLabel()
                ->itemLabel(
                    fn (array $state): string => Attribute::whereKey($state['attribute_id'])
                        ->value('name').': '.$state['value']
                )
                ->relationship()
                ->collapsible()
                ->cloneable()
                ->orderColumn(config('eloquent-sortable.order_column_name'))
                ->reorderableWithButtons()
                ->maxItems(fn () => Attribute::count())
                ->schema([

                    Forms\Components\Select::make('attribute_id')
                        ->translateLabel()
                        ->required()
                        ->relationship('attribute', 'name')
                        ->preload()
                        ->searchable(),

                    Forms\Components\TextInput::make('value')
                        ->translateLabel()
                        ->required(),
                    //                        ->live(onBlur: true)
                    //                        ->afterStateUpdated(function (Forms\Get $get) {
                    //                            ray($get('value'));
                    //                        }),

                ])
                ->columns(2),

            Forms\Components\Repeater::make('skuStocks')
                ->translateLabel()
                ->itemLabel(fn (array $state): string => Str::headline($state['type'] ?? null))
                ->relationship()
                ->schema(SkuStockSchema::schema(hasSku: false))
                ->collapsible(),

            Forms\Components\Section::make(trans('Other fields'))
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

        ];
    }
}
