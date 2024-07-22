<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Exports;

use App\Jobs\QueueJobPriority;
use Domain\Shop\Category\Models\Category;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class CategoryExporter extends Exporter
{
    protected static ?string $model = Category::class;

    #[\Override]
    public function getJobQueue(): ?string
    {
        return QueueJobPriority::EXCEL;
    }

    #[\Override]
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('parent.name'),
            ExportColumn::make('description')
                ->state(fn (Category $record) => Str::of($record->description ?? '')->stripTags()),
            ExportColumn::make('is_visible')
                ->state(fn (Category $record) => $record->is_visible ? 'Yes' : 'No'),
        ];
    }

    #[\Override]
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your category export has completed and '.
            number_format($export->successful_rows).' '.
            Str::of('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).
                ' '.Str::of('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
