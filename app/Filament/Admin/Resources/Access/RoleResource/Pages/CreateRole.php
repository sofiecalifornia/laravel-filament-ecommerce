<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\RoleResource\Pages;

use App\Filament\Admin\Resources\Access\RoleResource;
use Domain\Access\Role\Actions\CreateRoleAction;
use Domain\Access\Role\DataTransferObjects\RoleData;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateRoleAction::class)
            ->execute(new RoleData(...$data));
    }
}
