<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use ErrorException;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    /** @throws \Spatie\Activitylog\Exceptions\InvalidConfiguration */
    #[\Override]
    public static function getModel(): string
    {
        return ActivitylogServiceProvider::determineActivityModel();
    }

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[\Override]
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Infolists\Components\TextEntry::make('description')
                    ->translateLabel()
                    ->columnSpanFull(),

                Infolists\Components\TextEntry::make('subject')
                    ->translateLabel()
                    ->state(
                        function (Activity $record): ?string {
                            if (null === $record->subject) {
                                return null;
                            }

                            /** @var \Filament\Resources\Resource|null $resource */
                            $resource = collect(Filament::getResources())
                                ->first(fn (mixed $resource) => $record->subject::class === $resource::getModel());

                            return $resource
                                ? Str::headline($resource::getModelLabel())
                                : (string) Str::of($record->subject::class)->classBasename()->headline();
                        }
                    )
                    ->url(
                        function (Activity $record): ?string {
                            if (null === $record->subject) {
                                return null;
                            }

                            /** @var \Filament\Resources\Resource|null $resource */
                            $resource = collect(Filament::getResources())
                                ->first(fn (mixed $resource) => $record->subject::class === $resource::getModel());

                            if (null === $resource) {
                                return null;
                            }
                            try {
                                if ($resource::hasPage('view') && $resource::canView($record)) {
                                    return $resource::getUrl('view', ['record' => $record->subject]);
                                }
                                if ($resource::hasPage('edit') && $resource::canEdit($record)) {
                                    return $resource::getUrl('edit', ['record' => $record->subject]);
                                }
                            } catch (UrlGenerationException) {
                            }

                            return null;
                        },
                        shouldOpenInNewTab: true
                    )
                    ->placeholder('--'),

                Infolists\Components\TextEntry::make('causer')
                    ->translateLabel()
                    ->state(function (Activity $record): ?string {
                        if (null === $record->causer) {
                            return null;
                        }

                        return match ($record->causer::class) {
                            Admin::class => trans('Admin: :admin', ['admin' => $record->causer->name]),
                            Customer::class => trans('Customer: :customer', [
                                'customer' => $record->causer->full_name,
                            ]),
                            default => throw new ErrorException(
                                'No matching model `'.$record->causer::class.'` for activity causer.'
                            ),
                        };
                    })
                    ->url(function (Activity $record): ?string {
                        if (null === $record->causer) {
                            return null;
                        }

                        return match ($record->causer::class) {
                            Admin::class => AdminResource::canEdit($record->causer)
                                ? AdminResource::getUrl('edit', [$record->causer])
                                : null,
                            Customer::class => CustomerResource::canEdit($record->causer)
                                ? CustomerResource::getUrl('edit', [$record->causer])
                                : null,
                            default => throw new ErrorException(
                                'No matching model `'.$record->causer::class.'` for activity causer.'
                            ),
                        };
                    },
                        shouldOpenInNewTab: true
                    )
                    ->placeholder('--'),

                Infolists\Components\TextEntry::make('created_at')
                    ->label('Logged at')
                    ->translateLabel()
                    ->dateTime(),

                Infolists\Components\Section::make()
                    ->description(trans('Properties'))
                    ->visible(
                        fn (Activity $record): bool => $record
                            ->properties
                            ?->except('old', 'attributes')
                            ->isNotEmpty() ?? false
                    )
                    ->schema([

                        Infolists\Components\KeyValueEntry::make('properties')
                            ->hiddenLabel()
                            ->inlineLabel(false)
                            ->state(fn (Activity $record): ?Collection => $record
                                ->properties
                                ?->except('old', 'attributes')
                            ),

                        //                        Infolists\Components\RepeatableEntry::make('data')
                        //                            ->hiddenLabel()
                        //                            ->state(
                        //                                fn (Activity $record): ?Collection => $record
                        //                                    ->properties
                        //                                    ?->except('old', 'attributes')
                        //                            )
                        //                            ->schema(
                        //                                fn (?Collection $state): array => $state
                        //                                    ?->map(
                        //                                        fn (string $value, string $property): Infolists\Components\TextEntry => Infolists\Components\TextEntry::make($property)
                        //                                            ->color('primary')
                        //                                            ->state($value)
                        //                                            ->inlineLabel()
                        //                                    )
                        //                                    ->toArray() ?? []
                        //                            )
                        //                            ->contained(false),

                    ]),

                Infolists\Components\Section::make()
                    ->description(trans('Changes'))
                    ->visible(
                        fn (Activity $record): bool => $record
                            ->properties
                            ?->hasAny('old', 'attributes') ?? false
                    )
                    ->schema([

                        Infolists\Components\KeyValueEntry::make('old')
                            ->translateLabel()
                            ->inlineLabel(false)
                            ->state(self::changes('old')),

                        Infolists\Components\KeyValueEntry::make('new')
                            ->translateLabel()
                            ->inlineLabel(false)
                            ->state(self::changes('attributes')),
                    ]),

                Infolists\Components\Fieldset::make('others')
                    ->hiddenLabel()
                    ->schema([
                        Infolists\Components\TextEntry::make('event')
                            ->translateLabel()
                            ->badge()
                            ->placeholder('--'),

                        Infolists\Components\TextEntry::make('log_name')
                            ->translateLabel()
                            ->badge(),

                        Infolists\Components\TextEntry::make('batch_uuid')
                            ->translateLabel()
                            ->placeholder('--'),
                    ]),

            ])
            ->columns(1)
            ->inlineLabel();
    }

    private static function changes(string $type): \Closure
    {
        return function (Activity $record) use ($type) {
            $newProperties = $record->properties
                ?->only($type)
                ->first();

            if (null === $newProperties) {
                return ['' => ''];
            }

            $return = [];

            foreach ($newProperties as $property => $value) {

                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $return[$property] = $value;
            }

            return $return;
        };
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('batch_uuid')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->wrap()
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->translateLabel(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->translateLabel()
                    ->multiple()
                    ->options(function () {
                        /** @var \Illuminate\Database\Eloquent\Model $model */
                        $model = self::getModel();

                        return $model::query()
                            ->orderBy('log_name')
                            ->distinct()
                            ->pluck('log_name')
                            ->mapWithKeys(fn (string $value) => [$value => Str::headline($value)]);
                    }),
                Tables\Filters\SelectFilter::make('event')
                    ->translateLabel()
                    ->multiple()
                    ->options(function () {
                        /** @var \Illuminate\Database\Eloquent\Model $model */
                        $model = self::getModel();

                        return $model::query()
                            ->orderBy('event')
                            ->distinct()
                            ->pluck('event')
                            ->mapWithKeys(fn (?string $value) => [$value ?? 'null' => Str::headline($value ?? 'none')]);
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->translateLabel()
                    ->form([
                        Forms\Components\DatePicker::make('logged_from')
                            ->translateLabel(),
                        Forms\Components\DatePicker::make('logged_until')
                            ->translateLabel(),
                    ])
                    ->query(
                        fn (Builder $query, array $data): Builder => $query
                            ->when(
                                $data['logged_from'],
                                fn (Builder $query, string $date): Builder => $query
                                    ->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['logged_until'],
                                fn (Builder $query, string $date): Builder => $query
                                    ->whereDate('created_at', '<=', $date),
                            )
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['logged_from'] ?? null) {
                            $indicators['logged_from'] = trans('Logged from: :date', [
                                'date' => now()->parse($data['logged_from'])->toFormattedDateString(),
                            ]);
                        }

                        if ($data['logged_until'] ?? null) {
                            $indicators['logged_until'] = trans('Logged until: :date', [
                                'date' => now()->parse($data['logged_until'])->toFormattedDateString(),
                            ]);
                        }

                        return $indicators;
                    }),
            ])
            ->deferFilters()
            ->defaultSort('created_at', 'desc')
            ->groups(['log_name', 'event']);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ActivityResource\Pages\ListActivities::route('/'),
        ];
    }
}
