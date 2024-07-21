<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\BranchResource\Pages;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Branch\Models\Branch;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('status')
                            ->translateLabel()
                            ->optionsFromEnum(Status::class)
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->collection('image')
                            ->disk(config('media-library.disk_name'))
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5),

                        SpatieMediaLibraryFileUpload::make('panel')
                            ->translateLabel()
                            ->collection('panel')
                            ->disk(config('media-library.disk_name')),

                    ])->columnSpan(['lg' => fn (?Branch $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (Branch $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (Branch $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),

            ])
            ->columns(3);
    }

    /** @throws Exception */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                SpatieMediaLibraryImageColumn::make('panel_image')
                    ->translateLabel()
                    ->collection('panel')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->translateLabel()
                    ->optionsFromEnum(Status::class),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->actions([
                Action::make('panel_dashboard')
                    ->translateLabel()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(
                        fn (Branch $record): string => route('filament.branch.pages.dashboard', $record),
                        shouldOpenInNewTab: true
                    )
                    ->visible(
                        fn (Branch $record): bool => Filament::auth()
                            ->user()?->branches->contains($record) ?? false
                    ),

                Tables\Actions\EditAction::make()
                    ->translateLabel(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->translateLabel(),
                    Tables\Actions\RestoreAction::make()
                        ->translateLabel(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('api_link')
                    ->label(trans('API link'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (): string => route('api.branches.index', [
                        'include' => implode(',', [
                            'media',
                        ]),
                    ]), shouldOpenInNewTab: true),
            ])
            ->defaultSort(config('eloquent-sortable.order_column_name'))
            ->reorderable(config('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                'status',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\Domain\Shop\Branch\Models\Branch> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
