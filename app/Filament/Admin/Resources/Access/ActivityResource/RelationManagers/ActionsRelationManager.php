<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers;

use App\Filament\Admin\Resources\Access\ActivityResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    protected static ?string $recordTitleAttribute = 'id';

    #[\Override]
    public function form(Form $form): Form
    {
        return ActivityResource::form($form);
    }

    #[\Override]
    public function table(Table $table): Table
    {
        return (new ActivitiesRelationManager())->table($table);
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
