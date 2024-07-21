<?php

declare(strict_types=1);

namespace Domain\Access\Role\Models;

/**
 * Domain\Access\Role\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Permission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Permission extends \Spatie\Permission\Models\Permission
{
}
