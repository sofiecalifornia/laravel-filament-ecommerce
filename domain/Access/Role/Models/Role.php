<?php

declare(strict_types=1);

namespace Domain\Access\Role\Models;

use App\Helpers;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Access\Role\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
