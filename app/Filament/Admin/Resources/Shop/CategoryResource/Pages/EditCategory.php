<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CategoryResource\Pages;

use App\Filament\Admin\Resources\Shop\CategoryResource;
use Domain\Shop\Category\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property-read Category $record
 */
class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
            Actions\RestoreAction::make()
                ->translateLabel(),
            Actions\ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }

    public function beforeFill(): void
    {
        $this->record->loadCount([
            'children',
            'products' => function (Builder $builder) {
                /** @var \Domain\Shop\Product\Models\Product|\Illuminate\Database\Eloquent\Builder $builder */
                $builder->withTrashed();
            },
        ]);
    }
}
