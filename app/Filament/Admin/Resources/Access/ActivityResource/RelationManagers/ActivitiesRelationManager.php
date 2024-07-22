<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers;

use App\Filament\Admin\Resources\Access\ActivityResource;
use Exception;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = 'description';

    #[\Override]
    public function infolist(Infolist $infolist): Infolist
    {
        return ActivityResource::infolist($infolist);
    }

    //    public function form(Form $form): Form
    //    {
    //        return ActivityResource::form($form);
    //    }

    /** @throws Exception */
    #[\Override]
    public function table(Table $table): Table
    {
        return ActivityResource::table($table);
    }

    #[\Override]
    protected function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    protected function canEdit(Model $record): bool
    {
        return false;
    }

    #[\Override]
    protected function canDelete(Model $record): bool
    {
        return false;
    }
}
