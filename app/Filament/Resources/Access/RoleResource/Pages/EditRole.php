<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\RoleResource\Pages;

use App\Filament\Resources\Access\RoleResource;
use Domain\Access\Role\Actions\EditRoleAction;
use Domain\Access\Role\DataTransferObjects\RoleData;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
        ];
    }

    /** @throws Throwable */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var \Domain\Access\Role\Models\Role $record */
        return DB::transaction(
            fn () => app(EditRoleAction::class)
                ->execute($record, new RoleData(...$data))
        );
    }
}
