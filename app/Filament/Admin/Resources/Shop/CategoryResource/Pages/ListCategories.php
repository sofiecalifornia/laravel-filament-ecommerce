<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CategoryResource\Pages;

use App\Filament\Admin\Resources\Shop\CategoryResource;
use Domain\Shop\Category\Exports\CategoryExporter;
use Domain\Shop\Category\Imports\CategoryImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->translateLabel()
                ->importer(CategoryImporter::class)
                ->authorize('import')
                ->withActivityLog(),
            Actions\ExportAction::make()
                ->translateLabel()
                ->exporter(CategoryExporter::class)
                ->authorize('exportAny')
                ->withActivityLog(),
            Actions\CreateAction::make()
                ->translateLabel(),
        ];
    }
}
