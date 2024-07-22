<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers\OrdersRelationManager as CustomerOrdersRelationManager;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ListOrders;
use App\Filament\Admin\Resources\Shop\OrderResource\RelationManagers\OrderInvoicesRelationManager;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\PaymentMethod;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Exports\OrderExporter;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Builder as IlluminateQueryBuilder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'receipt_number';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        $countSummarize = fn (string $column, array $enumCases): array => collect($enumCases)->map(
            fn ($enum) => Tables\Columns\Summarizers\Count::make($enum->value)
                ->label(trans(Str::headline($enum->value)))
                ->query(fn (IlluminateQueryBuilder $query) => $query
                    ->where($column, $enum))
        )->toArray();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.full_name')
                    ->translateLabel()
                    ->description(fn (Order $record) => $record->customer->email)
                    ->visibleOn([
                        ListOrders::class,
                    ])
                    ->searchable(['first_name', 'last_name'], isIndividual: true)
                    ->sortable(['first_name', 'last_name'])
//                    ->url(
//                        fn (Order $record) => CustomerResource::canView($record->customer)
//                            ? CustomerResource::getUrl('edit', [$record->customer])
//                            : null,
//                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_price')
                    ->translateLabel()
                    ->money()
                    ->sortable()
                    ->summarize([
                        Average::make()
                            ->translateLabel()
                            ->money(divideBy: 100),
                        // "filament/filament": "^3.2.36",
                        // Filament\Tables\Columns\Summarizers\Summarizer::Filament\Tables\Columns\Summarizers\Concerns\{closure}(): Return value must be of type ?string, array returned
                        //                        Range::make()
                        //                            ->translateLabel()
                        //                            ->money(divideBy: 100),
                        Sum::make()
                            ->translateLabel()
                            ->money(divideBy: 100),
                    ]),

                Tables\Columns\TextColumn::make('branch.name')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('order_items_count')
                    ->translateLabel()
                    ->counts('orderItems')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('claim_type')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->default(new HtmlString('&mdash;'))
                    ->summarize($countSummarize('claim_type', ClaimType::cases())),

                Tables\Columns\TextColumn::make('payment_method')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->default(new HtmlString('&mdash;'))
                    ->summarize($countSummarize('payment_method', PaymentMethod::cases())),

                Tables\Columns\TextColumn::make('payment_status')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->summarize($countSummarize('payment_status', PaymentStatus::cases())),

                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->summarize($countSummarize('status', Status::cases())),

                Tables\Columns\TextColumn::make('admin.name')
                    ->translateLabel()
                    ->visibleOn([
                        ListOrders::class,
                        CustomerOrdersRelationManager::class,
                    ])
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('claim_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime()
                    ->summarize(
                        Range::make()->minimalDateTimeDifference()
                            ->translateLabel()
                    ),

                Tables\Columns\TextColumn::make('deleted_at')
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

                Tables\Filters\SelectFilter::make('claim_type')
                    ->translateLabel()
                    ->options(ClaimType::class),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->translateLabel()
                    ->options(PaymentMethod::class),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->translateLabel()
                    ->options(PaymentStatus::class),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->translateLabel(),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),

                ]),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->translateLabel()
                    ->exporter(OrderExporter::class)
                    ->authorize('exportAny')
                    ->withActivityLog(),
            ])
            ->deferFilters()
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'branch.name',
                'customer.email',
                'payment_method',
                'payment_status',
                'status',
                Tables\Grouping\Group::make('created_at')
                    ->collapsible()
                    ->date(),
                Tables\Grouping\Group::make('updated_at')
                    ->collapsible()
                    ->date(),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            OrderInvoicesRelationManager::class,
            ActivitiesRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'create' => OrderResource\Pages\CreateOrder::route('/create'),
            'view' => OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        return trans('There are new pending orders.');
    }

    #[\Override]
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    #[\Override]
    public static function getNavigationBadge(): ?string
    {
        $count = Order::whereStatus(Status::pending)->count();

        if (0 === $count) {
            return null;
        }

        return (string) $count;
    }

    #[\Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['receipt_number', 'customer.first_name', 'customer.last_name'];
    }

    #[\Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Shop\Order\Models\Order $record */

        return [
            'Customer' => $record->customer->full_name,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Order\Models\Order> */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
