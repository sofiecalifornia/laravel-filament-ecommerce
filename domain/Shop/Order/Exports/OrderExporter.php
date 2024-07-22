<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Exports;

use App\Jobs\QueueJobPriority;
use Domain\Shop\Order\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    #[\Override]
    public function getJobQueue(): ?string
    {
        return QueueJobPriority::EXCEL;
    }

    #[\Override]
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('receipt_number'),

            ExportColumn::make('branch.name'),

            ExportColumn::make('customer')
                ->state(
                    fn (Order $record): string => $record->customer->full_name
                ),

            ExportColumn::make('total_price')
                ->state(
                    fn (Order $record): string => $record->total_price->format()
                ),

            ExportColumn::make('payment_method')
                ->state(fn (Order $record) => $record->payment_method?->getLabel() ?? '--'),

            ExportColumn::make('payment_status')
                ->state(fn (Order $record) => $record->payment_status->getLabel()),

            ExportColumn::make('status')
                ->state(fn (Order $record) => $record->status->getLabel()),

            ExportColumn::make('created_at')
                ->state(
                    fn (Order $record) => $record->created_at
                        // TODO: timezone on export
//                                            ?->setTimezone(
//                                                Filament::auth()->user()->timezone
//                                            )
                        ?->format(Table::$defaultDateTimeDisplayFormat)
                ),
        ];
    }

    #[\Override]
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and '.number_format($export->successful_rows).
            ' '.Str::of('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).
                ' '.Str::of('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
