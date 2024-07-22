<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Customer\Models\Address
 *
 * @property string $uuid
 * @property string $model_type
 * @property string $model_id
 * @property string|null $country
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address query()
 *
 * @mixin \Eloquent
 */
class Address extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'country',
        'street',
        'city',
        'state',
        'zip',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
