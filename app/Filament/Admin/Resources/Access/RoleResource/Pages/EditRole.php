<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\RoleResource\Pages;

use App\Filament\Admin\Resources\Access\RoleResource;
use Domain\Access\Role\Actions\EditRoleAction;
use Domain\Access\Role\DataTransferObjects\RoleData;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
        ];
    }

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var \Domain\Access\Role\Models\Role $record */
        return app(EditRoleAction::class)
            ->execute($record, new RoleData(...$data));
    }
}
