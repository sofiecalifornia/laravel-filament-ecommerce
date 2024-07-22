<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\SkuStockResource\Schema;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Validation\Rules\Unique;

final class SkuStockSchema
{
    private function __construct()
    {
    }

    public static function form(Form $form, ?Branch $tenantBranch = null): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make()
                    ->schema(self::schema(tenantBranch: $tenantBranch))
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?SkuStock $record) => null === $record ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (SkuStock $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (SkuStock $record): ?string => $record->updated_at?->diffForHumans()),

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),
            ])
            ->columns(3);
    }

    public static function schema(bool $hasSku = true, ?Branch $tenantBranch = null): array
    {
        return [
            Forms\Components\Select::make('sku_uuid')
                ->translateLabel()
                ->relationship('sku', 'code')
                ->searchable()
                ->preload()
                ->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule
                        ->where(
                            'branch_uuid',
                            $tenantBranch?->getKey() ?? $get('branch_uuid')
                        )
                )
                ->validationMessages([
                    'unique' => fn ($get) => trans('The :attribute is already in stock with branch.'),
                ])
                ->disabledOn('edit')
                ->visible($hasSku),

            Forms\Components\Select::make('branch_uuid')
                ->translateLabel()
                ->relationship('branch', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn (?SkuStock $record) => null !== $record || null !== $tenantBranch)
                ->default($tenantBranch?->getKey()),

            Forms\Components\TextInput::make('count')
                ->translateLabel()
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->required(fn (Get $get) => StockType::base_on_stock->value === $get('type'))
                ->disabled(fn (Get $get) => StockType::base_on_stock->value !== $get('type'))
                ->helperText(trans('Required if type is base on stock.')),

            Forms\Components\TextInput::make('warning')
                ->translateLabel()
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->required(fn (Get $get) => StockType::base_on_stock->value === $get('type'))
                ->disabled(fn (Get $get) => StockType::base_on_stock->value !== $get('type'))
                ->helperText(trans('Get warning when reach the specified amount of count.')),

            Forms\Components\ToggleButtons::make('type')
                ->translateLabel()
                ->inline()
                ->options(StockType::class)
                ->enum(StockType::class)
                ->required()
                ->reactive()
                ->columnSpanFull(),
        ];
    }
}
