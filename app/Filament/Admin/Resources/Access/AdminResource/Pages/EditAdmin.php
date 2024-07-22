<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\AdminResource\Pages;

use App\Filament\Admin\Resources\Access\AdminResource;
use App\Filament\Admin\Resources\Access\AdminResource\Pages\Actions\ChangePasswordAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @property-read \Domain\Access\Admin\Models\Admin $record
 */
class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            ChangePasswordAction::make(),
            Actions\DeleteAction::make()
                ->translateLabel(),
            Actions\RestoreAction::make()
                ->translateLabel(),
            Actions\ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }
}
