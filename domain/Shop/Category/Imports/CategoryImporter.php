<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Imports;

use App\Jobs\QueueJobPriority;
use Domain\Shop\Category\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CategoryImporter extends Importer
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
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->exampleHeader('Name')
                ->example('Category A'),

            ImportColumn::make('parent')
                ->relationship(resolveUsing: 'name')
                ->rules(['nullable', 'max:255'])
                ->exampleHeader('Parent Name')
                ->example('Category B'),

            ImportColumn::make('description')
                ->rules(['nullable', 'max:255', 'string'])
                ->exampleHeader('Description')
                ->example('This is the description for Category A.'),

            ImportColumn::make('is_visible')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean'])
                ->exampleHeader('Visible')
                ->example('yes'),
        ];
    }

    #[\Override]
    public function resolveRecord(): ?Category
    {
        return Category::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    #[\Override]
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your category import has completed and '.
            number_format($import->successful_rows).' '.Str::of('row')
                ->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.
                Str::of('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
