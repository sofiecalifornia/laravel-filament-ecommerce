<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\RoleResource\Pages;

use App\Filament\Resources\Access\RoleResource;
use Domain\Access\Role\Actions\CreateRoleAction;
use Domain\Access\Role\DataTransferObjects\RoleData;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /** @throws Throwable */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(
            fn () => app(CreateRoleAction::class)
                ->execute(new RoleData(...$data))
        );
    }
}
