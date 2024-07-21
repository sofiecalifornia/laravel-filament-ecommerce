<?php

declare(strict_types=1);

namespace App\Filament\Resources\Access\RoleResource\Schema;

use App\Filament\Resources\Access\RoleResource\Support\PermissionData;
use Domain\Access\Role\Models\Permission;
use Domain\Access\Role\Models\Role;
use Exception;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection as CollectionSupport;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

final class PermissionSchema
{
    private static string $guardName;

    public static function schema(?string $guardName): array
    {
        if ($guardName === null) {
            return [
                Forms\Components\Placeholder::make(trans('Select guard name first before selecting permissions')),
            ];
        }

        self::$guardName = $guardName;
        unset($guardName);

        if (self::permissionGroup()->isEmpty()) {
            return [
                Forms\Components\Placeholder::make(trans('No Permission on guard name :value', ['value' => self::$guardName])),
            ];
        }

        return [

            Forms\Components\Hidden::make('permissions')
                ->afterStateHydrated(function (Forms\Components\Hidden $component, ?Role $record): void {
                    $component->state($record ? $record->permissions->pluck('id') : []);
                })
                ->dehydrateStateUsing(
                    fn (Get $get): array => self::permissionGroup()
                        ->reduce(
                            function (
                                CollectionSupport $result,
                                CollectionSupport $permissionGroup,
                                string $parentPermission
                            ) use ($get): CollectionSupport {

                                if ($get($parentPermission) ?? false) {
                                    $result->push($permissionGroup->firstWhere('name', $parentPermission)?->id ?? throw new Exception('Can not user id of null.'));
                                } elseif (filled($ids = $get("{$parentPermission}_abilities"))) {
                                    $result = $result->merge($ids);
                                }

                                return $result;
                            },
                            collect()
                        )
                        ->toArray()
                ),

            Forms\Components\Toggle::make('select_all')
                ->translateLabel()
                ->onIcon('heroicon-s-shield-check')
                ->offIcon('heroicon-s-shield-exclamation')
                ->helperText(trans('Enable all Permissions for this role'))
                ->reactive()
                ->afterStateUpdated(function (Get $get, Set $set, bool $state): void {
                    self::updatedToggleSelectAllState(get: $get, set: $set, state: $state);
                })
                ->afterStateHydrated(function (Forms\Components\Toggle $component, ?Role $record): void {

                    if ($record === null) {
                        $component->state(false);

                        return;
                    }

                    $all = true;

                    foreach (self::permissions() as $permissionData) {
                        if (! $record->hasPermissionTo($permissionData->name)) {
                            $all = false;

                            break;
                        }
                    }

                    $component->state($all);

                })
                ->dehydrated(false),

            Forms\Components\Grid::make(['sm' => 2])
                ->schema(
                    /////////////////////////////////////////////////////////////////////////////////////////// start
                    self::permissionGroup()
                        ->map(
                            fn (CollectionSupport $permissionsDatas, string $parentPermission): Forms\Components\Section => Forms\Components\Section::make()
                                ->schema([

                                    Forms\Components\Toggle::make($parentPermission)
                                        ->onIcon('heroicon-s-lock-open')
                                        ->offIcon('heroicon-s-lock-closed')
                                        ->reactive()
                                        ->afterStateHydrated(function (Forms\Components\Toggle $component, ?Role $record) use (
                                            $parentPermission
                                        ): void {
                                            if ($record === null) {
                                                $component->state(false);

                                                return;
                                            }
                                            $component->state($record->hasPermissionTo($parentPermission));
                                        })
                                        ->afterStateUpdated(
                                            function (Set $set, Get $get, bool $state) use (
                                                $parentPermission,
                                                $permissionsDatas
                                            ): void {
                                                self::updatedToggleSelectParentPermissionState(
                                                    parentPermission: $parentPermission,
                                                    permissionsDatas: $permissionsDatas,
                                                    get: $get,
                                                    set: $set,
                                                    state: $state,
                                                );
                                            }
                                        )
                                        ->dehydrated(false),

                                    Forms\Components\Fieldset::make('Abilities')
                                        ->translateLabel()
                                        ->schema([
                                            /////// start CheckboxList
                                            Forms\Components\CheckboxList::make("{$parentPermission}_abilities")
                                                ->hiddenLabel()
                                                ->options(
                                                    self::parentAbilitiesWithIdAndLabels(
                                                        permissionsDatas: $permissionsDatas
                                                    )
                                                        ->sort()
                                                        ->toArray()
                                                )
                                                ->columns(2)
                                                ->reactive()
                                                ->afterStateHydrated(function (Forms\Components\CheckboxList $component, ?Role $record) use (
                                                    $permissionsDatas,
                                                    $parentPermission
                                                ): void {

                                                    if ($record === null) {
                                                        $component->state([]);

                                                        return;
                                                    }

                                                    if ($record->hasPermissionTo($parentPermission)) {

                                                        $component->state($permissionsDatas->pluck('id')->toArray());

                                                        return;
                                                    }

                                                    $ids = [];

                                                    foreach (self::permissionsExceptParent($permissionsDatas) as $permissionData) {
                                                        if ($record->hasPermissionTo($permissionData->name)) {
                                                            $ids[] = $permissionData->id;
                                                        }
                                                    }

                                                    $component->state($ids);

                                                })
                                                ->afterStateUpdated(function (Set $set, Get $get, CollectionSupport|array $state) use (
                                                    $parentPermission,
                                                    $permissionsDatas
                                                ): void {
                                                    self::updatedCheckboxListPermissionState(
                                                        parentPermission: $parentPermission,
                                                        permissionDatas: $permissionsDatas,
                                                        set: $set,
                                                        get: $get,
                                                        state: $state
                                                    );
                                                })
                                                ->dehydrated(false),
                                            /////// end CheckboxList
                                        ])
                                        ->columnSpan(1),

                                ])
                                ->columnSpan(1)
                        )
                        ->toArray(),
                    /////////////////////////////////////////////////////////////////////////////////////////// end
                ),
        ];
    }

