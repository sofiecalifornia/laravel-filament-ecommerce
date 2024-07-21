<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\SkuStockResource\Schema;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

final class SkuStockSchema
{
    private function __construct()
    {
    }

    public static function form(Form $form, Branch $tenantBranch = null): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema(self::schema(tenantBranch: $tenantBranch))
                    ->columns(2)
                    ->columnSpan([
                        'lg' => fn (?SkuStock $record) => $record === null ? 3 : 2,
                    ]),
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

    public static function schema(bool $hasSku = true, Branch $tenantBranch = null): array
    {
        return [
            Forms\Components\Select::make('sku_id')
                ->translateLabel()
                ->relationship('sku', 'code')
                ->searchable()
                ->preload()
                ->required()
//                ->unique(
//                    ignoreRecord: true,
//                    modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule
//                        ->where('branch_id', $get('branch_id'))
//                )
                ->rule(fn (Forms\Get $get, ?SkuStock $record) => function ($attribute, $value, $fail) use (
                    $tenantBranch,
                    $record,
                    $get,
                ) {

                    if ($record !== null) {
                        return;
                    }

                    $brandId = $tenantBranch?->getKey() ?? $get('branch_id');

                    if ($brandId === null) {
                        return;
                    }

                    $exist = SkuStock::query()
                        ->where('sku_id', $value)
                        ->where('branch_id', $brandId)
                        ->exists();

                    if ($exist) {
                        $fail('The selected sku is already in stock with branch.');
                    }

                })
                ->disabledOn('edit')
                ->visible($hasSku),

            Forms\Components\Select::make('branch_id')
                ->translateLabel()
                ->relationship('branch', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->disabledOn('edit')
                ->visible($tenantBranch === null),

            Forms\Components\Radio::make('type')
                ->translateLabel()
                ->optionsFromEnum(StockType::class)
                ->required()
                ->reactive()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('count')
                ->translateLabel()
                ->disabled(fn (Get $get) => StockType::BASE_ON_STOCK->value !== $get('type'))
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->rule(
                    fn (Get $get) => Rule::requiredIf(StockType::BASE_ON_STOCK->value === $get('type'))
                )
                ->helperText(trans('Required if type is base on stock.')),

            Forms\Components\TextInput::make('warning')
                ->translateLabel()
                ->disabled(fn (Get $get) => StockType::BASE_ON_STOCK->value !== $get('type'))
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->rule(
                    fn (Get $get) => Rule::requiredIf(StockType::BASE_ON_STOCK->value === $get('type'))
                )
                ->helperText(trans('Get warning when reach the specified amount of count.')),
        ];
    }
}
