<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access;

use App\Filament\Resources\Access\ActivityResource\Pages\ListActivities;
use Carbon\Carbon;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use ErrorException;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    /** @throws \Spatie\Activitylog\Exceptions\InvalidConfiguration */
    public static function getModel(): string
    {
        return ActivitylogServiceProvider::determineActivityModel();
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->translateLabel()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('causer')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->formatStateUsing(function (Activity $record): string {
                        if ($record->causer === null) {
                            return '--';
                        }

                        return match ($record->causer::class) {
                            Admin::class => $record->causer->name,
                            Customer::class => $record->causer->full_name,
                            default => throw new ErrorException(
                                'No matching model `'.$record->causer::class.'` for activity causer.'
                            ),
                        };
                    }),

                Forms\Components\TextInput::make('subject_type')
                    ->formatStateUsing(
                        function (?string $state): string {
                            if ($state === null) {
                                return '--';
                            }

                            return (string) Str::of(Relation::getMorphedModel($state) ?? '')
                                ->classBasename()
                                ->headline();
                        }
                    )
                    ->translateLabel(),

                Forms\Components\TextInput::make('subject_id')
                    ->translateLabel(),

                Forms\Components\TextInput::make('event')
                    ->translateLabel(),

                Forms\Components\TextInput::make('description')
                    ->translateLabel(),
                Forms\Components\KeyValue::make('properties.old')
                    ->translateLabel(),

                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('New')
                    ->translateLabel(),
            ])
            ->columns(2);
    }

    /** @throws Exception */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->translateLabel(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->translateLabel()
                    ->multiple()
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->translateLabel()
                    ->form([
                        Forms\Components\DatePicker::make('logged_from')
                            ->translateLabel(),
                        Forms\Components\DatePicker::make('logged_until')
                            ->translateLabel(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['logged_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['logged_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        ))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['logged_from'] ?? null) {
                            $indicators['logged_from'] = 'Created from '.Carbon::parse($data['logged_from'])->toFormattedDateString();
                        }

                        if ($data['logged_until'] ?? null) {
                            $indicators['logged_until'] = 'Created until '.Carbon::parse($data['logged_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups(['log_name', 'event']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