    /** @return CollectionSupport<int, PermissionData> */
    private static function permissions(): CollectionSupport
    {
        return once(
            fn () => app(PermissionRegistrar::class)
                ->getPermissions(['guard_name' => self::$guardName])
                ->sortBy('name')
                ->map(fn (Permission $permission): PermissionData => new PermissionData($permission->id, $permission->name))
        );
    }

    /** @return CollectionSupport<string, CollectionSupport<int, PermissionData>> */
    private static function permissionGroup(): CollectionSupport
    {
        return once(
            fn () => self::permissions()
                ->groupBy(
                    fn (PermissionData $permissionData): string => $permissionData->parent_name
                )
        );
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     * @return CollectionSupport<int, string>
     */
    private static function parentAbilitiesWithIdAndLabels(CollectionSupport $permissionsDatas): CollectionSupport
    {
        return self::permissionsExceptParent($permissionsDatas)
            ->mapWithKeys(fn (PermissionData $permissionData) => [
                $permissionData->id => Str::headline($permissionData->child_name ?? throw new Exception('Can not use $permissionData->child_name when null return.')),
            ]);
    }

    /** @param  CollectionSupport<int, PermissionData>  $permissionDatas */

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionDatas
     * @param  CollectionSupport<int, int>|array<int, int>  $state
     */
    private static function updatedCheckboxListPermissionState(
        string $parentPermission,
        CollectionSupport $permissionDatas,
        Set $set,
        Get $get,
        CollectionSupport|array $state
    ): void {
        $set($parentPermission, self::permissionsExceptParent($permissionDatas)->count() === count($state));

        self::refreshToggleSelectAllState(get: $get, set: $set);
    }

    /** @param  CollectionSupport<int, PermissionData>  $permissionsDatas */
    private static function updatedToggleSelectParentPermissionState(
        string $parentPermission,
        CollectionSupport $permissionsDatas,
        Get $get,
        Set $set,
        bool $state
    ): void {
        $set($parentPermission.'_abilities', $get($parentPermission)
            ? self::parentAbilitiesWithIdAndLabels($permissionsDatas)->keys()
            : []);

        self::refreshToggleSelectAllState(get: $get, set: $set);
    }

    private static function updatedToggleSelectAllState(Get $get, Set $set, bool $state): void
    {
        foreach (self::permissionGroup() as $parentPermissionName => $permissionDatas) {
            $set($parentPermissionName, $state);
            $set(
                "{$parentPermissionName}_abilities",
                $state ? self::permissionsExceptParent($permissionDatas)
                    ->pluck('id')
                    : []
            );
        }
    }

    private static function refreshToggleSelectAllState(Get $get, Set $set): void
    {
        $selectAll = true;

        foreach (self::permissionGroup() as $parentPermission => $permissionAbilities) {
            if ($get($parentPermission) === false) {
                $selectAll = false;

                break;
            }
        }

        $set('select_all', $selectAll);
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionDatas
     * @return CollectionSupport<int, PermissionData>
     */
    private static function permissionsExceptParent(CollectionSupport $permissionDatas): CollectionSupport
    {
        return $permissionDatas->reject(fn (PermissionData $permissionData) => $permissionData->is_parent);
    }
}
