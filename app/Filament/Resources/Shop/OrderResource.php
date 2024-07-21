<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Shop\OrderResource\Schema\OrderSchema;
use Domain\Shop\Order\Actions\PrintOrderAction;
use Domain\Shop\Order\Enums\PaymentMethod;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'receipt_number';

    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    public static function form(Form $form, bool $withCustomer = true, bool $withAdmin = true): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(OrderSchema::schemaDetails($withCustomer))
                            ->columns(2),

                        Forms\Components\Section::make(OrderSchema::schemaItems()),
                    ])
                    ->columnSpan(['lg' => 3]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make([OrderSchema::total()]),

                        Forms\Components\Section::make([
                            Forms\Components\Placeholder::make('created_at')
                                ->translateLabel()
                                ->content(fn (Order $record): ?string => $record->created_at?->diffForHumans()),

                            Forms\Components\Placeholder::make('updated_at')
                                ->translateLabel()
                                ->content(fn (Order $record): ?string => $record->updated_at?->diffForHumans()),
                        ]),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(4);
    }

    /** @throws Exception */
    public static function table(Table $table, bool $withCustomer = true, bool $withAdmin = true): Table
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
                    ->hidden(! $withCustomer)
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
                        Range::make()
                            ->translateLabel()
                            ->money(divideBy: 100),
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
                    ->hidden(! $withAdmin)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

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

                Tables\Filters\SelectFilter::make('payment_method')
                    ->translateLabel()
                    ->optionsFromEnum(PaymentMethod::class),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->translateLabel()
                    ->optionsFromEnum(PaymentStatus::class),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->translateLabel(),
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('updatePaymentMethod')
                        ->translateLabel()
                        ->modalDescription(fn (Order $record) => trans('Order :order', ['order' => $record->receipt_number]))
                        ->modalSubmitActionLabel(fn (Order $record) => trans('Update :order', ['order' => $record->receipt_number]))
                        ->icon('heroicon-o-pencil')
                        ->form(fn (Order $record) => [
                            Select::make('payment_method')
                                ->translateLabel()
                                ->default($record->payment_method)
                                ->optionsFromEnum(PaymentMethod::class)
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data): void {

                            $record->update(['payment_method' => $data['payment_method']]);

                            Notification::make()
                                ->title(trans(':value payment method updated successfully!', ['value' => $record->receipt_number]))
                                ->success()
                                ->send();

                        })
                        ->authorize('updatePaymentMethod'),

                    Tables\Actions\Action::make('updatePaymentStatus')
                        ->translateLabel()
                        ->modalDescription(fn (Order $record) => trans('Order :order', ['order' => $record->receipt_number]))
                        ->modalSubmitActionLabel(fn (Order $record) => trans('Update :order', ['order' => $record->receipt_number]))
                        ->icon('heroicon-o-pencil')
                        ->form(fn (Order $record) => [
                            Select::make('payment_status')
                                ->translateLabel()
                                ->default($record->payment_status)
                                ->optionsFromEnum(PaymentStatus::class)
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data): void {
                            $record->update(['payment_status' => $data['payment_status']]);

                            Notification::make()
                                ->title(trans(':value payment status updated successfully!', ['value' => $record->receipt_number]))
                                ->success()
                                ->send();

                        })
                        ->authorize('updatePaymentStatus'),

                    Tables\Actions\Action::make('updateStatus')
                        ->translateLabel()
                        ->modalDescription(fn (Order $record) => trans('Order :order', ['order' => $record->receipt_number]))
                        ->modalSubmitActionLabel(fn (Order $record) => trans('Update :order', ['order' => $record->receipt_number]))
                        ->icon('heroicon-o-pencil')
                        ->form(fn (Order $record) => [
                            Select::make('status')
                                ->translateLabel()
                                ->default($record->status)
                                ->optionsFromEnum(Status::class)
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data): void {
                            $record->update(['status' => $data['status']]);

                            Notification::make()
                                ->title(trans(':value status updated successfully!', ['value' => $record->receipt_number]))
                                ->success()
                                ->send();

                        })
                        ->authorize('updateStatus'),

                    Tables\Actions\Action::make('print')
                        ->icon('heroicon-s-printer')
                        ->color('success')
                        ->action(
                            function (Order $record) {
                                $download = app(PrintOrderAction::class)
                                    ->execute($record)
                                    ->download();

                                Notification::make()
                                    ->title(trans('Export are ready to download!'))
                                    ->success()
                                    ->send();

                                return $download;
                            }
                        )
                        ->authorize('print'),
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->translateLabel()
                    ->exports([
                        ExcelExport::make()
                            ->askForFilename()
                            ->askForWriterType()
                            ->queue()->withChunkSize(100)
                            ->modifyQueryUsing(
                                fn (Builder $query) =>
                                /** @var Builder|\Domain\Shop\Order\Models\Order $query */
                                $query->with('customer')
                                    ->latest()
                            )
                            ->withColumns([
                                Column::make('receipt_number'),
                                Column::make('customer.full_name')
                                    ->heading(trans('Customer')),
                                Column::make('total_price')
                                    ->formatStateUsing(
                                        fn (Order $record): string => money($record->total_price * 100)->format()
                                    ),
                                Column::make('payment_method'),
                                Column::make('payment_status'),
                                Column::make('status'),
                                Column::make('created_at')
                                    ->formatStateUsing(
                                        fn (Order $record) => $record->created_at
                                            // TODO: timezone on export
//                                            ?->setTimezone(
//                                                Filament::auth()->user()->timezone
//                                            )
                                            ?->format(Table::$defaultDateTimeDisplayFormat)
                                    ),
                            ]),
                    ])
                    ->authorize('exportAny'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'branch.name',
                'customer.email',
                'payment_method',
                'payment_status',
                'status',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'create' => OrderResource\Pages\CreateOrder::route('/create'),
            'view' => OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Order::whereStatus(Status::PENDING)->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['receipt_number', 'customer.first_name', 'customer.last_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Shop\Order\Models\Order $record */

        return [
            'Customer' => $record->customer->full_name,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Order\Models\Order> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
