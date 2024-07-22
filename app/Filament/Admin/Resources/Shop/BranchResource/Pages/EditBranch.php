<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\BranchResource\Pages;

use App\Filament\Admin\Resources\Shop\BranchResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

/**
 * @property-read \Domain\Shop\Branch\Models\Branch $record
 */
class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('panel_dashboard')
                ->translateLabel()
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(
                    route('filament.branch.pages.main-dashboard', $this->record),
                    shouldOpenInNewTab: true
                )
                ->visible(
                    fn (): bool => Filament::auth()->user()
                        ?->canAccessTenant($this->record) ?? false
                ),
            Actions\DeleteAction::make()
                ->translateLabel(),
            Actions\RestoreAction::make()
                ->translateLabel(),
            Actions\ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }
}
