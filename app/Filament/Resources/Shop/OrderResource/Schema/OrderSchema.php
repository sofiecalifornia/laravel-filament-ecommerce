<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\OrderResource\Schema;

use App\Filament\Resources\Shop\CustomerResource;
use App\Filament\Resources\Shop\OrderResource\Support;
use Domain\Shop\Branch\Enums\Status as BranchStatus;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\PaymentMethod;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status as OrderStatus;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Support\format_money;

final class OrderSchema
{
    private function __construct()
    {
    }

    public static function createForm(
        Form $form,
        Action $submitAction,
        Action $cancelAction,
        Branch $tenantBranch = null,
    ): Form {
        return $form->schema([
            Forms\Components\Hidden::make('total_price')
                ->dehydrateStateUsing(
                    fn (Forms\Get $get): float => Support::callCalculatorForTotalPrice($get('orderItems'))
                ),

            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Wizard::make([
                        Forms\Components\Wizard\Step::make(trans('Order Details'))
                            ->schema(OrderSchema::schemaDetails(tenantBranch: $tenantBranch))
                            ->columns(2),

                        Forms\Components\Wizard\Step::make(trans('Order Items'))
                            ->schema(OrderSchema::schemaItems(tenantBranch: $tenantBranch)),
                    ])
                        ->submitAction($submitAction)
                        ->cancelAction($cancelAction),
                ])
                ->columnSpan(['lg' => 3]),

            Forms\Components\Section::make()
                ->schema([OrderSchema::total()])
                ->columnSpan(['lg' => 1]),

        ])
            ->columns(4);
    }

    public static function schemaDetails(bool $hasCustomer = true, Branch $tenantBranch = null): array
    {
        return [

            Forms\Components\Select::make('customer_id')
                ->translateLabel()
                ->hidden(! $hasCustomer)
                ->required()
                ->searchable()
                ->preload()
                ->optionsLimit(20)
                ->getOptionLabelFromRecordUsing(
                    fn (Customer $record) => $record->full_name
                )
                ->relationship(
                    'customer',
                    'full_name',
                    fn (Builder $query) => $query->latest()
                )
                ->createOptionForm([
                    Forms\Components\Section::make(
                        CustomerResource\Schema\CustomerSchema::schema(),
                    )->columns(['sm' => 2]),
                ]),

            Forms\Components\Select::make('branch_id')
                ->translateLabel()
                ->relationship(
                    'branch',
                    'name',
                    fn (Builder $query) => $query
                        ->where('status', BranchStatus::ENABLED)
                )
                ->visible($tenantBranch === null)
                ->searchable()
                ->preload()
                ->required()
                ->reactive(),

            Forms\Components\Select::make('payment_method')
                ->translateLabel()
                ->optionsFromEnum(PaymentMethod::class)
                ->nullable(),

            Forms\Components\Select::make('payment_status')
                ->translateLabel()
                ->optionsFromEnum(PaymentStatus::class)
                ->required()
                ->default(PaymentStatus::PENDING),

            Forms\Components\Select::make('status')
                ->translateLabel()
                ->optionsFromEnum(OrderStatus::class)
                ->required()
                ->default(OrderStatus::PENDING),

            Forms\Components\MarkdownEditor::make('notes')
                ->translateLabel()
                ->nullable()
                ->columnSpanFull(),
        ];
    }

    public static function schemaItems(Branch $tenantBranch = null): array
    {
        return [
            Forms\Components\Repeater::make('orderItems')
                ->translateLabel()
                ->required()
                ->relationship('orderItems')
                ->schema(fn () => self::inputItems(tenantBranch: $tenantBranch))
                ->columns(4),
        ];
    }

    private static function inputItems(Branch $tenantBranch = null): array
    {
        return [
            Forms\Components\Select::make('sku_id')
                ->translateLabel()
                ->relationship(
                    'sku',
                    'code',
                    fn (Forms\Get $get, Builder $query): Builder => $query
                        ->whereRelation(
                            'skuStocks.branch',
                            'id',
                            $tenantBranch?->getKey() ?? $get('../../branch_id')
                        )
                )
                ->preload()
                ->searchable()
                ->required()
                ->afterStateHydrated(
                    function (Forms\Set $set, ?int $state, ?OrderItem $record): void {

                        if ($record !== null) {
                            return;
                        }

                        $price = Sku::whereKey($state)->value('price');
                        $set('price', number_format($price / 100, 2));
                    }
                )
                ->afterStateUpdated(
                    function (Forms\Set $set, $state, ?OrderItem $record): void {

                        if ($record !== null) {
                            return;
                        }

                        $sku = Sku::whereKey($state)->first();

                        if ($sku === null) {
                            return;
                        }

                        $set('price', $sku->price);
                        $set('minimum', $sku->minimum);
                        $set('maximum', $sku->maximum);
                    }
                )
                ->reactive(),

            Forms\Components\TextInput::make('price')
                ->translateLabel()
                ->numeric()
                ->disabled()
                ->dehydrated(false),

            Forms\Components\TextInput::make('minimum')
                ->translateLabel()
                ->disabled()
                ->dehydrated(false),

            Forms\Components\Hidden::make('maximum')
                ->translateLabel()
                ->disabled()
                ->dehydrated(false),

            Forms\Components\TextInput::make('quantity')
                ->translateLabel()
                ->default(1)
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(9_999)
                ->rule(
                    fn (Forms\Get $get) => new CheckQuantitySkuStockRule(
                        /** @phpstan-ignore-next-line  */
                        branch: $tenantBranch ?? Branch::whereKey($get('../../branch_id'))->first(),
                        sku: $get('sku_id'),
                    )
                )
                ->reactive(),

            //            Forms\Components\TextInput::make('total')
            //                ->translateLabel()
            //                ->visibleOn('view')
            //                ->formatStateUsing(fn (?OrderItem $record) => $record === null
            //                    ? null
            //                    : number_format($record->total_price / 100, 2)),
        ];
    }

    public static function total(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('total_price_placeholder')
            ->label('Total price')
            ->translateLabel()
            ->content(
                fn (?Order $record, Forms\Get $get) => format_money(
                    $record?->total_price ?? Support::callCalculatorForTotalPrice($get('orderItems')),
                    Table::$defaultCurrency
                )
            );
    }
}
