<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Customer\Models\Address
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $country
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Address whereZip($value)
 *
 * @mixin \Eloquent
 */
class Address extends Model
{
    use LogsActivity;

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
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
