<?php

declare(strict_types=1);

namespace Database\Seeders\Auth;

use Domain\Access\Role\Contracts\HasPermissionPage;
use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\Support;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class PermissionSeeder extends \Domain\Access\Role\Database\Seeders\PermissionSeeder
{
    /** @throws Exception */
    protected function permissionsByGuard(): array
    {
        return [
            'admin' => $this->getPermissionsFromResourceModelPolicies()
                ->merge($this->getPermissionsFromWidgets())
                ->merge($this->getPermissionsFromPages())
                ->toArray(),
        ];
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromResourceModelPolicies(): Collection
    {
        $permissionsByPolicy = collect();

        foreach (Filament::getResources() as $filamentResource) {

            $modelPolicy = Gate::getPolicyFor($filamentResource::getModel());

            $permissionsByPolicy = $permissionsByPolicy->merge(
                self::generateFilamentResourcePermissions($modelPolicy::class)
            );
        }

        return $permissionsByPolicy;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromWidgets(): Collection
    {
        $permissionNames = collect();

        foreach (Filament::getWidgets() as $widget) {
            if (app($widget) instanceof HasPermissionWidgets) {
                $permissionNames->push(Support::getWidgetPermissionName($widget));
            }
        }

        if ($permissionNames->isEmpty()) {
            return $permissionNames;
        }

        $permissionNames->prepend(Support::WIDGETS);

        return $permissionNames;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromPages(): Collection
    {
        $permissionNames = collect();

        foreach (Filament::getPages() as $page) {
            if (app($page) instanceof HasPermissionPage && $page::canBeSeed()) {
                $permissionNames->push(Support::getPagePermissionName($page));
            }
        }

        if ($permissionNames->isEmpty()) {
            return $permissionNames;
        }

        $permissionNames->prepend(Support::PAGES);

        return $permissionNames;
    }
}
