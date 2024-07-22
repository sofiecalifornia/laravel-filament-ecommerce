<?php

declare(strict_types=1);

namespace Domain\Access\Role\Models;

use Domain\Access\Role\Observers\RoleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Access\Role\Models\Role
 *
 * @property string $uuid
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Access\Role\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Access\Admin\Models\Admin[] $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Role\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Role withoutPermission($permissions)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(RoleObserver::class)]
class Role extends \Spatie\Permission\Models\Role
{
    use HasUuids;
    use LogsActivity;

    protected $primaryKey = 'uuid';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
